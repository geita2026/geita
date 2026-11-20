<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\APITraits;
use App\Traits\NotificationTrait;

class ThirdPartMessageController extends Controller
{
    use NotificationTrait;
    use APITraits;

    #------------------ THIRD PART SEND TOKEN MECHANISM -------------------------#
    public function store(Request $request)
    {
        try
        {
        $request_name=$request->response['sp_code'];
        $token=$request->response['token'];
        $status=$this->AuthenticateRequest($request_name,$token);

        if($status==0)
        {
            $response=array('code'=>401,'message'=>'Authentication failed');
            return response()->json([
                'response'=>$response,
            ]);
        }
        $recipients=$request->response['phone_number'];
        $sms_content=$request->response['sms_content'];
        $datetime = date('Y-m-d H:i:s');
        $status=$this->Notification($recipients,$sms_content,$datetime);
        
        return response()->json([
            'code'=>200,
            'message'=>'Token sent successfully'
        ]);



        } catch (\Throwable $th) {
            return response()->json([
                'code'=>500,
                'message'=>'Something went wrong!'.$th->getMessage(),
            ]);
        }
    }
    #-------------------------- END SEND TOKEN ----------------------- #

}
