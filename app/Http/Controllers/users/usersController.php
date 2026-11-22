<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\Companies\CompanyAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Traits\NotificationTrait;;

class usersController extends Controller
{
    use NotificationTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display users.
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('View Users', User::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $users = User::leftJoin('roles', 'users.role', '=', 'roles.id')
            ->select([
                'users.id',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.name',
                'users.email',
                'users.user_uuid',
                'users.user_ip_address',
                'users.role',
                'users.phone_number',
                'users.account_status',
                'users.account_type',
                'users.myCompany',
                'roles.name as role_name',
            ]);

        if ($request->filled('userName')) {
            $users->where('users.email', 'LIKE', '%' . $request->userName . '%');
        }

        if ($request->filled('user_type')) {
            $users->where('users.account_type', $request->user_type);
        }

        $users = $users
            ->orderBy('users.first_name', 'ASC')
            ->orderBy('users.id', 'DESC')
            ->paginate(25)
            ->appends($request->query());

        $roles = Role::all();

        $companies = companies::where('status', 1)
            ->orderBy('companyName', 'ASC')
            ->get();

        return view('users.users', compact('users', 'roles', 'companies'));
    }

    /**
     * Show create user form.
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('Create Users', User::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $roles = Role::all();

        return view('users.add_user', compact('roles'));
    }

    /**
     * Store new user.
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Create Users', User::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $request->validate([
            'TSRN' => 'nullable|exists:companies,registrationID',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|numeric|digits:12|unique:users,phone_number',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'signature_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        try {
            $userSign = null;
            $profileImage = null;
            $userCompany = null;
            $accountType = 1;

            if ($request->hasFile('signature_image')) {
                $userSign = Str::uuid() . '.' .
                    $request->file('signature_image')->extension();

                $request->file('signature_image')->move(
                    public_path('signatures'),
                    $userSign
                );
            }

            if ($request->hasFile('profile_image')) {
                $profileImage = Str::uuid() . '.' .
                    $request->file('profile_image')->extension();

                $request->file('profile_image')->move(
                    public_path('profile_images'),
                    $profileImage
                );
            }

            if ($request->filled('TSRN')) {
                $company = companies::where(
                    'registrationID',
                    $request->TSRN
                )->firstOrFail();

                $userCompany = $company->companyID;
                $accountType = 2;
            }

            $defaultPassword = $request->phone_number;

            $user = User::create([
                'user_uuid' => (string) Str::uuid(),
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'profile_image' => $profileImage,
                'user_signature' => $userSign,
                'role' => $request->role,
                'account_type' => $accountType,
                'password' => Hash::make($defaultPassword),
                'myCompany' => $userCompany,
            ]);

            $role = Role::findOrFail($request->role);
            $user->assignRole($role->name);

            if (config('global.APP_STATUS') == 1) {
                $this->EmailPasswordNotification(
                    $request->email,
                    $request->first_name,
                    $request->last_name,
                    $defaultPassword
                );

                $customerName = $request->first_name . ' ' . $request->last_name;

                $this->SendDefaultPassSMS(
                    $request->phone_number,
                    $defaultPassword,
                    $customerName
                );
            }

            return redirect()
                ->route('users.index')
                ->with(
                    'success',
                    $request->first_name . ' ' .
                    $request->last_name .
                    ' Information registered successfully!'
                );

        } catch (\Throwable $th) {
            return back()
                ->withInput()
                ->with('fail', 'User registration failed: ' . $th->getMessage());
        }
    }

    /**
     * Display specified user.
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('View Users', User::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        return back()->with('fail', 'We did not understand your request!');
    }

    /**
     * Show edit user form.
     */
    public function edit(Request $request, $id)
    {
        if ($request->user()->cannot('Update Users', User::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $users = User::leftJoin('roles', 'users.role', '=', 'roles.id')
            ->where('users.id', $id)
            ->select([
                'users.id',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.name',
                'users.email',
                'users.user_uuid',
                'users.phone_number',
                'users.role',
                'users.user_signature',
                'roles.name as role_name',
            ])
            ->get();

        $roles = Role::all();

        return view('users.edit_user', compact('users', 'roles'));
    }

    /**
     * Update user.
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('Update Users', User::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|numeric|digits:12|unique:users,phone_number,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'role_name' => 'required|exists:roles,id',
            'TSRN' => 'nullable|exists:companies,registrationID',
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            if ($request->filled('TSRN')) {
                $company = companies::where(
                    'registrationID',
                    $request->TSRN
                )->firstOrFail();

                $exists = CompanyAdmin::where(
                    'companyId',
                    $company->companyID
                )
                    ->where('adminId', $user->user_uuid)
                    ->exists();

                if (!$exists) {
                    CompanyAdmin::create([
                        'referenceId' => (string) Str::uuid(),
                        'adminId' => $user->user_uuid,
                        'companyId' => $company->companyID,
                    ]);
                }
            }

            $role = Role::findOrFail($request->role_name);

            $user->update([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'role' => $role->id,
                'account_status' => 1,
            ]);

            $user->syncRoles([$role->name]);

            DB::commit();

            return back()->with('success', config('Messages.200'));

        } catch (\Throwable $th) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('fail', config('Messages.500') . ' ' . $th->getMessage());
        }
    }

    /**
     * Disable or suspend user account.
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('Delete Users', User::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        if ((int) $id === (int) Auth::id()) {
            return back()->with(
                'fail',
                'Restricted! You cannot disable or delete your own account.'
            );
        }

        try {
            $user = User::findOrFail($id);

            $user->update([
                'account_status' => 0,
            ]);

            if ($request->boolean('disable_ccount')) {
                return back()->with(
                    'success',
                    'Account disabled successfully. User can no longer access the system!'
                );
            }

            return back()->with('success', 'Account suspended successfully!');

        } catch (\Throwable $th) {
            return back()->with(
                'fail',
                'Something went wrong: ' . $th->getMessage()
            );
        }
    }

    /**
     * Send default password SMS.
     */
    public function SendDefaultPassSMS(
        $recipients,
        $default_password,
        $customer_name
    ) {
        $smsContent = "Dear, {$customer_name}
You have been registered to GeMEMIS. Below are your login credentials.
Username: Your registered email.
Password: {$default_password}.";

        $datetime = now()->format('Y-m-d H:i:s');

        $this->Notification(
            $recipients,
            $smsContent,
            $datetime
        );
    }
}
