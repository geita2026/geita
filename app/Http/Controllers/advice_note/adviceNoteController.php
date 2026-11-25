<?php

namespace App\Http\Controllers\advice_note;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\advice_note\advice_notes;
use App\Models\advice_note\canceled_advice_notes;
use App\Models\advice_note\particulars;
use Illuminate\Support\Facades\DB;
use App\Models\booking\booked_sponsorship_packages;
use App\Traits\NotificationTrait;
use App\Traits\ExhibitorTrait;
use App\Traits\InvoiceTrait;
use App\Traits\BillingTrait;
use App\Models\companies;
use App\Models\exhibition\exhibitors;
use App\Models\Information\Comment;
use App\Models\setting\BankAccount;
use App\Models\setting\DefaultSettings;
use App\Models\setting\ExchangeRate;
use App\Models\sponsorships\sponsorship_packages;
use App\Traits\IntergrationTraits;
use App\Traits\BadgesTraits;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Traits\AutomaticActionTraits;

// use Illuminate\Database\Eloquent\Model;

class adviceNoteController extends Controller
{
    use ExhibitorTrait;
    use NotificationTrait;
    use InvoiceTrait;
    use BillingTrait;
    use IntergrationTraits;
    use BadgesTraits;
    use AutomaticActionTraits;

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
        #---------------------------------- INTERNAL USERS--------------------------#
        if (Auth::user()->account_type == 1) {
            if ($request->user()->cannot('MANAGE_INVOICES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICES'));
            }

