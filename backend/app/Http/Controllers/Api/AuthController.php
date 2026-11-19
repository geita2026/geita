<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exhibitor;
use App\Models\User;
use App\Services\Audit;
use App\Services\Notifier;
use App\Support\ExhibitorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private function passwordRule(): Password
    {
        return Password::min(10)->letters()->mixedCase()->numbers();
    }

    private function session(User $user): array
    {
        return [
            'user' => $user->load('exhibitor'),
            'permissions' => $user->permissions(),
        ];
    }

    /** FR-012. Locks the account after repeated failures. */
    public function login(Request $request)
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $user = User::where('email', strtolower($data['email']))->first();
        $max = config('gememis.auth.max_failed_logins');

        if ($user?->locked_at) {
            throw ValidationException::withMessages(['email' => 'This account is locked after too many failed attempts. Ask the System Administrator to unlock it.']);
        }
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            if ($user) {
                $user->forceFill(['failed_logins' => $user->failed_logins + 1, 'locked_at' => $user->failed_logins + 1 >= $max ? now() : null])->save();
                Audit::log('auth.failed', "Failed sign-in for {$user->email}", $user, reference: $user->email);
            }
            throw ValidationException::withMessages(['email' => 'The email or password is incorrect.']);
        }
        if (! $user->is_active) {
            throw ValidationException::withMessages(['email' => 'This account has been deactivated.']);
        }

        $user->forceFill(['failed_logins' => 0, 'last_login_at' => now()])->save();
        auth()->setUser($user);
        Audit::log('auth.login', "{$user->name} signed in", $user, reference: $user->email);

        return ['token' => $user->createToken('web')->plainTextToken] + $this->session($user);
    }

    /**
     * FR-011: the person creates an account — name, phone, email, password, as
     * on SAMS. Whether they exhibit as an individual or a registered business,
     * and the organisation's details, are asked when they first apply.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', $this->passwordRule()],
            'terms' => 'accepted',
        ], ['terms.accepted' => 'Accept the terms to create an account.']);
        $data['email'] = strtolower($data['email']);
        $name = trim($data['first_name'].' '.$data['last_name']);

        $user = DB::transaction(function () use ($data, $name) {
            // A profile from day one, completed (individual or business) on the first application.
            $exhibitor = Exhibitor::create([
                'organization_name' => $name, 'contact_person' => $name, 'phone' => $data['phone'], 'email' => $data['email'],
            ]);

            $user = User::create([
                'name' => $name, 'email' => $data['email'], 'phone' => $data['phone'], 'password' => $data['password'],
                'role' => 'exhibitor', 'exhibitor_id' => $exhibitor->id,
            ]);
            $user->forceFill(['terms_accepted_at' => now()])->save();

            return $user;
        });

        auth()->setUser($user);
        Audit::log('exhibitor.registered', "$name registered", $user->exhibitor, reference: $data['email']);
        Notifier::user($user, 'account', 'Welcome to GeMEMIS',
            'Your account is ready. Open a published exhibition to apply for space — you will say there whether you exhibit as an individual or a registered business.',
            '/exhibitions');

        return response()->json(['token' => $user->createToken('web')->plainTextToken] + $this->session($user), 201);
    }

    public function me(Request $request)
    {
        return $this->session($this->actor());
    }

    public function logout(Request $request)
    {
        Audit::log('auth.logout', "{$this->actor()->name} signed out", $this->actor(), reference: $this->actor()->email);
        $this->actor()->currentAccessToken()->delete();

        return response()->noContent();
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', 'different:current_password', $this->passwordRule()],
        ]);
        $user = $this->actor();
        $user->forceFill(['password' => $data['password'], 'must_change_password' => false])->save();
        // Every other session ends now.
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();
        Audit::log('auth.password_changed', "{$user->name} changed their password", $user, reference: $user->email);

        return ['message' => 'Password changed.'];
    }

    /** FR-013: an exhibitor maintains their own profile, as an individual or a registered business. */
    public function updateProfile(Request $request)
    {
        $user = $this->actor();
        abort_unless($user->exhibitor, 404, 'No exhibitor profile is attached to this account.');
        $data = $request->validate(ExhibitorProfile::rules($request->input('entity_type')));
        ExhibitorProfile::apply($user->exhibitor, $data, $user->name);
        [$old, $new] = Audit::diff($user->exhibitor);
        if ($new) {
            Audit::log('exhibitor.updated', "{$user->exhibitor->organization_name} updated their profile", $user->exhibitor, $old, $new);
        }

        return $user->exhibitor;
    }
}
