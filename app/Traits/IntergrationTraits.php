<?php

namespace App\Traits;

use App\Models\advice_note\advice_notes;
use App\Models\Logs\ApiLogs;
use Illuminate\Support\Facades\Http;
use App\Models\off_fair\customers\off_fair_customers;
use App\Models\companies;
use App\Models\ePermit\ApplicationInvoice;
use App\Models\off_fair\advice_notes\off_fair_advice_notes;
use App\Traits\APITraits;
use App\Traits\NotificationTrait;
use Google\Service\AndroidManagement\Application;
use Illuminate\Support\Str;

trait IntergrationTraits
{

  /*
    |
    |----------- GeMEMIS - Geita Mineral Exhibition Management Information System INTERGRATION FILE #####
    |----------- This file depends APITraits.php file for remote authentications #####
    */

  use APITraits;
  use NotificationTrait;

  # ----------  CUSTOMER REGISTRATION APIS -----------#
  function customer_registration_api($company_id, $firstName, $phone_number, $email)
  {
    $id = $company_id;
    $url = $this->APIs()['base_url'] . 'api_add_customer'; // get invoice api end point
    $auth = $this->APIs()['auth']; // authentication details (code & token)

    $data = array('sp_customer_no' => $company_id, 'customer_name' => $firstName, 'mobile' => $phone_number, 'email' => $email, 'currency' => 'TZS'); // data array

    #----- Register Demo to Remote ----- #
    if (config('global.APP_STATUS') == 1) {
      $response = Http::withOptions(["verify" => false])->post($url, ['auth' => $auth, 'data' => $data,]);
      ApiLogs::create(['response' => $response, 'reminder' => $company_id]); // save the response for moment
      $customer_no = $response['data']['customer_no']; // customer no from billing (response results)
      $id = $response['data']['sp_customer_no']; // company id (Response results)
      
      #------- for Other customers------#
      if (companies::where('companyID', $id)->exists()) {
        companies::find($id)->update(['sp_customer_no' => $customer_no]); // update company to set its for DITF customers
        return 1;
      }
      #------ end-------#

    }
  }
  #------ register customer-------#


  //Requesting invoice number from billing (id is advice note id), then after it will post control number respect to invoice given
  function RequestInvoiceToBilling($id, $data)
  {
    $control_no = Null;
    if (config('global.APP_STATUS') == 0) {
      $id = $id;
      $invoice_no = 'DEMO-' . random_int(1000, 5000); // Dammy invoice Id
      $control_no = '99967382' . random_int(1000, 9999);
      $reference = $id;
    } elseif (config('global.APP_STATUS') == 1) {
      $url = $this->APIs()['base_url'] . 'api_customer_invoice'; // get invoice api end point
      $auth = $this->APIs()['auth']; // authentication details (code & token)
      $response = Http::withOptions(["verify" => false])->post($url, ['auth' => $auth, 'data' => $data]); // sending Request to API
      ApiLogs::create(['response' => $response, 'reminder' => $id]); // save the response for moment
      $id = $response['data']['reference'];
      $invoice_no = $response['data']['invoice_no'];
    }

    #--- IN FAIR INVOICE INVOICE UPDATE ---#
    if (advice_notes::where('advice_note_id', $id)->exists()) {
      advice_notes::find($id)->update([
        'sp_invoice_no' => $invoice_no,
        'control_number' => $control_no,
      ]);
    }
    #--- END IN FAIR INVOICE INVOICE UPDATE ---#

    #--- OUT FAIR INVOICE INVOICE UPDATE ---#
    if (off_fair_advice_notes::where('off_fair_advice_note_id', $id)->exists()) {
      off_fair_advice_notes::find($id)->update([
        'sp_invoice_no' => $invoice_no,
        'control_number' => $control_no,
      ]);
    }
    #--- END OUT FAIR INVOICE INVOICE UPDATE ---#

    #------ e_Permit Invoices ---- #
    if (ApplicationInvoice::where('invoiceNo', $id)->exists()) {
      ApplicationInvoice::find($id)->update([
        'spInvoiceNo' => $invoice_no,
        'controlNumber' => $control_no,
      ]);
    }

    $log = ApiLogs::where('reminder', $id)->get();
    foreach ($log as $key) {
      ApiLogs::find($key->id)->delete(); // delete log if request successfully!.
    }
  }
  // end of requesting invoices from billing

  # ----- CANCEL INVOICE ---- #
  function CancelInvoiceFromSp($data, $id)
  {
    $url = $this->APIs()['base_url'] . 'api_cancel_invoice'; // Cancel invoice api end point
    $auth = $this->APIs()['auth']; // authentication details (code & token)
    $response = Http::withOptions(["verify" => false])->post($url, ['auth' => $auth, 'data' => $data]);
    ApiLogs::create(['response' => $response, 'reminder' => $id]); // save the response for moment
    if ($response['response']['code'] == 200) {
      ApiLogs::where('reminder', $id)->delete();
      return 1;
    } elseif ($response['response']['code'] == 116) {
      ApiLogs::where('reminder', $id)->delete();
      return 1;
    } else {
      return 0;
    }
  }
  # ----- END CANCEL INVOICE ---- #




  # --------- CONTROL NUMBER REUSE ------- #
  function ReuseControlNumber($id, $data)
  {
    $url = $this->APIs()['base_url'] . 'api_reuse_control_number'; // extend deadlin API end point
    $auth = $this->APIs()['auth'];

    $response = Http::withOptions(["verify" => false])->post($url, ['auth' => $auth, 'data' => $data]);

    ApiLogs::create(['response' => $response, 'reminder' => $id]); // save the response for moment
    if ($response['response']['code'] == 200) {
      $log = ApiLogs::where('reminder', $id)->get();
      foreach ($log as $key) {
        ApiLogs::find($key->id)->delete(); // delete log if request successfully!.
      }
      return 1;
    } // successfully excuted

    else {
      return 0;
    } // something went wrong

  }
  # ----- END CONTROL NUMBER REUSE ---- #


}
#
