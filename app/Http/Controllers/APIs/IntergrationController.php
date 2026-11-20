<?php
/*
    |  ******************* INTERGRATION FILE CONTROLLER *****************
    |
    | This file define all endpoint logics for posting
    |-->All gfs Codes created from Billing system
    |-->All control numbers from billing system
    |-->All payments being made.
    |
    */

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use Illuminate\Http\Request;
use App\Models\Collections\Income;
use App\Models\Collections\PaymentDetails;
use App\Models\ePermit\ApplicationInvoice;
use App\Models\ePermit\ePermitApplication;
use App\Models\off_fair\advice_notes\off_fair_advice_notes;
use App\Traits\CollectionSettingTraits;
use App\Traits\APITraits;
use App\Traits\NotificationTrait;
use Illuminate\Support\Str;

class IntergrationController extends Controller
{
    use  CollectionSettingTraits;
    use APITraits;
    use NotificationTrait;


    # ************  POST GFSCODE FUNCTION **************#
    function PostGfsCode(Request $request)
    {
        try {
            $request_name = $request->response['sp_code'];
            $token = $request->response['token'];
            $status = $this->AuthenticateRequest($request_name, $token);
            if ($status == 0) {
                return response()->json([
                    'code' => '401',
                    'message' => 'You are not Authenticated to Access this Resource!'
                ]);
            }

            foreach ($request->data as $key) {
                $gfs_code = $key['item_code'];
                $income_name = $key['name'];
                $gfs_codes = Income::get();
                foreach ($gfs_codes as $value) {
                    if ($gfs_code == $value->gfs_code) {
                        continue;
                    }
                }

                $this->SaveNewGfsCode($gfs_code, $income_name); // method to register new Iterm

            }

            return response()->json([
                'code' => 200,
                'message' => 'Item added successfully!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => 'Something went wrong!'
            ]);
        }
    }
    # ******** END POST GFSCODE FUNCTION ***********#



    # ********* POST CONTROL NUMBER FUNCTION *******#
    function PostControlNumber(Request $request)
    {
        try {
            $request_name = $request->response['sp_code'];
            $token = $request->response['token'];
            $status = $this->AuthenticateRequest($request_name, $token);
            if ($status == 0) {
                $response = array('code' => 401, 'message' => 'ERROR OCCURED');
                return response()->json(['response' => $response]);
            }

            // $invoice_no=$request->data['invoice_no']; // service provider invoice ID.
            $control_no = $request->data['control_number']; // control no for the invoice.
            $reference = $request->data['reference']; // reference ID.

            #------- DITF INVOICES POST CONTROL NUMBER API------#
            if (advice_notes::where('advice_note_id', $reference)->exists()) {
                advice_notes::find($reference)->update(['control_number' => $control_no]); // update advice note set control number
                $this->SentControlNoNotification($reference); //this methode send control number to user(BulkSms).
                // $this->sendControlNoToCustomer($reference); // send control number to user (Email).
                $response = array('code' => 200, 'message' => 'Request Processed successfully!');
                return response()->json(['response' => $response]); // return response to Service Provider
            }
            #------- END DITF INVOICES POST CONTROL NUMBER API------#

            #------- OFF FAIR INVOICES POST CONTROL NUMBER API------#
            if (off_fair_advice_notes::where('off_fair_advice_note_id', $reference)->exists()) {
                off_fair_advice_notes::find($reference)->update(['control_number' => $control_no]); // set control no to invoice
                $this->SentControlNoNotification($reference); //this methode send control number to user(BulkSms).
                $response = array('code' => 200, 'message' => 'Request Processed successfully!');
                return response()->json(['response' => $response]); // return response to Service Provider
            }
            #------- END OFF FAIR INVOICES POST CONTROL NUMBER API------#


            #---- e_Permit Invoices ----- #
            if (ApplicationInvoice::where('invoiceNo', $reference)->exists()) {
                ApplicationInvoice::find($reference)->update([
                    'controlNumber' => $control_no,
                ]);
                $this->SentControlNoNotification($reference); //SMS
                $response = array('code' => 200, 'message' => 'Request Processed successfully!');
                return response()->json(['response' => $response]);
            }
            #---- e_Permit Invoices ----- #

            $response = array('code' => 404, 'message' => 'Invoice does not exist');
            return response()->json(['response' => $response]); // return response to Service Provider
        } catch (\Throwable $th) {
            $response = array('code' => 500, 'message' => 'Something went wrong!' . $th->getMessage());
            return response()->json(['ressponse' => $response]);
        }
    }
    # ******* END POST CONTROL NUMBER FUNCTION ********#