            $advice_note = advice_notes::orderBy('advice_note_created_at', 'DESC')
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
                ->where('advice_note_status', '!=', 'CANCELED')->limit(config('global.DATA_LIMIT'))
                ->paginate(config('global.DATA_LIMIT'), [
                    'company_id',
                    'advice_notes.advice_note_id',
                    'companyName',
                    'advice_note_deadline',
                    'invoice_type',
                    'advice_note_status',
                    'advice_note_description',
                    'advice_note_created_at',
                    'registrationID',
                    'companyLocation',
                    'companyPhone',
                    'control_number',
                    'balance',
                    'total_amount',
                    'currency',
                    'amount_paid',
                    'payment_mode',
                    'sp_invoice_no'
                ]);
        }
        #----------------------------END INTERNAL USERS-----------------------------------#

        #------------------------- EXTERNAL USERS----------------------------#
        elseif (Auth::user()->account_type == 2) {
            if ($request->user()->cannot('MANAGE_INVOICES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICES'));
            }
            //CURRENT EVENT INVOICES
            $advice_note = advice_notes::orderBy('advice_note_created_at', 'DESC')
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
                ->leftJoin('company_admins', 'advice_notes.company_id', '=', 'company_admins.companyId')
                ->where('company_admins.adminId', Auth::user()->user_uuid)
                ->where('advice_note_status', '!=', 'CANCELED')->limit(config('global.DATA_LIMIT'))
                ->paginate(config('global.DATA_LIMIT'), [
                    'company_id',
                    'advice_notes.advice_note_id',
                    'companyName',
                    'advice_note_deadline',
                    'invoice_type',
                    'advice_note_status',
                    'advice_note_description',
                    'advice_note_created_at',
                    'registrationID',
                    'companyLocation',
                    'companyPhone',
                    'control_number',
                    'balance',
                    'total_amount',
                    'currency',
                    'amount_paid',
                    'payment_mode',
                    'sp_invoice_no'
                ]);
        }
        #----------------------------------- END EXTERNAL USERS------------------------#
        return view('advice_note.on-fair-advice-notes', compact('advice_note')); // return on fair advice note with data
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        #---------------------------------- INTERNAL USERS--------------------------#
        if (Auth::user()->account_type == 1) {
            if ($request->user()->cannot('MANAGE_INVOICES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICES'));
            }

            $advice_note = advice_notes::orderBy('advice_note_created_at', 'DESC')
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
                ->where('advice_note_status', '!=', 'CANCELED')->limit(config('global.DATA_LIMIT'))
                ->paginate(config('global.DATA_LIMIT'), [
                    'company_id',
                    'advice_notes.advice_note_id',
                    'companyName',
                    'advice_note_deadline',
                    'invoice_type',
                    'advice_note_status',
                    'advice_note_description',
                    'advice_note_created_at',
                    'registrationID',
                    'companyLocation',
                    'companyPhone',
                    'control_number',
                    'balance',
                    'total_amount',
                    'currency',
                    'amount_paid',
                    'payment_mode',
                    'sp_invoice_no'
                ]);
        }
        #----------------------------END INTERNAL USERS-----------------------------------#

        #------------------------- EXTERNAL USERS----------------------------#
        elseif (Auth::user()->account_type == 2) {
            if ($request->user()->cannot('MANAGE_INVOICES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICES'));
            }
            // CURRENT EVENT INVOICES
            $advice_note = advice_notes::orderBy('advice_note_created_at', 'DESC')
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
                ->leftJoin('company_admins', 'advice_notes.company_id', '=', 'company_admins.companyId')
                ->where('company_admins.adminId', Auth::user()->user_uuid)
                ->where('advice_note_status', '!=', 'CANCELED')->limit(config('global.DATA_LIMIT'))
                ->paginate(config('global.DATA_LIMIT'), [
                    'company_id',
                    'advice_notes.advice_note_id',
                    'companyName',
                    'advice_note_deadline',
                    'invoice_type',
                    'advice_note_status',
                    'advice_note_description',
                    'advice_note_created_at',
                    'registrationID',
                    'companyLocation',
                    'companyPhone',
                    'control_number',
                    'balance',
                    'total_amount',
                    'currency',
                    'amount_paid',
                    'payment_mode',
                    'sp_invoice_no'
                ]);
        }

        #----------------------------------- END EXTERNAL USERS------------------------#
        return view('advice_note.on-fair-advice-notes', compact('advice_note')); // return on fair advice note with data
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('MANAGE_INVOICES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICES'));
        }
        $request->validate([
            'company_id' => 'required',
        ]);
        $transactionLevel = DB::transactionLevel();
        try {
            #----- DEADLINE ------#
            $today = Carbon::now();
            $newDate = $today->addDays(config('global.INVOICE_DUE_DAYS'));
            $defaultDeadline = $newDate->toDateString(); // Format: YYYY-MM-DD  
            #----- END--- DEADLINE---#

            $event_id = session('liveEvent');
            $user_id = Auth::user()->user_uuid;
            $company_id = $request->company_id; // company ID
            # ------ CHECKING CUSTOMER REGISTRATION ---- #
            
           
            if (config('global.APP_STATUS') == 1) {
                $companies = companies::where('companyID', $company_id)
                    ->where('sp_customer_no', Null)->first();
                if ($companies) {
                    $firstName = $companies->companyName;
                    $phone_number = $companies->companyPhone;
                    $email = $companies->companyEmail;
                    if (empty($phone_number || empty($email))) {
                        return back()->with('fail', 'Company lacks phone  number  email, please update it to proceed!');
                    }
                    if (substr($phone_number, 0, 3) !== '255') {
                        $phone_number = '255000000000';
                    }
                    if (strlen($phone_number) === 13) {
                        $phone_number = substr($phone_number, 1);
                    }
                    if (!$this->customer_registration_api($company_id, $firstName, $phone_number, $email)) {
                        return back()->with('fail', 'Failed to Register Company to Billing system');
                    }
                }
            }
            # ------- CHECKING CUSTOMER REGISTRATION -----#

            # ------generate advice note  for customer Booking.-----#
            if ($request->advice_note) {
                DB::beginTransaction();
                $vat = $request->vat;
                $adviceNote = "INV-" . random_int(5000, 9999) . "-" . random_int(1000, 4999) . "-00" . date('y'); //advice note id created here
                advice_notes::create([
                    'advice_note_id' => $adviceNote,
                    'user_id' => Auth::user()->user_uuid,
                    'company_id' => $request->company_id,
                    'advice_note_status' => 'UNDER_REVIEW', // advice note status
                    'event' => $event_id,
                    'advice_note_deadline' => $defaultDeadline,
                    'vat' => $vat,
                    'change_currency' => $request->input('currencyRequested') === 'TZS',
                    'advice_note_description' => $request->advice_note_description
                ]);

                // for sponsorship Packages
                $booked = booked_sponsorship_packages::where('advice_note_id', Null)->where('approved_by', Auth::user()->user_uuid)->first();
                if ($booked) {
                    $packageCurrency=sponsorship_packages::find($booked->package_id)->type_of_currency;
                    advice_notes::find($adviceNote)->update([
                        'invoice_type' => 'SPONSORSHIP',
                        'currency'=>$packageCurrency,
                        'change_currency' => false,
                    ]);
                    $booked->update(['advice_note_id' => $adviceNote]);
                }
                // End

                //if advice note generated update booths with invoice ID.
                DB::update("update service_charges set advice_note_id='$adviceNote' where company_id ='$request->company_id' and advice_note_id is null and user_id='$user_id'");
                DB::update("update booked_pavilions set advice_note_id='$adviceNote' where companyID ='$request->company_id' and advice_note_id is null and userID='$user_id'");
                $company_id = $request->company_id;

                #------MAKE CUSTOMER AS NEW EXHIBITOR-----#
                $participationId = $this->RegisterCompanyAsExhibitor($company_id, $event_id); // register this company as exhibitor
                if ($participationId != '0') {
                    $exhibitor = exhibitors::find($participationId);
                    $exhibitor->update([
                        'exhibitionSpace' => $request->exhibitionSpace,
                        'exhibitedProducts' => $request->exhibitedProducts,
                        'makeSales' => $request->makeSales
                    ]);
                }

                #END EXHIBITORS#
                $id = $adviceNote;
                if (!$this->CreateParticulars($id)) {
                    DB::rollBack();
                    return back()->with('fail', 'The invoice could not be created. Check the booking and its GFS codes.');
                }
                $this->CreateBill($id); // create bill for this particulars with specified Advice note ID
                DB::commit();

                return redirect()->route('adviceNotes.show', ['adviceNote' => $id])->with('success', 'Invoice generated successfully!');
            }
        } catch (\Throwable $th) {
            DB::rollBack($transactionLevel);
            report($th);
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
        if ($request->user()->cannot('MANAGE_INVOICES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICES'));
        }
        //advice note details
        $adviceNotes = advice_notes::visibleTo($request->user())
            ->leftJoin('companies', 'companies.companyID', '=', 'advice_notes.company_id')
            ->leftJoin('users', 'advice_notes.user_id', '=', 'users.user_uuid')
            ->leftJoin('roles', 'users.role', '=', 'roles.id')
            ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
            ->where('advice_notes.advice_note_id', $id)
            ->get([
                'advice_notes.advice_note_id',
                'advice_notes.company_id',
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
                'event_name',
                'event_descriptions',
                'vat',
                'total_amount',
                'currency',
                'advice_notes.control_number',
                'rejection_comment',
                'total_amount',
                'account_type',
                'discount',
                'discount_reasons',
                'discount_attachment',
                'initially_assessed_by',
                'finally_assessed_by',
                'rejection_reasons',
                'balance',
                'amount_paid',
                'change_currency',
                'new_deadline',
                'sp_invoice_no',
                'registrationID',
                'discountAmount'
            ]);
        //end

        abort_if($adviceNotes->isEmpty(), 404);

        foreach ($adviceNotes as $key) {
            #----- CREATE BILL FOR INVOICE WITH NO BILL ----#
            if ($key->total_amount == Null) {
                $this->CreateBill($id);
            }
            #----- END CREATE BILL FOR INVOICE WITH NO BILL ----#
        }

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
                ]
            ); // end
        $exhibitor = exhibitors::leftJoin('advice_notes', 'exhibitors.exhibitor_id', '=', 'advice_notes.company_id')
            ->where('exhibitor_event', session('liveEvent'))->where('advice_notes.advice_note_id', $id)->first();
        $transfer = BankAccount::where('bankAccountStatus','ACTIVE')->inRandomOrder()->get(['account_number', 'account_name', 'swift_code', 'account_currency', 'bank_name','bankAccountStatus']);
        $rate = ExchangeRate::where('currencyCode', 'USD')->first()->SELLING ?? 2700;
        return view('advice_note.printable-advice-note', compact(
            'adviceNotes',
            'particulars',
            'transfer',
            'rate',
            'exhibitor'
        )); // return invoice details


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->user()->cannot('MAPP_INVOICES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MAPP_INVOICES'));
        }
        try {
            advice_notes::find($id)->update([
                'sp_invoice_no' => $request->sp_invoice_no,
                'control_number' => $request->control_number,
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500').$th->getMessage());
        }
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
        try {
            #******************** CHANGE CURRENCY ****************#
            if ($request->change_currency == 1) {
                advice_notes::find($id)->update(['change_currency' => 1]);
                return back()->with('success', 'Currency change requested successfully!');
            } elseif ($request->change_currency == 2) {
                advice_notes::find($id)->update(['change_currency' => 0]);
                return back()->with('success', 'Currency change requested successfully!');
            }
            #******************** CHNAGE CURRENCY ****************#

            #----- APPROVE INVOICE -----#
            if ($request->source == 1) {
                if ($request->user()->cannot('REQUEST_ADVICE_NOTE', Role::class)) {
                    return back()->with('fail', $this->permissionDeniedMessage('REQUEST_ADVICE_NOTE'));
                }
                #------- CHANGING CURRENCY ------#
                $rate = DefaultSettings::first()->exchangeRate ?? 2700;
                if ($request->currency == 'TZS') {
                    $particulars = particulars::where('advice_note_id', $id)->get();
                    foreach ($particulars as $value) {
                        $newAmount = $value->cost_for_one_particular * $rate;
                        particulars::find($value->particular_id)->update(['cost_for_one_particular' => $newAmount, 'type_of_currency' => 'TZS']);
                    }
                    advice_notes::find($id)->update([
                        'exchange_rate' => $rate,
                        'change_currency' => 1,
                    ]);
                    $this->CreateBill($id); // Reload Bill Creation
                }
                #------- CHANGING CURRENCY ------#
                $this->GenerateInvoice($id); // Request Control number
                advice_notes::find($id)->update([
                    'advice_note_status' => 'APPROVED',
                    'payment_mode' => 1,
                    'exchange_rate'=> $rate,
                    'approved_at' => date('Y-m-d H:i:s'),
                ]);
                $this->AllocateBages($id); // allocate badges to exhibitors
                return back()->with('success', config('Messages.200'));
            }
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500').$th->getMessage());
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
            if ($request->user()->cannot('CANCEL_INVOICES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('CANCEL_INVOICES'));
            }
            if (empty($request->reasons)) {
                return back()->with('fail', 'Please, give reasons why do you cancel this Advice Note');
            }
            $value = advice_notes::visibleTo($request->user())
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->where('advice_note_id', $id)->first(['advice_notes.*', 'companies.sp_customer_no']);
            if (!$value || !$value->isCancelable()) {
                return back()->with('fail', 'Only unpaid, active invoices can be canceled.');
            }
            $customer_no = $value->sp_customer_no;
            $invoice_no = $value->sp_invoice_no;
            $canceledby = Auth::user()->first_name . " " . Auth::user()->last_name;
            $reference = $id;
            $reasons = $request->reasons;
            $data = array('customer_no' => $customer_no, 'invoice_no' => $invoice_no, 'reference' => $reference, 'cancelledby' => $canceledby, 'reason' => $reasons); // data array
            #************ NO CONTROL NUMBER*********#
            if ($value->control_number == Null) {
                $this->ClearBadgeAllocated($id); // remove badges from Allocation Table
                $this->RemoveAdviceNoteFromList($id);
                advice_notes::find($id)->delete();
                return redirect()->route('adviceNotes.create')->with('success', 'Advice Note Deleted successfully!');
            }
            #************** END**********#
            #*********** INVOICE HAS CONTROL NUMBER ***********#
            if ($value->control_number != Null) {
                if (config('global.APP_STATUS') == 1) {
                    $status = $this->CancelInvoiceFromSp($data, $id); // if their is control number in invoice, cancel control number first from billing system.
                } else {
                    $status = 1;
                }
                if ($status == 1) {
                    if (!canceled_advice_notes::where('advice_note_id', $id)->exists()) {
                        canceled_advice_notes::create(['id' => Str::uuid(), 'advice_note_id' => $id, 'canceled_by' => Auth::user()->user_uuid, 'reasons' => $request->reasons]);
                    }
                    $this->ClearBadgeAllocated($id); // remove badges from Allocation Table
                    $this->RemoveAdviceNoteFromList($id); // cancel advice note and remove company from exhibitor;
                    advice_notes::find($id)->update(['advice_note_status' => 'CANCELED']);
                    return redirect()->route('adviceNotes.create')->with('success', config('Messages.200'));
                } else {
                    return back()->with('fail', config('Messages.500')); // failed to delete from SP database.
                }
            }
            return redirect()->route('adviceNotes.create')->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    # ----- Review Invoice Discount -----#
    public function reviewInvoiceDiscount(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('APPLY_DISCOUNT', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('APPLY_DISCOUNT'));
            }
            $adviceNotes = advice_notes::visibleTo($request->user())
                ->leftJoin('companies', 'companies.companyID', '=', 'advice_notes.company_id')
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
                    'event_name',
                    'event_descriptions',
                    'vat',
                    'total_amount',
                    'currency',
                    'advice_notes.control_number',
                    'rejection_comment',
                    'total_amount',
                    'account_type',
                    'discount',
                    'discount_reasons',
                    'discount_attachment',
                    'initially_assessed_by',
                    'finally_assessed_by',
                    'rejection_reasons',
                    'balance',
                    'amount_paid',
                    'change_currency',
                    'new_deadline',
                    'sp_invoice_no',
                    'registrationID',
                    'discountAmount'
                ]);
            if ($adviceNotes->isEmpty()) {
                return back()->with('fail', 'Invoice not found.');
            }
            $comments = Comment::leftJoin('users', 'comments.commentedBy', '=', 'users.user_uuid')
                ->leftJoin('roles', 'users.role', '=', 'roles.id')
                ->where('referenceId', $id)->get();
            return view('advice_note.InvoiceDiscountManagement', compact('adviceNotes', 'comments'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    #---- Apply Discount -----#
    public function applyDiscount(Request $request)
    {
        try {
            if ($request->user()->cannot('APPLY_DISCOUNT', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('APPLY_DISCOUNT'));
            }
            $validator = Validator::make($request->all(), [
                'discount' => 'required|numeric',
                'discount_reasons' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            if ($request->discount == 0 || $request->discount > 100) {
                return back('fail', 'Discount must be greater than Zero or be greter than 100% ');
            }
            $id = $request->adviceNoteId;
            $discount = $request->discount;
            advice_notes::find($id)->update([
                'discount' => $discount,
                'discount_reasons' => $request->discount_reasons,
                'initially_assessed_by' => Auth::user()->user_uuid,
            ]);
            Comment::create([
                'commentId' => Str::uuid(),
                'commentedBy' => Auth::user()->user_uuid,
                'comment' => $request->discount_reasons,
                'referenceId' => $id,
            ]);
            // Request Discount  
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }


    public function approveDiscount(Request $request)
    {
        try {
            if ($request->user()->cannot('APPLY_DISCOUNT', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('APPLY_DISCOUNT'));
            }
            $validator = Validator::make($request->all(), [
                'comment' => 'required',
                'descision' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            $id = $request->adviceNoteId;
            $invoice = advice_notes::find($id);
            $discount = $invoice->discount;
            Comment::create([
                'commentId' => Str::uuid(),
                'commentedBy' => Auth::user()->user_uuid,
                'comment' => $request->comment,
                'referenceId' => $id,
            ]);

            if ($request->descision == 'APPROVED') {
                if ($request->user()->cannot('APPROVE_ADVICE_NOTE', Role::class)) {
                    return back()->with('fail', $this->permissionDeniedMessage('APPROVE_ADVICE_NOTE'));
                }
                if (Auth::user()->user_uuid != $invoice->initially_assessed_by) {
                    return back()->with('fail', 'The Discount Approver must be different from dicount Requester');
                }

                $particulars = particulars::where('advice_note_id', $id)->get();
                foreach ($particulars as  $value) {
                    $discountAmount = ($discount / 100) * $value->cost_for_one_particular;
                    $cost = ceil(($value->cost_for_one_particular - $discountAmount) * 100) / 100;
                    particulars::find($value->particular_id)
                        ->update(['cost_for_one_particular' => $cost]);
                }
                $part = particulars::where('advice_note_id', $id)->get();
                $amountAfterDisWithoutVat = 0;
                $vat = 0;
                foreach ($part as  $value) {
                    $amountAfterDisWithoutVat = $amountAfterDisWithoutVat + ($value->particular_quantity * $value->cost_for_one_particular);
                }
                if (advice_notes::find($id)->vat == 1) {
                    $vat = ceil(($amountAfterDisWithoutVat * 0.18) * 100) / 100;
                }

                $totalAmount = ceil(($amountAfterDisWithoutVat + $vat) * 100) / 100;
                $adviceNote = advice_notes::find($id);
                $discountAmount = ceil(($adviceNote->total_amount * ($adviceNote->discount / 100)) * 100) / 100;

                $adviceNote->update([
                    'finally_assessed_by' => Auth::user()->user_uuid,
                    'total_amount' => $totalAmount,
                    'balance' => $totalAmount,
                    'discountAmount' => $discountAmount,
                ]);
                return back()->with('success', 'Discount has been applied to the whole invoice!');
            }

            return back()->with('success', 'Comment saved successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
