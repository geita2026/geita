<?php
namespace App\Http\Controllers\users;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Traits\NotificationTrait;

class resendPasswordController extends Controller
{
    use NotificationTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if($request->user()->cannot('Resend Password',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $password=str::random(8);
        $user=User::get(['first_name','last_name','id','email'])->where('id',$id);
        foreach ($user as $value) {

        }

        try {
            $email_data=[
                'fromName'=>config('app.name'),
                'email'=>$value->email,
                'name'=>$value->first_name. ' '. $value->last_name,
                'password'=>$password,
                'body'=>'Your remembered to change password after first login.',
                'subject'=>'PASSWORD NOTIFICATION',
            ];

            Mail::send('mails.password', $email_data, function ($message) use ($email_data) {
                $message
                ->to($email_data['email'],$email_data['name'])
                ->priority(1)
                ->subject($email_data['subject']);
        });
        #update password
        $users=User::find($id);
        $users->update([
            'password'=>hash::make($password),
        ]);
        #update password
        return back()->with('success',"New Password sent to $value->email successfully");
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong!, It might be due to Internet connection problems, Please try Again.');
        }


    }

    public function update(Request $request,$id){
        if($request->user()->cannot('Resert Password',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        try
        {
            $user=User::find($id);
            $phone_number=$user->phone_number;
            $customer_name=$user->first_name.' '.$user->last_name;
            $sms_content= "Loging with the following credentials:
UserName:".$user->email."
Password:".$request->new_password."
Click this link:".env('APP_URL');
$sent_on=date('Y-m-d');
$this->SaveSendSmsJobs($phone_number,$sms_content,$sent_on,$customer_name);
            User::find($id)->update(['password'=>hash::make($request->new_password)]); // update with new password
            return back()->with('success','Password updated successfully!');
        }
        catch (\Throwable $th)
        {
            return back()->with('fail','Something went wrong!');
        }

    }
}
