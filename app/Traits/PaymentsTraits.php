<?php
namespace App\Traits;
use App\Models\Collections\Bill;
use App\Models\Collections\PaymentDetails;
use Illuminate\Support\Str;
use App\Traits\BillingTrait;
use App\Models\Collections\OffFairBill;

trait PaymentsTraits{
    use BillingTrait;
   public function PostPayments($reference,$contro_number,$amount_paid,$currency,$receipt,$bank_receipt,$gate_way_receipt,$trans_date,$bank_account){
        try {

        $bill=Bill::where('advice_note_id',$reference)->get();
        foreach ($bill as $value) {
            $bill_id=$value->bill_id;
            PaymentDetails::create([
                'payment_id'=>Str::uuid(),
                'bill_paid'=>$bill_id,
                'advice_note_id'=>$reference,
                'control_number'=>$contro_number,
                'amount_paid'=>$amount_paid,
                'currency'=>$currency,
                'receipt'=>$receipt,
                'bank_receipt'=>$bank_receipt,
                'gateway_receipt'=>$gate_way_receipt,
                'trans_date'=>$trans_date,
                'bank_account'=>$bank_account,
            ]);
            $this->updateBillWithPayments($reference); // update balance in bill table
            return 1;

        }

         // for off fair payments
         $off_fair_bills=OffFairBill::where('advice_note_id',$reference)->get();
         foreach ($off_fair_bills as $value) {
             $bill_id=$value->bill_id;
             PaymentDetails::create([
                 'payment_id'=>Str::uuid(),
                 'off_fair_bill_paid'=>$bill_id,
                 'advice_note_id'=>$reference,
                 'control_number'=>$contro_number,
                 'amount_paid'=>$amount_paid,
                 'currency'=>$currency,
                 'receipt'=>$receipt,
                 'bank_receipt'=>$bank_receipt,
                 'gateway_receipt'=>$gate_way_receipt,
                 'trans_date'=>$trans_date,
                 'bank_account'=>$bank_account,
             ]);

             $this->updateBillWithPayments($reference); // update balance in bill table
             return 1;
            }

            return 0;

    } catch (\Throwable $th) {
        //throw $th;
      return 0;
    }

    }

}
#
