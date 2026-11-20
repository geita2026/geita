<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\VipTicketBookings;
use App\Traits\IntergrationTraits;
use Google\Service\Docs\Request;
use App\Traits\NotificationTrait;

class ContactVerificationController extends Controller
{
    use IntergrationTraits;
    use NotificationTrait;
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id){
       try {
        $reference=$id; 
        $booking= VipTicketBookings::find($reference);
        $amount_paid=$booking->amount_paid;
        $this->PaymentNotification($reference,$amount_paid);
        return back()->with('status','Verified Tickets');
       } catch (\Throwable $th) {
        return redirect()->route('online_tickets.index')->with('fail','We Regrate to notify you that, Invoice download is currently unavailable...!').$th->getMessage();
       }

    //  
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
           
            #*********** VERIFY CONTACTS ********** #
        $ticket=VipTicketBookings::find($id);

        if($ticket->verification_status!=0)
        {
            return redirect()->route('online_tickets.index')->with('fail','Your Request has already been verified! ,
             wait a few minutes, we are processing your Control Number. Check your email or Phone after sometime.');
        }
        $company_id=$ticket->ticket_booking_id;
        $firstName=$ticket->customer_name;
        $phone_number=$ticket->phone_number;
        $email=$ticket->email_address;
        $status=0;
        if(config('global.APP_STATUS')==1){
            $status=$this->customer_registration_api($company_id,$firstName,$phone_number,$email); // register user as customer in billing.
        }
        else
        {
            VipTicketBookings::find($id)->update(['verification_status'=>1,'sp_customer_no'=>'CN-0021']);
            $this->OrganizeInvoice($company_id); 
        }

        #***********END VERIFY CONTACTS ********** #
        
        return redirect()->route('online_tickets.index')->with('success','Your Request verified successfully! You will get a control number in two to five minutes.');
        } catch (\Throwable $th) {
            return redirect()->route('online_tickets.index')->with('fail','ERROR_102');
        }
    }


    public function OrganizeInvoice($company_id)
    {
        $invoice=VipTicketBookings::find($company_id);
        $vat=0; $descriptions='Being charged for 48th DITF TICKETS';
        $items[]=['item_code'=>$invoice->gfs_code_mapping,'amount'=>$invoice->ticket_amount];
        $id=$company_id;
        $data=array('customer_no'=>$invoice->sp_customer_no,'amount'=>$invoice->ticket_amount,'currency'=>$invoice->currency,
            'reference'=>$company_id,'withholding_tax'=>0,'taxable'=>$vat,'payment_mode'=>2,
            'expire_date'=>date('d-m-Y',strtotime($invoice->payment_deadline)),'items'=>$items,'description'=>$descriptions
        );
        $this->RequestInvoiceToBilling($id,$data); // requesting invoice to billing
    }
}
