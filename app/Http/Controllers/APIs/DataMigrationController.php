<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\companies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Traits\BillingTrait;
use Illuminate\Support\Facades\Hash;

class DataMigrationController extends Controller
{
    use BillingTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $sp_customer_no=$request->response['customer_no'];
        $reference_id=$request->response['sp_customer_no'];
        try {
            companies::find($reference_id)->update(['sp_customer_no'=>$sp_customer_no,]);
            $response=array('status'=>1, 'message'=>'company updated successfully!');
            return response()->json(['response'=>$response]);
        } catch (\Throwable $th)
        {
            $response=array('status'=>0,'Something went wrong'.$th->getMessage());
            return response()->json(['response'=>$response]);

        }

    }

    // EXCHANGE SP CUSTOMER NUMBER
    public function InvoiceDataMigration(Request $request)
    {
        // $url='http://192.168.1.84/TANTRADENEWBILLING/index.php/v1/api_get_invoice_list';
        $url='https://billing.tantrade.go.tz/index.php/v1/api_get_invoice_list';
        $auth=array('token'=>'MH20230501NAVNORMILELIZURqm$dynUpe&@Y4YzgRpgnJ#y&XB%VQA95HBgq@guXSw68*fPV8!',"code"=>"SAMS");

        $response=Http::withOptions(["verify"=>false])->post($url,[ 'auth'=>$auth]);


        foreach ($response['data'] as $value) {
            $advice_notes=advice_notes::leftJoin('events','advice_notes.event','=','events.event_id')->where('event_status',1)
            ->where('advice_note_status','!=','CANCELED')->orderBy('advice_note_created_at','DESC')->get();
            foreach ($advice_notes as $item) {
               if($item->company_id==$value['sp_customer_no'])
               {
                companies::find($item->company_id)->update(['sp_customer_no'=>$value['customer_no']]);

                if($item->total_amount==Null && $item->company_id==$value['sp_customer_no']){
                    $id=$item->advice_note_id;
                    $this->CreateBill($id);
                }

                if($value['amount']==$item->total_amount)
                {
                    advice_notes::find($item->advice_note_id)->update([
                        'sp_invoice_no'=>$value['invoice_no'],
                        'control_number'=>$value['control_number']
                    ]);
                }
                // cumpany exists but no invoice
                else
                {
                    companies::find($item->company_id)->update(['invoice_exist'=>1]);
                }

               }

            }

        }


    }




    //  GET ALL INVOICES AND PAYMENTS
    public function ReturnInvoice(){

        $invoice=advice_notes::where('sp_invoice_no','!=','null')->get(['sp_invoice_no','advice_note_id']);

        $data=array();
        foreach ($invoice as $value) {
            $data[]=array('reference'=>$value->advice_note_id,'invoice_no'=>$value->sp_invoice_no);
        }
        $url='http://192.168.1.84/TANTRADENEWBILLING/index.php/v1/api_receiver_update';
        $url='https://billing.tantrade.go.tz/index.php/v1/api_receiver_update';
        $auth=array('token'=>'MH20230501NAVNORMILELIZURqm$dynUpe&@Y4YzgRpgnJ#y&XB%VQA95HBgq@guXSw68*fPV8!',"code"=>"SAMS");
        $response=Http::withOptions(["verify"=>false])->post($url,['data'=>$data,'auth'=>$auth]);
        return $response;

    }




}
