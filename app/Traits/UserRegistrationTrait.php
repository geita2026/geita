<?php
namespace App\Traits;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

trait UserRegistrationTrait
{
    public function ExtenaluserRegistration($company_id,$email,$phone_number,$password,$firstName,$lastName){
        $customerRole = Role::findByName('Customer', 'web');
        $user=User::create([
            'user_uuid'=>$company_id,
             'first_name'=>$firstName,
             'last_name'=>$lastName,
             'phone_number'=>$phone_number,
             'email'=>$email,
             'role'=>$customerRole->getKey(),
             'password'=>hash::make($password),
            ]);
            $user->assignRole($customerRole);
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
            $customerRole = Role::findByName('Customer', 'web');
            $user=User::create([
                'user_uuid'=>$id,
                'first_name'=>$firstName,
                'last_name'=>'.',
                'phone_number'=>$phone_number,
                'email'=>$email,
                'password'=>Hash::make(123456),
                'role'=>$customerRole->getKey(),
            ]);
            $user->assignRole($customerRole);
        }
    }
}
//

