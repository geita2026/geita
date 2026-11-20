<?php

namespace App\Http\Controllers\APIs;
use App\Http\Controllers\Controller;
use App\Models\Collections\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\APITraits;
use App\Traits\NotificationTrait;
use App\Models\advice_note\advice_notes;
use App\Models\Collections\OffFairBill;
use Illuminate\Support\Facades\Mail;

class ControlNoApiController extends Controller
{
    use APITraits;
    use NotificationTrait;

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
            $response=array('code'=>401,'message'=>'Authentication Failed, plese try valid details'); // authentication response
            return response()->json([
                'response'=>$response
            ]);
        }

            // $invoice_no=$request->data['invoice_no']; // service provider invoice ID.
            $control_no=$request->data['control_number']; // control no for the invoice.
            $reference=$request->data['reference']; // reference ID.


            $invoice=advice_notes::find($reference);
            if($invoice)
            {
                $invoice->update(['control_number'=>$control_no]); // update advice note with control number.
                $response=array('code'=>200,'message'=>'Control number posted successfully!');

                $this->SentControlNoNotification($reference); //this methode send control number to user(BulkSms).
                $this->sendControlNoToCustomer($reference); // send control number to user (Email).
                return response()->json(['response'=>$response]); // set success message.
            }





            // for off fair
            $off_fair_bill=OffFairBill::where('advice_note_id',$reference)->get();
            foreach ($off_fair_bill as $value) {
                OffFairBill::find($value->bill_id)->update(['control_number'=>$control_no]); // filling control numbers
                $this->SentControlNoNotification($reference); //this methode send control number to user Bulk Sms.
                $this->sendControlNoToCustomer($reference); // send control number to user (Email).

                $response=array('code'=>200,'message'=>'Control number posted successfully!'); // response
                return response()->json(['response'=>$response]); // set success message.
            }



            $response=array('code'=>200,'message'=>'No invoice for this details');

            return response()->json(['response'=>$response]);

        } catch (\Throwable $th) {
            return response()->json(['status'=>404,'message'=>'Something went wrong'.$th->getMessage()]);
        }

    }



    // send control no notification to user.
    function SentControlNoNotification($reference){

        $invoice=advice_notes::leftJoin('companies','advice_notes.company_id','companies.companyID')
        ->leftJoin('company_details','advice_notes.company_id','=','company_details.companyID')->first();


            $phone_number=$invoice->companyPhone;
            $customer_name=$invoice->companyName;
           $amount=number_format($invoice->total_amount).$invoice->currency;
           $control_no=$invoice->control_number;
           $deadline=date('d-m-Y 00:00:00',strtotime($invoice->advice_note_deadline));
           $msg="You have been Charged for Participating in 50 DITF";
           $invoice_url=env('APP_URL').'/adviceNotes/'.$reference;


// sms to be sent
$sms_content ="Dear, ".$customer_name.", $msg, Control No: $control_no, Total Amount: $amount, Deadline: $deadline $invoice_url";
//end of sms to be sent


        $recipients=$phone_number; $datetime = date('Y-m-d H:i:s');
        $this->Notification($recipients,$sms_content,$datetime); // send sms methode

        // $this->SaveSendSmsJobs($phone_number,$sms_content,$sent_on,$customer_name); // saving message to jobs

    }


    function sendControlNoToCustomer($id){

        // Item  booked
        $particulars=advice_notes::leftJoin('particulars','advice_notes.advice_note_id','=','particulars.advice_note_id')
        ->leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->where('advice_notes.advice_note_id',$id)
        ->get(
            ['particulars.advice_note_id','companyName','particular_name','cost_for_one_particular',
             'particular_quantity','type_of_currency','particular_category','particular_description',
             'particular_type','contain_vat'
            ]);// end

       $adviceNotes=advice_notes::leftJoin('companies','companies.companyID','=','advice_notes.company_id')
       ->leftJoin('company_details','advice_notes.company_id','=','company_details.companyID')
       ->leftJoin('users','advice_notes.user_id','=','users.user_uuid')
       ->leftJoin('roles','users.role','=','roles.id')
       ->leftJoin('events','advice_notes.event','=','events.event_id')
       ->leftJoin('bills','advice_notes.advice_note_id','=','bills.advice_note_id')
       ->where('advice_notes.advice_note_id',$id)
       ->get([
           'advice_notes.advice_note_id','companyName','advice_note_created_at','companyAddress','companyLocation',
           'companyPhone','personalPhone','first_name','last_name','companyEmail','roles.name','advice_note_deadline'
           ,'advice_note_description','tinNo','users.user_signature','registrationNo','advice_note_status',
           'advice_notes.updated_at','event_name','event_descriptions','vat','total_amount','currency','control_number'
           ]);

           foreach ($adviceNotes as  $value) {
               $email=$value->companyEmail;
               // $email=$value->personalEmail;
               $name=$value->companyName;
           }

       $email_data=[
           'fromName'=>config('app.name'),
           'email'=>$email,
           'name'=>$name,
           'adviceNotes'=>$adviceNotes,
           'particulars'=>$particulars,
           'subject'=>'CONTROL NUMBER',
       ];
       Mail::send('mails.invoice', $email_data, function ($message) use ($email_data) {
           $message
           ->to($email_data['email'],$email_data['name'])
           ->priority(3)
           ->subject($email_data['subject']);
   });
   }

}
