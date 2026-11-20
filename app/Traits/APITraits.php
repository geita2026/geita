<?php
namespace App\Traits;

use App\Models\APIs\Intergration;
use App\Models\setting\ApiRequest;
trait APITraits
{
    function AuthenticateRequest($request_name,$token)
    {
        try {
            $exists=ApiRequest::where('request_name',$request_name)->where('access_token',$token)->get();
        foreach($exists as $item){ return 1; }
        return 0; // if not authenticated
        } catch (\Throwable $th) {
            return 2; // something went wrong
        }
    }

    function APIs(){

        // $base_url='https://billing.tantrade.go.tz/index.php/v1/'; // billing base url
        // $code='SAMS';
        // $token='MH20230501NAVNORMILELIZURqm$dynUpe&@Y4YzgRpgnJ#y&XB%VQA95HBgq@guXSw68*fPV8!';

        #*************** API INFORMATION FROM DB ************#
        $api_info=Intergration::first();
        $base_url=$api_info->billing_base_url;
        $code=$api_info->billing_service_code;
        $token=$api_info->billing_access_token;
        #END *******************API INFORMATION *************#

        $auth=array('code'=>$code,'token'=>$token);
        $api=array('base_url'=>$base_url,'auth'=>$auth);
        return $api;
    }

}
