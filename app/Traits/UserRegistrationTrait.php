<?php
namespace App\Traits;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

trait UserRegistrationTrait
{
    public function ExtenaluserRegistration($company_id,$email,$phone_number,$password,$firstName,$lastName){
        $user=User::create([
            'user_uuid'=>$company_id,
             'first_name'=>$firstName,
             'last_name'=>$lastName,
             'phone_number'=>$phone_number,
             'email'=>$email,
             'role'=>41,
             'password'=>hash::make($password),
            ]);
            $user->assignRole(41); // asigning user to role 41 (Normal User)
    }


    public function UpdateUser($id,$firstName,$phone_number,$email){
        if(User::where('user_uuid',$id)->exists())
        {
        $users=User::where('user_uuid',$id)->get();
        foreach ($users as $value)
        {
            $user=User::find($value->id);
            $user->update([
                'first_name'=>$firstName,
                 'phone_number'=>$phone_number,
                 'email'=>$email,
            ]);

        }
        }
        else
        {
           $user=User::create([
                'user_uuid'=>$id,
                'first_name'=>$firstName,
                'last_name'=>'.',
                'phone_number'=>$phone_number,
                'email'=>$email,
                'password'=>Hash::make(123456),
                'role'=>41,
            ]);
            $user->assignRole(41); // asigning user to role 41 (Normal User)
        }
    }
}
//

