<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\EsbHelper;

class TestIntergrationController extends Controller
{
   

    public function RequestToNida(Request $request){

        $esbHelper = new EsbHelper();
        $nin = "20030518214060000129";
        $payload = [
            'Payload' => [
                'NIN' => $nin,
            ],
        ];
        $payload=[
            "NIN"=>$nin,
        ];

        //  Kwa Request ya kawaida NECTA,TCU,NACTVET,TAMISMEI,NAPA,BRELA
       
    //    $esbData = $this->requestData("apiCodeUnawekaHapa", $payload, "json");

       //  Kwa wanaoenda NIDA
              $esbData = $esbHelper->requestData("q1mCOpjk", $payload, "json");
       
       //	Kwa wanao push
       //        $esbData = $esbHelper->pushData("pushCodeUnawekaHapa", $requestPayload, "json");
       
               //Kwa wanao jibu async response
       //        $esbData = $esbHelper->asyncSuccessResponse("requestId", $requestPayload, "json");
       
               if (!$payload) {
                   //Signature verification has failed.
                   return 'error occured';
                   //Throw exception ama handle kama ikifeli signature verification
               }
       
               return dd($esbData);
               $esbRequestId = $esbData["requestId"];
               $esbSuccess = $esbData["success"];
              return  $producerResponse = $esbData["esbBody"];

               if (!$esbSuccess) {
                   //handle failure
               }
       
               //Implement business logic ....

    }
}
