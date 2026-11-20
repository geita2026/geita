<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends Controller
{
    //

    /**
     * Create User
     * @param Request $request
     * @return User
     */

     public function createUser(Request $request)
     {

        try {
            $validateUser=Validator::make($request->all(),
            [
                'first_name'=>'required',
                'last_name'=>'required',
                'email'=>'required|email',
                'phone_number'=>'required|digits:12',
            ]);

            if($validateUser->fails())
            {
                return response()->json([
                    'status'=>false,
                    'message'=>'Validation error',
                    'errors'=>$validateUser->errors()
                ],400);
            }
            $password=hash::make($request->phone_number);
            $user=User::create([
                'user_name'=>$request->user_name,
                 'phone_number'=>$request->phone_number,
                 'email'=>$request->email,
                 'password'=>$password,
            ]);

            return response()->json([
                'status'=>true,
                'message'=>'user created successfull!',
                'token'=>$user->createToken("API TOKEN")->plainTextToken,
            ],200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage()
            ],500);
        }
     }



      /**
     * Login The User
     * @param Request $request
     * @return User
     */
     public function loginUser(Request $request)
     {
       try {
        $validateUser=Validator::make($request->all(),
        [
            'user_name'=>'required',
            'password'=>'required'
        ]);

        if($validateUser->fails())
        {
            return response(['status'=>0,'message'=>'please enter username or password','errors'=>$validateUser->errors()],404);
        }
        $user=User::orwhere('email',$request->user_name)
        ->orWhere('phone_number',$request->user_name)
        ->first();
        if (!$user || !Hash::check($request->password,$user->password)) {

            return response()->json(['status'=>0,'message'=>'Incorect Username or Password'],200);
        }

        else
        {
            Auth::login($user,$this->boolean('remember'));
            RateLimiter::clear($this->throttleKey());
            $request->session()->regenerate();
            return response(['status'=>1,'user'=>$user,'message'=>'Successfully Logged in!']);
        }


    }
       catch (\Throwable $th) {
        //throw $th;
        $message='something went wrong: '.$th->getMessage();
        $status=0;
        return response()->json(['status'=>$status,'message'=>$message],500);
       }
     }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('user_name')).'|'.$this->ip();
    }





    // authenticate API requests


}
