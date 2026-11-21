<?php

namespace App\Traits;

use App\Models\advice_note\advice_notes;
use App\Models\advice_note\canceled_advice_notes;
use App\Models\companies;
use App\Traits\BadgesTraits;
use App\Traits\IntergrationTraits;
use App\Traits\ExhibitorTrait;
use App\Traits\EmailNotification;
use Illuminate\Support\Str;

trait AutomaticActionTraits
{
    use BadgesTraits;
    use IntergrationTraits;
    use ExhibitorTrait;
    use EmailNotification;
    public function AutocancelationOfExpiredInvoices($id)
    {
        $reasons = 'The system has automatically canceled this invoice upon expiration as per the regulations of the Dar es Salaam International Trade Fair.';
        if (config('global.APP_STATUS') == 1) {
            $value = advice_notes::leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->where('advice_note_id', $id)
                ->first();
            $customer_no = $value->sp_customer_no;
            $invoice_no = $value->sp_invoice_no;
            $canceledby = 'GeMEMIS SYSTEM';
            $reference = $id;
            $data = array('customer_no' => $customer_no, 'invoice_no' => $invoice_no, 'reference' => $reference, 'cancelledby' => $canceledby, 'reason' => $reasons); // data array
            if ($value->control_number != Null) {
                $status = $this->CancelInvoiceFromSp($data, $id); // if their is control number in invoice, cancel control number first from billing system.
            } else {
                $status = 1;
            }
        } else {
            $status = 1;
        }
        if ($status == 1) {
            $invoice = advice_notes::find($id);
            $invoice->update(['advice_note_status' => 'CANCELED']);
            canceled_advice_notes::create(['id' => Str::uuid(), 'advice_note_id' => $id, 'canceled_by' => "c5017429-4638-4508-8fa9-14890a37688c", 'reasons' => $reasons]);
            $this->RemoveAdviceNoteFromList($id);
            $this->ClearBadgeAllocated($id);
            #----- SEND NOTIFICATION TO CUSTOMER ----#
            if (config('global.APP_STATUS') == 1) {
                $company = companies::where('companyID', $invoice->company_id)
                    ->where('companyEmail', '!=', Null)->first();
                if ($company) {
                    $emailTo = $company->companyEmail;
                    $linkLable = 'View Canceled Invoice';
                    $receiverName = strtoupper($company->companyName);
                    $regardsName = 'Space Allocation Committee Team';
                    $emailSubject = 'BOOKING CANCELLATION NOTIFICATION';
                    $emailLink = env('APP_URL') . '/accountingManagement/adviceNotes/' . $id;
                    $emailContent = '
        Kindly, we are writing to inform you that your booking [ ' . $invoice->advice_note_id .' (' .number_format($invoice->total_amount,2).' '.$invoice->currency.') ] Has been canceled due to an overdue payment. As per our policy, we require all payments to be completed by the deadline of ' . date('M d, Y', strtotime($invoice->advice_note_deadline)) . '.
         Now the Space(s) or Service(s) is available for others to Book.';
                    $this->sendEmail($emailTo, $linkLable, $receiverName, $emailContent, $regardsName, $emailSubject, $emailLink);
                }
                #----- SEND NOTIFICATION TO CUSTOMER -----#
            }
            return $status;
        } else {
            return 0;
        }
    }
}