    # ******* POST PAYMENTS FUNCTION **********#
    function PostPayments(Request $request)
    {
        try {

            $request_name = $request->response['sp_code'];
            $token = $request->response['token'];
            $status = $this->AuthenticateRequest($request_name, $token);
            if ($status == 0) {
                $response = array('code' => 401, 'message' => 'You are not Authenticated to Access this Resource!');
                return response()->json([
                    'response' => $response,
                ]);
            }

            // values from billing
            $reference = $request->data['reference'];
            $receipt = $request->data['receipt'];
            $bank_receipt = $request->data['bank_receipt'];
            $gate_way_receipt = $request->data['gateway_receipt'];
            $amount_paid = $request->data['amount'];
            $contro_number = $request->data['control_number'];
            $trans_date = $request->data['trans_date'];
            $bank_account = $request->data['bank_account'];
            $currency = $request->data['currency'];
            // end values from billing
            $payments = PaymentDetails::where('receipt', $receipt)->first();
            if (!$payments) {
                PaymentDetails::create([
                    'payment_id' => Str::uuid(),
                    'referenceId' => $reference,
                    'control_number' => $contro_number,
                    'amount_paid' => $amount_paid,
                    'currency' => $currency,
                    'receipt' => $receipt,
                    'bank_receipt' => $bank_receipt,
                    'gateway_receipt' => $gate_way_receipt,
                    'trans_date' => $trans_date,
                    'bank_account' => $bank_account,
                ]);
                $status = $this->UpdatePayments($reference,$amount_paid); // update payments in invoices
                if ($status == 1) {
                    $this->PaymentNotification($reference, $amount_paid); // Notify user when paid.
                    return response()->json([
                        'response' => array('code' => 200, 'message' => 'Payments posted successfully!'),
                    ]);
                }
            } elseif ($payments) {
                $response = array('code' => 200, 'message' => 'Dublicate payments');
                return response()->json(['response' => $response]);
            }
        } catch (\Throwable $th) {
            $response = array('code' => 500, 'message' => 'Something went wrong!');
            return response()->json([
                'response' => $response,
            ]);
        }
    }

    #update payments to database
    function UpdatePayments($reference,$amount_paid)
    {
        #--- UPDATE PAYMENTS IN INFAIR INVOICES---#
        if (advice_notes::where('advice_note_id', $reference)->exists()) {
            $invoice = advice_notes::where('advice_note_id', $reference)->first();
            $total_amount = $invoice->total_amount;
            $amount_paid = 0;
            $payments = PaymentDetails::where('referenceId', $reference)->get();
            foreach ($payments as $item) {
                $amount_paid = $amount_paid + $item->amount_paid;
            }
            $balance = $total_amount - $amount_paid;
            $invoice->update(['amount_paid' => $amount_paid, 'balance' => $balance]);
            return 1;
        }
        #---END UPDATE PAYMENTS IN INFAIR INVOICES---#

        #--- UPDATE PAYMENTS IN OFF FAIR INVOICES---#
        if (off_fair_advice_notes::where('off_fair_advice_note_id', $reference)->exists()) {
            $invoice = off_fair_advice_notes::where('off_fair_advice_note_id', $reference)->first();
            $total_amount = $invoice->total_amount;
            $amount_paid = 0;
            $payments = PaymentDetails::where('referenceId', $reference)->get();
            foreach ($payments as $item) {
                $amount_paid = $amount_paid + $item->amount_paid;
            }
            $balance = $total_amount - $amount_paid;
            $invoice->update(['amount_paid' => $amount_paid, 'balance' => $balance]);
            return 1;
        }
        #--- END UPDATE PAYMENTS IN OFF FAIR INVOICES---#


        if (ApplicationInvoice::where('invoiceNo', $reference)->exists()) {
            $invoice=ApplicationInvoice::find($reference);
            $invoice->update([
                'amountPaid' => $amount_paid,
            ]);
            #---- ISSUE CERTIFICATE ----- #
            ePermitApplication::find($invoice->referenceApplication)
            ->update([
                'applicationStatus'=>6,
                'DateOfIssue'=>date('Y-m-d h:i:s'),
                'permitNo'=>'TP-0'.date('y-').random_int(100,999),
            ]);
            return 1;
        }
        return 0;
    }

    // function to update balance in bills table
    function updateBillWithPayments($reference)
    {
        $invoice = advice_notes::where('advice_note_id', $reference)->get();
        foreach ($invoice as $key) {
            $total_amount = $key->total_amount;
        }
        $amount_paid = 0;
        $payments = PaymentDetails::where('advice_note_id', $reference)->get();
        foreach ($payments as $item) {
            $amount_paid = $amount_paid + $item->amount_paid;
        }
        $balance = $total_amount - $amount_paid;
        advice_notes::find($reference)->update(['amount_paid' => $amount_paid, 'balance' => $balance]); // update invoice with amount paid
    }
    // end of methode to update balance in bills table



    # ******* END POST PAYMENTS FUNCTION *********#






}
