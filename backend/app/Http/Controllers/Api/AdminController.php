<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Exhibitor;
use App\Models\User;
use App\Services\Audit;
use App\Services\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Users (FR-043..046), exhibitor directory, notifications, audit trail (FR-049). */
class AdminController extends Controller
{
    /* ------------------------------------------------------------------ users */

    public function users(Request $request)
    {
        $this->permit('users.manage');
        $query = User::with('exhibitor:id,organization_name');

        if ($role = $request->query('role')) {
            $query->whereIn('role', explode(',', $role));
        }
        if ($q = $request->query('q')) {
            $query->where(fn ($w) => $w->where('name', 'like', "%$q%")->orWhere('email', 'like', "%$q%"));
        }

        return $query->orderBy('name')->paginate($this->perPage());
    }

    public function storeUser(Request $request)
    {
        $this->permit('users.manage');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'role' => ['required', Rule::in(array_diff(array_keys(config('gememis.roles')), ['exhibitor']))],
        ]);
        $temporary = Str::password(14, symbols: false);
        $user = User::create(['email' => strtolower($data['email']), 'password' => $temporary, 'must_change_password' => true] + $data);
        Audit::log('user.created', "User {$user->email} created as {$user->role_label}", $user, null, ['role' => $user->role], $user->email);
        Notifier::user($user, 'account', 'Your GeMEMIS account', 'An account has been created for you. You will be asked to choose a new password when you first sign in.');

        // Shown once to the administrator, never stored in clear.
        return response()->json(['user' => $user, 'temporary_password' => $temporary], 201);
    }

    /** FR-044/045: role, details, activate/deactivate, unlock. */
    public function updateUser(Request $request, User $user)
    {
        $this->permit('users.manage');
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'role' => ['sometimes', Rule::in(array_keys(config('gememis.roles')))],
            'is_active' => 'sometimes|boolean',
            'unlock' => 'sometimes|boolean',
        ]);
        abort_if($user->is($this->actor()) && (($data['is_active'] ?? true) === false || ($data['role'] ?? $user->role) !== $user->role),
            422, 'You cannot deactivate yourself or change your own role.');
        abort_if(isset($data['role']) && ($data['role'] === 'exhibitor') !== ($user->role === 'exhibitor'), 422, 'Staff and exhibitor accounts cannot be converted into each other.');

        $user->fill(collect($data)->except('unlock')->all());
        if ($request->boolean('unlock')) {
            $user->forceFill(['locked_at' => null, 'failed_logins' => 0]);
        }
        $user->save();
        [$old, $new] = Audit::diff($user);

        if ($new) {
            Audit::log('user.updated', "User {$user->email} updated", $user, $old, $new, $user->email);
        }
        // Access ends now for a deactivated account or a changed role.
        if (array_key_exists('is_active', $new) && ! $user->is_active || array_key_exists('role', $new)) {
            $user->tokens()->delete();
        }

        return $user;
    }

    /** FR-046 */
    public function resetPassword(Request $request, User $user)
    {
        $this->permit('users.manage');
        $temporary = Str::password(14, symbols: false);
        $user->forceFill(['password' => $temporary, 'must_change_password' => true, 'locked_at' => null, 'failed_logins' => 0])->save();
        $user->tokens()->delete();
        Audit::log('user.password_reset', "Password reset for {$user->email}", $user, reference: $user->email);

        return ['temporary_password' => $temporary];
    }

    /* -------------------------------------------------------------- exhibitors */

    public function exhibitors(Request $request)
    {
        $this->permit('exhibitors.view');
        $query = Exhibitor::withCount(['applications', 'allocations as live_allocations_count' => fn ($q) => $q->whereNotNull('active_space_id')]);

        if ($q = $request->query('q')) {
            $query->where(fn ($w) => $w->where('organization_name', 'like', "%$q%")->orWhere('email', 'like', "%$q%")
                ->orWhere('contact_person', 'like', "%$q%")->orWhere('tin', 'like', "%$q%"));
        }
        foreach (['country', 'exhibitor_type', 'status', 'business_sector', 'entity_type'] as $filter) {
            if ($value = $request->query($filter)) {
                $query->whereIn($filter, explode(',', $value));
            }
        }
        if ($exhibition = $request->query('exhibition_id')) {
            $query->whereHas('applications', fn ($a) => $a->where('exhibition_id', $exhibition)->where('status', '!=', 'draft'));
        }

        return $query->orderBy('organization_name')->paginate($this->perPage());
    }

    public function exhibitor(Exhibitor $exhibitor)
    {
        $this->ownOrStaff($exhibitor->id);

        return $exhibitor->load(['users:id,name,email,phone,exhibitor_id,is_active,last_login_at',
            'applications' => fn ($q) => $q->with('exhibition:id,code,name')->withCount('activeAllocations')->latest()]);
    }

    public function updateExhibitor(Request $request, Exhibitor $exhibitor)
    {
        $this->permit('exhibitors.manage');
        $exhibitor->update($request->validate([
            'exhibitor_type' => 'sometimes|in:local,international,government,sme',
            'status' => 'sometimes|in:active,suspended',
        ]));
        [$old, $new] = Audit::diff($exhibitor);
        if ($new) {
            Audit::log('exhibitor.updated', "Exhibitor {$exhibitor->organization_name} updated", $exhibitor, $old, $new);
        }

        return $exhibitor;
    }

    /* ----------------------------------------------------------- notifications */

    public function notifications(Request $request)
    {
        return AppNotification::where('user_id', $this->actor()->id)->latest()->limit(30)->get();
    }

    public function readNotifications(Request $request)
    {
        $query = AppNotification::where('user_id', $this->actor()->id)->whereNull('read_at');
        if ($id = $request->input('id')) {
            $query->whereKey($id);
        }
        $query->update(['read_at' => now()]);

        return response()->noContent();
    }

    /* ------------------------------------------------------------------- audit */

    public function audit(Request $request)
    {
        $this->permit('audit.view');
        $query = AuditLog::query();

        if ($q = $request->query('q')) {
            $query->where(fn ($w) => $w->where('summary', 'like', "%$q%")->orWhere('reference', 'like', "%$q%")->orWhere('user_name', 'like', "%$q%"));
        }
        if ($action = $request->query('action')) {
            $query->where('action', 'like', $action.'%');
        }
        if ($user = $request->query('user_id')) {
            $query->where('user_id', $user);
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query->latest('id')->paginate($this->perPage());
    }
}
