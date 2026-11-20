<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\Collections\PaymentDetails;
use Illuminate\Http\Request;
use App\Traits\PaymentsTraits;
use App\Traits\APITraits;

class PaymentController extends Controller
{
    use PaymentsTraits;
    use APITraits;

   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
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

             // values from billing
             $reference=$request->data['reference'];
             $receipt=$request->data['receipt'];
             $bank_receipt=$request->data['bank_receipt'];
             $gate_way_receipt=$request->data['gateway_receipt'];
             $amount_paid=$request->data['amount'];
             $contro_number=$request->data['control_number'];
             $trans_date=$request->data['trans_date'];
             $bank_account=$request->data['bank_account'];
             $currency=$request->data['currency'];
             // end values from billing

             $payments=PaymentDetails::where('receipt',$receipt)->get();
             foreach ($payments as $value) {
                $response=array('code'=>200,'message'=>'Dublicate payments');
                return response()->json(['response'=>$response]);
             }

             // posting methode
            $status= $this->PostPayments($reference,$contro_number,$amount_paid,$currency,$receipt,$bank_receipt,$gate_way_receipt,$trans_date,$bank_account); // methode to post payments to table.
            // end posting methode

            $response=array('code'=>200,'message'=>'Payments posted successfully!');
            // Status 1 mean successfully process, status 0 mean process failed
            if($status==1)
            {
             return response()->json([
                 'response'=>$response,
             ]); // return success response.
            }


            else{
                $response=array('code'=>500,'message'=>'Something went wrong!');
             return response()->json([
                 'response'=>$response,
             ]); //return unsuccessfully action.
            }
          } catch (\Throwable $th) {
            //throw $th;
            $response=array('code'=>500,'message'=>'Something went wrong!');
            return response()->json([
                'response'=>$response,
            ]);
          }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
