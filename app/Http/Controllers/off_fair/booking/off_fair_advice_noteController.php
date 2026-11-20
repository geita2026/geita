<?php

namespace App\Http\Controllers\off_fair\booking;

use App\Http\Controllers\Controller;
use App\Models\off_fair\advice_notes\off_fair_advice_notes;
use App\Models\off_fair\advice_notes\off_fair_booking_particulars;
use App\Models\off_fair\booking\booked_utilities;
use App\Models\off_fair\booking\off_fair_space_bookings;
use App\Models\off_fair\off_fair_spaces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Collections\Income;
use App\Models\companies;
use App\Models\Companies\CompanyAdmin;
use App\Models\setting\BankAccount;
use App\Models\setting\DefaultSettings;
use App\Traits\BillingTrait;
use Spatie\Permission\Models\Role;

class off_fair_advice_noteController extends Controller
{

    use BillingTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->user()->cannot('Manage Invoices', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        if (Auth::user()->account_type == 1) {
            $advice_notes = off_fair_advice_notes::leftJoin('off_fair_customers', 'off_fair_advice_notes.advice_note_owned_by', '=', 'off_fair_customers.customer_id')
                ->leftJoin('companies', 'off_fair_advice_notes.advice_note_owned_by', '=', 'companies.companyID')
                ->where('off_fair_advice_note_status', '!=', 0)
                // ->whereIn('companies.companyID', CompanyAdmin::where('adminId', Auth::user()->user_uuid)->get(['companyId']))
                ->orderBy('advice_note_created_at', 'DESC')
                ->paginate(config('global.DATA_LIMIT'), [
                    'off_fair_advice_note_id',
                    'off_fair_customers.customer_id',
                    'customer_name',
                    'off_fair_advice_notes.advice_note_id',
                    'off_fair_advice_note_deadline',
                    'control_number',
                    'total_amount',
                    'currency',
                    'balance',
                    'companyName'
                ]);
        }
        if (Auth::user()->account_type == 2) {
            $advice_notes = off_fair_advice_notes::leftJoin('off_fair_customers', 'off_fair_advice_notes.advice_note_owned_by', '=', 'off_fair_customers.customer_id')
                ->leftJoin('companies', 'off_fair_advice_notes.advice_note_owned_by', '=', 'companies.companyID')
                ->where('off_fair_advice_note_status', '!=', 0)
                ->whereIn('companies.companyID', CompanyAdmin::where('adminId', Auth::user()->user_uuid)->get(['companyId']))
                ->orderBy('advice_note_created_at', 'DESC')
                ->paginate(config('global.DATA_LIMIT'), [
                    'off_fair_advice_note_id',
                    'off_fair_customers.customer_id',
                    'customer_name',
                    'off_fair_advice_notes.advice_note_id',
                    'off_fair_advice_note_deadline',
                    'control_number',
                    'total_amount',
                    'currency',
                    'balance',
                    'companyName'
                ]);
        }

        return view('Off-Fair.Advice-Notes.off-fair-advice-note', compact('advice_notes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if ($request->user()->cannot('Manage Invoices', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            # ------ CHECKING CUSTOMER REGISTRATION ---- #
            $customer = companies::where('companyID', $request->customer_name)->where('sp_customer_no', null)->first();
            if ($customer) {
                $firstName = $customer->companyName;
                $phone_number = $customer->companyPhone;
                $email = $customer->companyEmail;
                $company_id = $request->customer_name;
                if (strlen($phone_number) != 12) {
                    return redirect()->route('company_profile', ['companyId' => $customer->registrationID])->with('fail', 'Please update your Phone number, remember to start with 255');
                }
                if (empty($email)) {
                    $email = 'tantrade@gmail.com';
                }
                if (config('global.APP_STATUS') == 1 && $customer->sp_customer_no == null) {
                     $this->customer_registration_api($company_id, $firstName, $phone_number, $email);
                }
            }

            # ------- CHECKING CUSTOMER REGISTRATION -----#

            $customer = companies::where('companyID', $request->customer_name)->first();
            if ($customer->registrationID != null) {
                $booking = off_fair_space_bookings::find($request->booking_id);
                $adviceNote = "INV-00" . date('y') . "-" . random_int(1000, 5000) . "-" . random_int(5001, 9999);
                $id = $adviceNote;
                $exipiredDate = $booking->start_date;
                if (date('Y-m-d', strtotime($exipiredDate)) <= date('Y-m-d')) {
                    $exipiredDate = '2025-02-01';
                }
                off_fair_advice_notes::create([
                    'off_fair_advice_note_id' => $adviceNote,
                    'advice_note_owned_by' => $request->customer_name,
                    'advice_note_generated_by' => Auth::user()->user_uuid,
                    'off_fair_advice_note_status' => 1,
                    'advice_note_id' => Str::uuid(),
                    'vat' => $request->vat,
                    'off_fair_advice_note_deadline' => $exipiredDate,
                    'off_fair_advice_note_status' => 2,
                ]);
                booked_utilities::where('user_id', Auth::user()->user_uuid)->where('advice_note_id', '=', Null)
                    ->update(['advice_note_id' => $id]);
                #------update booking insert advice_note_id---#
                off_fair_space_bookings::find($request->booking_id)->update(['advice_note_id' => $id]);
                # ----END-----#

                #************** CREATING PERTICULARS **********#
                $this->CreateParticulars($id);
                #********** END ************#
                #*************** CREATE BILL *******#
                $this->CreateBill($id); // creating bill
                #*************** END CREATE BILL *******#
                $this->GenerateInvoice($id); # Create Invoice
                return redirect()->route('Off-Fair-Advice-Notes.index')->with('success', 'Advice Note generated successfully!!');
            }
            return back()->with('fail', config('Messages.500'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('Manage Invoices', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $adviceNotes = off_fair_advice_notes::leftJoin('off_fair_customers', 'off_fair_advice_notes.advice_note_owned_by', '=', 'off_fair_customers.customer_id')
            ->leftJoin('companies', 'off_fair_advice_notes.advice_note_owned_by', '=', 'companies.companyID')
            ->leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
            ->where('off_fair_advice_note_id', $id)->get();

        $booking_dates = off_fair_space_bookings::get(['start_date', 'end_date', 'advice_note_id'])->where('advice_note_id', $id);

        $particulars = off_fair_advice_notes::leftJoin('off_fair_booking_particulars', 'off_fair_advice_notes.off_fair_advice_note_id', '=', 'off_fair_booking_particulars.advice_note_id')
            ->where('off_fair_advice_note_id', $id)->get();

        $defaultSettings = DefaultSettings::get();
        $inv = off_fair_advice_notes::leftJoin('users', 'off_fair_advice_notes.advice_note_attended_by', '=', 'users.user_uuid')
            ->leftJoin('roles', 'users.role', '=', 'roles.id')->where('off_fair_advice_note_id', $id)->first();

        $transfer = BankAccount::get(['account_number', 'account_name', 'swift_code', 'account_currency', 'bank_name']);

        return view('Off-Fair.Advice-Notes.printing-pages.printable-advice-note', compact(
            'adviceNotes',
            'particulars',
            'booking_dates',
            'defaultSettings',
            'transfer'
        ));
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if ($request->user()->cannot('Manage Invoices', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }


        // testing validit of deadline
        try {

            #****************APPROVE INVOICE****************#
            if ($request->approve_invoice == 1) {
                $this->GenerateInvoice($id); // generate invoice
                off_fair_advice_notes::find($id)->update(['off_fair_advice_note_status' => 3, 'advice_note_attended_by' => Auth::user()->user_uuid]);
            }
            #****************END APPROVE OFF FAIR INVOICE**********#

            return back()->with('success', config('global.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('global.500'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('Cancel Invoices', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $adviceNote = off_fair_advice_notes::find($id);

            #****************CHECK IF CONTROL NUMBER EXIST OR NOT*********#
            if ($adviceNote->control_number != null and config('global.APP_STATUS') == 1) {
                $reasons = $request->reasons;
                $status = $this->CancelOffFairInvoiceFromBilling($id, $reasons);
                if ($status == 0) {
                    return back()->with('fail', 'Failed to cancel control number!, please contact system Administrator');
                }
            }
            #***********END CHECKING***************#
            off_fair_space_bookings::where('advice_note_id', '=', $id)->delete();
            booked_utilities::where('advice_note_id', '=', $id)->delete();
            if ($request->action == 1) {
                $adviceNote->delete(); // delete completely from system
            } else {
                $adviceNote->update(['off_fair_advice_note_status' => 0]);
            }
            return redirect()->route('Off-Fair-Advice-Notes.index')->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }


    // create bill
    function CreateBill($id)
    {
        #---- OFF FAIR BILL CREATION ----#
        if (off_fair_advice_notes::where('off_fair_Advice_note_id', $id)->exists()) {

            $particulars1 = off_fair_booking_particulars::leftJoin('off_fair_advice_notes', 'off_fair_booking_particulars.advice_note_id', '=', 'off_fair_advice_notes.off_fair_advice_note_id')
                ->where('off_fair_booking_particulars.advice_note_id', $id)->get();

            $total_amount = 0;
            foreach ($particulars1 as $item) {
                $total_amount = $total_amount + ($item->particular_amount * $item->particular_quantity * $item->particular_duration);
                $currency = $item->type_of_currency;
            }

            $vat = 0; // default vat is zero.
            if ($item->vat == 1) {
                $vat = $total_amount * 0.18;
                $total_amount = $total_amount + $vat;
            } // VAT + AMOUNT
            off_fair_advice_notes::find($id)->update(['total_amount' => $total_amount, 'balance' => $total_amount, 'currency' => $currency, 'total_vat_amount' => $vat]);
        }
        #---- END OFF FAIR BILL CREATION ----#
    }

    function GenerateInvoice($id)
    {
        $particulars = off_fair_booking_particulars::leftJoin('off_fair_advice_notes', 'off_fair_booking_particulars.advice_note_id', '=', 'off_fair_advice_notes.off_fair_advice_note_id')
            ->leftJoin('companies', 'off_fair_advice_notes.advice_note_owned_by', 'companies.companyID')
            ->where('off_fair_booking_particulars.advice_note_id', $id)->get();
        foreach ($particulars as  $value) {
            $sp_customer_no = $value->sp_customer_no; // customer no from customer side.
            $total_amount = $value->total_amount; // total amount of the invoice
            $currency = $value->currency; // currency of the collection
            $vat = $value->vat; // contain VAT= 1, 0 does'nt contain VAT.
            $advice_note_deadline = date('d-m-Y', strtotime($value->off_fair_advice_note_deadline)); // invoice deadline
            $descriptions = 'Being charged for Off fair services contributions';
        }

        $without_vat = $total_amount - $value->total_vat_amount; // amount without VAT
        $items[] = ['item_code' => $value->gfs_code, 'amount' => $without_vat]; // get item in array

        $data = array(
            'customer_no' => $sp_customer_no,
            'amount' => $without_vat,
            'currency' => $currency,
            'reference' => $id,
            'withholding_tax' => 0,
            'taxable' => $vat,
            'payment_mode' => 2,
            'expire_date' => $advice_note_deadline,
            'items' => $items,
            'description' => $descriptions,
        );

        $this->RequestInvoiceToBilling($id, $data); // send data array to request invoice
    }



    #***********************UPDATE BOOKED UTILITIES****************#
    function BookedUtilities($id, $status)
    {
        // update booked utility table to insert advicenote for that booking
        $value = booked_utilities::get(['utility_booking_id', 'user_id', 'advice_note_id'])
            ->where('user_id', Auth::user()->user_uuid)->where('advice_note_id', '=', Null);
        foreach ($value as $key) {
            $utility = booked_utilities::find($key->utility_booking_id);
            $utility->update(['advice_note_id' => $id, 'booking_status' => $status]);
        }
        // end updating advice note to booked utilities
    }
    #****************END UPDATE BOOKED UTILITIES*****************#


    #****************************** CREATE PARTICULARS ***********************#
    function CreateParticulars($id)
    {
        $gfs_code = Null;
        #**************** insert all perticulars to particular table **************** #.
        $booking = off_fair_space_bookings::leftJoin('companies', 'off_fair_space_bookings.off_fair_customer_id', '=', 'companies.companyID')
            ->leftJoin('off_fair_spaces', 'off_fair_space_bookings.off_fair_space_id', '=', 'off_fair_spaces.space_id')
            ->leftJoin('off_fair_activities', 'off_fair_space_bookings.customer_activity', '=', 'off_fair_activities.activity_id')
            ->leftJoin('space_size_category_costs', 'off_fair_spaces.space_category_cost', '=', 'space_size_category_costs.space_size_category_cost_id')
            ->where('advice_note_id', '=', $id)
            ->first([
                'companies.companyID',
                'off_fair_space_bookings.off_fair_space_id',
                'companyName',
                'space_name',
                'space_cost',
                'off_fair_booking_id',
                'space_id',
                'start_date',
                'end_date',
                'activity_name',
                'off_fair_customer_id',
                'space_size',
                'off_fair_space_bookings.advice_note_id',
                'space_category',
                'activity_cost',
                'customer_activity',
                'space_category',
                'size_range_cost',
                'sales_status',
                'durationCount'
            ]);

        if ($booking) {
            if (session('month') == 1) { // Tenants
                $gfs_code = Income::where('is_joint_payment', 1)->first()->gfs_code;
                #for religious.
                if ($booking->customer_activity == 'a9944c4a-e446-4474-80b3-609a2822fe97') // for religious activities
                {
                    $space_cost = $booking->activity_cost; # if activity is religious services.
                } else {
                    $space_cost = $booking->space_cost;
                }
            }
            # end of tenants

            # for  Functional Halls
            else {
                $gfs_code = Income::where('is_joint_payment', 2)->first()->gfs_code;
                $space_cost = $booking->space_cost;
            }
            if ($gfs_code == null) {
                return 0;
            }
            off_fair_booking_particulars::create([
                'particular_id' => Str::uuid(),
                'particular_name' => $booking->space_name . ' (Booked Space)',
                'particular_amount' => $space_cost,
                'type_of_currency' => 'TZS',
                'particular_quantity' => $booking->space_size,
                'particulars_approved_by' => Auth::user()->user_uuid,
                'advice_note_id' => $id,
                'particular_duration' => $booking->durationCount,
                'gfs_code' => $gfs_code,
            ]);
            # end of inserting data into particulars table.    
        }

        #  for  utilities
        $utilities = booked_utilities::leftJoin('off_fair_utilities', 'booked_utilities.booked_utility_id', '=', 'off_fair_utilities.utility_id')
            ->where('advice_note_id', $id)
            ->get(['booked_utility_id', 'utility_name', 'utility_cost', 'utility_priolity', 'advice_note_id', 'booked_utilities.user_id']);

        foreach ($utilities as $value) {
            off_fair_booking_particulars::create([
                'particular_id' => Str::uuid(),
                'particular_name' => $value->utility_name,
                'particular_amount' => $value->utility_cost,
                'type_of_currency' => 'TZS',
                'particular_quantity' => 1,
                'particulars_approved_by' => Auth::user()->user_uuid,
                'advice_note_id' => $id,
                'particular_duration' => $booking->durationCount,
                'gfs_code' => $gfs_code,
            ]);
        }
        // end inserting
    }
    #*******************END PERTICULARS*****************************#

    #************************CANCEL INVOICE FROM BILLING SYSTEM ******************#
    function CancelOffFairInvoiceFromBilling($id, $reasons)
    {
        $value = off_fair_advice_notes::leftJoin('companies', 'off_fair_advice_notes.advice_note_owned_by', '=', 'companies.companyID')
            ->where('off_fair_advice_note_id', $id)->first(); // get bill relating to invoice id
        if ($value) {
            $customer_no = $value->sp_customer_no;
            $invoice_no = $value->sp_invoice_no;
            $canceledby = Auth::user()->first_name . " " . Auth::user()->last_name;
            $reference = $id;
            $data = array('customer_no' => $customer_no, 'invoice_no' => $invoice_no, 'reference' => $reference, 'cancelledby' => $canceledby, 'reason' => $reasons); // data array
            $status = $this->CancelInvoiceFromSp($data, $id); // if their is control number in invoice, cancel control number first from billing system.
            return $status;
        }
        return 0;
    }
}
