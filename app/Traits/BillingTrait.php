<?php
namespace App\Traits;

use App\Models\advice_note\advice_notes;
use App\Models\advice_note\particulars;
use App\Models\Collections\Bill;
use App\Models\Collections\PaymentDetails;
use App\Models\Logs\ApiLogs;
use App\Traits\IntergrationTraits;

trait BillingTrait
{

    use BillingJobsTrait;
    use IntergrationTraits;

    // id is an advice note id
     function CreateBill($id)
     {
        #---- IN FAIR BILL CREATION ---#
        if(advice_notes::where('advice_note_id',$id)->exists()){
            $particulars=particulars::leftJoin('advice_notes','particulars.advice_note_id','=','advice_notes.advice_note_id')
            ->where('particulars.advice_note_id',$id)->get();
            $currency='Null';
            $total_amount=0;
            foreach ($particulars as $item) { $total_amount=round($total_amount+($item->cost_for_one_particular*$item->particular_quantity),2); $currency=$item->type_of_currency; }
            $vat=0; // default vat is zero.  
            if ($total_amount>0) {
                if($item->contain_vat>0){ $vat=round($total_amount*0.18,2); $total_amount=round($total_amount+$vat,2); } // VAT + AMOUNT
            }
            advice_notes::find($id)->update([ 'total_amount'=>$total_amount,'balance'=>$total_amount,'currency'=>$currency,'total_vat_amount'=>$vat ]); // adding bills details to invoice
        }
        #---- END IN FAIR BILL CREATION ---#
    }


    // methode to generate invoice
    function GenerateInvoice($id){
        $total_amount=0;
        $particulars=particulars::leftJoin('advice_notes','particulars.advice_note_id','=','advice_notes.advice_note_id')
        ->leftJoin('events','advice_notes.event','=','events.event_id')
        ->leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->where('advice_note_status','!=','CENCELED')
        ->where('particulars.advice_note_id',$id)
        ->get([ 'particulars.advice_note_id','gfs_code','particular_quantity','cost_for_one_particular', 'currency',
        'event_name','advice_note_deadline','sp_customer_no','contain_vat','payment_mode' ]);

        foreach ($particulars as $value)
         {
          $total_amount=round($total_amount+($value->particular_quantity*$value->cost_for_one_particular),2);
          $currency=$value->currency;
          $amount=round($value->particular_quantity*$value->cost_for_one_particular,2);
          $items[]=['item_code'=>$value->gfs_code,'amount'=>round($amount,2)]; // ITEMS

           $vat=$value->contain_vat; // get VAT status

            $sp_customer_no=$value->sp_customer_no;
            $advice_note_deadline=date('d-m-Y',strtotime($value->advice_note_deadline));
            $descriptions='Being charged for the '.$value->event_name; $payment_mode=1;  #$value->payment_mode
        }

        #------------SUM ARRAY WITH THE SAME GFS CODE -----------#
        $result = array_reduce($items, function($carry, $item)
        {
            if(!isset($carry[$item['item_code']]))
            {
                $carry[$item['item_code']] = ['item_code'=>$item['item_code'],'amount'=>$item['amount']];
            }
            else
            {
                $carry[$item['item_code']]['amount'] += $item['amount'];
            }
            return $carry;
        });

        foreach ($result as $key => $value)
        {
            $joint[]=['item_code'=> $value['item_code'],'amount'=>$value['amount']]; // SUMMED ITEMS ARRAY
        }
        $items=$joint;  // assigning value to items variable
        #--------------- END OF NEW ARRAY ----------- #

        $data=array('customer_no'=>$sp_customer_no,'amount'=>$total_amount,'currency'=>$currency,
            'reference'=>$id,'withholding_tax'=>0,'taxable'=>$vat,'payment_mode'=>1,
            'expire_date'=>$advice_note_deadline,'items'=>$items,'description'=>$descriptions,); // data array must be of this type to get control number

        ######### SAVE JSON TO DATABASE ###############
        $jsonData = json_encode($data);
        #END SAVING JSON TO DATABASE ##################
        $this->RequestInvoiceToBilling($id,$data); // requesting invoice to billing
        

    }

    // end of generating invoice



    // function to update balance in bills table
    function updateBillWithPayments($reference)
    {
        $bill = Bill::where('advice_note_id', $reference)->get()->last();
        if ($bill) {
            $amountPaid = PaymentDetails::where('advice_note_id', $reference)->sum('amount_paid');
            $bill->update(['balance' => $bill->total_amount - $amountPaid]);
        }
    }
    // end of methode to update balance in bills table


}

//
