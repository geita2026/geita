<?php

namespace App\Traits;

use App\Models\advice_note\advice_notes;
use App\Models\Collections\OffFairBill;
use App\Models\ePermit\ApplicationInvoice;
use App\Models\Notifications\BulkSms;
use App\Traits\BulkSmsTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait NotificationTrait
{
    use BulkSmsTrait;

    function Notification($recipients, $sms_content, $datetime)
    {
        try {
            $message = array('recipients' => $recipients, 'message' => $sms_content, 'datetime' => $datetime, 'sender_id' => 'TANTRADE', 'mobile_service_id' => 35);
            $json_data = json_encode($message); // creating json data.
            $this->sendQuickSms(array('data' => $json_data, 'datetime' => $datetime));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail', 'something went wrong, it might be due to internect connection problems');
        }
    }


    // password notefication after new account created
    public function EmailPasswordNotification($email, $first_name, $last_name, $default_password)
    {
        $email_data = [
            'fromName' => config('app.name'),
            'email' => $email,
            'name' => $first_name . " " . $last_name,
            'password' => $default_password,
            'body' => 'Your reminded to change password after first login.',
            'subject' => 'PASSWORD NOTIFICATION',
        ];
        Mail::send('mails.default-password', $email_data, function ($message) use ($email_data) {
            $message
                ->to($email_data['email'], $email_data['name'])
                ->priority(3)
                ->subject($email_data['subject']);
        });
    }
    // end password notification.

    function SendMsg()
    {
        // send sms to user
        $messages = BulkSms::where('sms_status', 0)->get();
        foreach ($messages as $key) {
            $recipients = $key->phone_number;
            $datetime = date('Y-m-d H:i:s');

            // sms to be sent
            $sms_content =
                "Dear, " . $key->customer_name . ",
" . $key->sms_body;
            //end of sms to be sent

            $this->Notification($recipients, $sms_content, $datetime); // send sms methode
            $sms = BulkSms::find($key->sms_id);
            $sms->update(['sms_status' => 1,]); // after sennding sms change status to 1;
        }
    }


    # *********** RESERT PASWORD EMAIL NOTIFICATION ************ #
    function PasswordResertPasswordMail($email, $first_name, $last_name, $password, $id)
    {
        $email_data = [
            'fromName' => config('app.name'),
            'email' => $email,
            'name' => $first_name . ' ' . $last_name,
            'password' => $password,
            'subject' => 'PASSWORD NOTIFICATION'
        ];

        Mail::send('mails.password', $email_data, function ($message) use ($email_data) {
            $message
                ->to($email_data['email'], $email_data['name'])
                ->priority(1)
                ->subject($email_data['subject']);
        });
    }
    # *********** END RESERT PASSWORD EMAIL NOTIFICATION ********* #

    #************* CONTROL NUMBER NOTIFICATIONS
    function SentControlNoNotification($reference)
    {
        #********************* DITF INVOICE ****************#
        $invoice = advice_notes::leftJoin('companies', 'company_id', '=', 'companies.companyID')->where('advice_note_id', $reference)->first();
        if ($invoice) {
            $phone_number = $invoice->companyPhone;
            $customer_name = $invoice->companyName;
            $amount = number_format($invoice->total_amount, 2);
            $currency = $invoice->currency;
            $control_no = $invoice->control_number;
            $deadline = date('d-m-Y 00:00:00', strtotime($invoice->advice_note_deadline));
            $app_url = env('APP_URL') . '';
            $registrationID = $invoice->registrationID;
            #------------ MESSAGE -----------#

            $sms_content = "You have been invoiced for participating in 50th DITF,
Control No: $control_no,
Amount: $amount $currency,
Deadline: $deadline,
Your TSRN: $registrationID";
            #-------------- END MESSAGE ---- #
        }

        #***************** OFF FAIR NOTIFICATION ********#
        $invoices = OffFairBill::leftJoin('off_fair_customers', 'off_fair_bills.customer_id', '=', 'off_fair_customers.customer_id')
            ->leftJoin('off_fair_advice_notes', 'off_fair_bills.advice_note_id', '=', 'off_fair_advice_notes.off_fair_advice_note_id')
            ->where('off_fair_bills.advice_note_id', $reference)->first();
        if ($invoices) {
            $phone_number = $invoices->customer_phone;
            $customer_name = $invoices->customer_name;
            $amount = number_format($invoices->total_amount, 2);
            $control_no = $invoices->control_number;
            $currency = $invoices->currency;
            $deadline = date('d-m-y h:i:s', strtotime($invoices->off_fair_advice_note_deadline));
            $app_url = env('APP_URL');

            #------------ MESSAGE -----------#
            $sms_content = "You have been invoiced for Off fair Rental services,
Control No: $control_no,
Total Amount: $amount $currency,
Deadline: $deadline,
#Sabasaba Kidijitali";
            #-------------- END MESSAGE ---- #
        }
        #*************** END OFF FAIR ****************#

        if (ApplicationInvoice::where('invoiceNo', $reference)->exists()) {
            $invoice = ApplicationInvoice::leftJoin('companies', 'application_invoices.customerId', '=', 'companies.companyID')
                ->where('invoiceNo', $reference)->first();
            $phone_number = $invoice->companyPhone;
            $customer_name = $invoice->companyName;
            $sms_content = "You have been invoiced for Event Permit.
Control No: $invoice->controlNumber,
Amount: $invoice->invoiceAmount $invoice->currency.";
        }

        $sent_on = date('Y-m-d');
        if ($phone_number != Null) {
            $this->SaveSendSmsJobs($phone_number, $sms_content, $sent_on, $customer_name);
        }
    }


    #Email Notification
    function sendControlNoToCustomer($id)
    {
        // Item  booked
        $particulars = advice_notes::leftJoin('particulars', 'advice_notes.advice_note_id', '=', 'particulars.advice_note_id')
            ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
            ->where('advice_notes.advice_note_id', $id)
            ->get(
                [
                    'particulars.advice_note_id',
                    'companyName',
                    'particular_name',
                    'cost_for_one_particular',
                    'particular_quantity',
                    'type_of_currency',
                    'particular_category',
                    'particular_description',
                    'particular_type',
                    'contain_vat'
                ]
            ); // end

        $adviceNotes = advice_notes::leftJoin('companies', 'companies.companyID', '=', 'advice_notes.company_id')
            ->leftJoin('company_details', 'advice_notes.company_id', '=', 'company_details.companyID')
            ->leftJoin('users', 'advice_notes.user_id', '=', 'users.user_uuid')
            ->leftJoin('roles', 'users.role', '=', 'roles.id')
            ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
            ->where('advice_notes.advice_note_id', $id)
            ->get([
                'advice_notes.advice_note_id',
                'companyName',
                'advice_note_created_at',
                'companyAddress',
                'companyLocation',
                'companyPhone',
                'personalPhone',
                'first_name',
                'last_name',
                'companyEmail',
                'roles.name',
                'advice_note_deadline',
                'advice_note_description',
                'tinNo',
                'users.user_signature',
                'registrationNo',
                'advice_note_status',
                'advice_notes.updated_at',
                'event_name',
                'event_descriptions',
                'vat',
                'total_amount',
                'currency',
                'control_number'
            ]);

        foreach ($adviceNotes as  $value) {
            $email = $value->companyEmail;
            // $email=$value->personalEmail;
            $name = $value->companyName;
        }

        $email_data = [
            'fromName' => config('app.name'),
            'email' => $email,
            'name' => $name,
            'adviceNotes' => $adviceNotes,
            'particulars' => $particulars,
            'subject' => 'CONTROL NUMBER',
        ];
        Mail::send('mails.invoice', $email_data, function ($message) use ($email_data) {
            $message
                ->to($email_data['email'], $email_data['name'])
                ->priority(3)
                ->subject($email_data['subject']);
        });
    }

    # ************** END CONTROL NUMBER NOTIFICATIONS ********** #



    // saving bulk sms to jobs
    function SaveSendSmsJobs($phone_number, $sms_content, $sent_on, $customer_name)
    {
        BulkSms::where('phone_number', $phone_number)
            ->delete();
        BulkSms::create([
            'sms_id' => Str::uuid(),
            'phone_number' => $phone_number,
            'sms_body' => $sms_content,
            'sms_sent_on' => $sent_on,
            'customer_name' => $customer_name,
        ]);
        // $this->SendMsg();
    }

    #**************************PAYMENT NOTFICATION************************#
    public function PaymentNotification($reference, $amount_paid)
    {
        #************* PAYMENT NOTIFICATIONS ******************#
        $invoice = advice_notes::leftJoin('companies', 'company_id', '=', 'companies.companyID')->where('advice_note_id', $reference)->get();
        foreach ($invoice as $value) {
            $phone_number = $value->companyPhone;
            $customer_name = $value->companyName;
            $balance = number_format($value->balance, 2);
            $currency = $value->currency;
            $registrationID = $value->registrationID;
            $amount_paid = number_format($amount_paid, 2);

            // sms to be sent
            $sms_content = "Confirmed! we have received $amount_paid $currency,
Your new Balance is: $balance $currency.
Your TSRN: $registrationID";
            $sent_on = date('Y-m-d');
            if ($phone_number != null) {
                $this->SaveSendSmsJobs($phone_number, $sms_content, $sent_on, $customer_name); // saving message to jobs
            }
            //end of sms to be sent

            #*************END PAYMENT NOTIFICATIONS ******************#
        }

        #----e_Permit Payment Notification -----#
        if (ApplicationInvoice::where('invoiceNo', $reference)->exists()) {
            $invoice = ApplicationInvoice::leftJoin('companies', 'application_invoices.customerId', '=', 'companies.companyID')
                ->where('application_invoices.invoiceNo', $reference)->first();
            $phone_number = $invoice->companyPhone;
            $customer_name = $invoice->companyName;
            $currency = $invoice->currency;
            $amount_paid = number_format($amount_paid, 2);
            $sms_content = "Confirmed! we have received $amount_paid $currency.
Visit your account to download your event Permit, and view the assigned Officer for any Help";
            $sent_on = date('Y-m-d');
            if ($phone_number != null) {
                $this->SaveSendSmsJobs($phone_number, $sms_content, $sent_on, $customer_name); // saving message to jobs
            }
        }

    }


    public function CheckBalance()
    {
        $adviceNotes = advice_notes::leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
            ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')->where('event_status', 1)
            ->where('balance', '>', 0)->where('advice_note_id', 'ADV-0023-9691-4661')
            ->where('advice_note_status', '!=', 0)->get();
        foreach ($adviceNotes as $value) {
            if ($value->new_deadline != null) {
                $deadline = date('d-m-Y', strtotime($value->new_deadline));
            } else {
                $deadline = date('d-m-Y', strtotime($value->advice_note_deadline));
            }
            $phone_number = $value->companyPhone;
            $sms_content = 'We are remaining you to complete your Payments of ' . $value->balance . ' ' . $value->currency . ' Before ' . $deadline . '23:59' . '
                For more information visit tradefair.tantrade.go.tz for more information';
            $sent_on = date('Y-m-d');
            $customer_name = $value->companyName;
            $this->SaveSendSmsJobs($phone_number, $sms_content, $sent_on, $customer_name); // saving message to jobs
        }
    }
}
