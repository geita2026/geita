<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class accountSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if($request->user()->cannot('ACCOUNT_SETTINGS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('ACCOUNT_SETTINGS'));
        }
        return view('settings.account_setting');
    }

    public function create()
    {


        $user_id = 'ict.list@tantrade.go.tz';
        $live_end_point = "https://msdg.ega.go.tz/msdg/public/quick_sms";
        $local_end_point = "http://msdg.ega.go.tz/msdg/public/quick_sms";
        $request_type='api';
        $datetime='2023-05-15 00:25:00';

        $APIKey='LXupStknkJE9XEwdLnn9Hz7ootohSIsTreLqKXMv';

        $recipients=array('255753427137');
        $message=array('msisdn'=>'255753427137','message'=>'Hellow','datetime'=>$datetime,'sender_id' =>'TANTRADE', 'mobile_service_id' =>35);
        $data=json_encode($message);
        $request_data=array('data'=>$data,'datetime'=>$datetime);

        $request_data=json_encode($request_data);


        $hash= base64_encode(hash_hmac('sha256', $request_data, $APIKey, true));

        $headers=array('X-Auth-Request-Hash'=>$hash,'X-Auth-Request-Id'=>$user_id,'X-Auth-Request-Type'=>$request_type);

       $response= Http::withOptions(["verify"=>false])->withHeaders([
            'headers'=>$headers ])
        ->post($live_end_point,[ 'data'=>$request_data, 'datetime'=>'2023-05-15 00:25:00' ]);

        return $response['statusCode'];
    }

}
