<?php
namespace App\Http\Controllers\mails;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class sendPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

            $data=[
                'body'=>'Your remembered to change password after first login.',
                'subject'=>'Password Notification',
            ];

            Mail::send('mails.default-password', $data, function ($message) {

                $message->from('suport.tantradeforms@gmail.com', 'sac.go.tz');

                $message->sender('suport.tantradeforms@gmail.com', 'SAC');

                $message->to('remixkabandwa@gmail.com','KABANDWA');

                // $message->bcc('obedmwaka@gmail.com', 'KABANDWA');

                $message->subject('PASSWORD NOTIFICATION');

                $message->priority(3);

                // $message->attach(public_path('images/logo.jpg'));

            });

            return back()->with('success','Message sent successfully!');
    }
    
}
