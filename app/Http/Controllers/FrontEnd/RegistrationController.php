<?php
namespace App\Http\Controllers\FrontEnd;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\companies;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Traits\NotificationTrait;
use App\Traits\UserRegistrationTrait;

class RegistrationController extends Controller
{
    use NotificationTrait;
    use UserRegistrationTrait;


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'company_name'=>'required|unique:users,first_name|unique:companies,companyName',
            'phone_number'=>'required|unique:users,phone_number|digits:12|unique:companies,companyPhone',
            'company_email'=>'required|email|unique:companies,companyEmail',
        ]);

        if($validator->fails()){
            return response(['status'=>0,'error'=>$validator->errors()->toArray()]);
        }

        // try {

            $company_id=Str::uuid(); $email=$request->company_email; $phone_number=$request->phone_number;
            $password=strtoupper(Str::random(8)); // this creates the defaul password for user.
            $firstName=$request->company_name; $lastName='.';
            // register company in company list4
                $reg_id='T-'.substr(date('Y'),-3).'-'.random_int(100,999).'-'.random_int(100,999);
                companies::create([
                    'companyID'=>$company_id,
                    'registrationID'=>$reg_id,
                    'companyName'=>$firstName,
                    'companyEmail'=>$email,
                    'companyPhone'=>$phone_number,
                    'status'=>'1',
                ]);
                // register this company as system user
                $this->ExtenaluserRegistration($company_id,$email,$phone_number,$password,$firstName,$lastName); // this methode register new user
                $default_password=$password; $recipients=$phone_number; $datetime = date('Y-m-d H:i:s');
$sms_content="Dear $firstName,
Your username is: your email or phone number
your password is: ".$default_password;
                $this->Notification($recipients,$sms_content,$datetime); // send Bulk sm to user
                $this->EmailPasswordNotification($email,$firstName,$lastName,$default_password); // send mail to user
                // end

                return response(['status'=>1, 'success'=>'Account for '.$firstName.' created successfully, Now you can login']);

        // } catch (\Throwable $th) {
        //     //throw $th;
        //     return back()->with('fail', 'Something went wrong!');
        // }
    }
}
