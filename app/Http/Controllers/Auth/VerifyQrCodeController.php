<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\Collections\PaymentDetails;
use App\Models\ePermit\ApplicationInvoice;
use App\Models\ePermit\ePermitApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerifyQrCodeController extends Controller
{
    public function VerifyQrCodeFunction(Request $request,$data){
        $validator=Validator::make($request->all(),[
            'data'=>'string|max:50'
        ]);
        if ($validator->fails()) {
            return back()->with('fail','Data or Document you are Verifying is not recorginazed by Tanzania Trade Development Authority (TanTrade) For more help Contact: '.config('global.CALL_CENTER'));
        }
        $search=$request->data;
        $data=null;
        $payments=PaymentDetails::where('payment_id','LIKE','%'.$search.'%')
        ->leftJoin('advice_notes','payment_details.referenceId','=','advice_notes.advice_note_id')
        ->leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->first();
        if ($payments) {
            $data=[
                'documentTitle'=>'CUSTOMER PAYMENT RECEIPT',
                'ownerName'=>$payments->companyName,
                'description'=>'ReceiptNo: '.$payments->receipt. " & ControlNumber: ".$payments->control_number,
            ];
        }
        $permits=ApplicationInvoice::leftJoin('companies','application_invoices.customerId','=','companies.companyID')
        ->where('referenceApplication','LIKE','%'.$search.'%')->first();
        
        if ($permits) {
            $data=[
                'documentTitle'=>'TRADE PROMOTION EVENT PERMIT',
                'ownerName'=>$permits->companyName,
                'description'=>'NULL',
            ];
        }
       
        $invoices=advice_notes::leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->where('advice_note_id',$search)->first();
        if ($invoices) {
            $data=[
                'documentTitle'=>'CUSTOMER INVOICE',
                'ownerName'=>$invoices->companyName,
                'description'=>'InvoiceNumber: '.$invoices->advice_note_id. " & ControlNumber: ".$invoices->control_number.", Amount To be Paid: ".number_format($invoices->total_amount,2)." ".$invoices->currency,
            ];
        }
            return view('DocumentsVerification.QrCodVerify',compact('data'));
       
    }
}
