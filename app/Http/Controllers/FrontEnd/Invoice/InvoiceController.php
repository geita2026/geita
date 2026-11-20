<?php

namespace App\Http\Controllers\FrontEnd\Invoice;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\advice_note\advice_notes;
use App\Models\setting\BankAccount;
use App\Models\setting\DefaultSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\ExhibitorTrait;
use App\Traits\NotificationTrait;
use App\Traits\InvoiceTrait;
use App\Traits\BillingJobsTrait;
use App\Traits\BillingTrait;
use Spatie\Permission\Models\Role;

class InvoiceController extends Controller
{
    use BillingTrait;
    use BillingJobsTrait;
    use ExhibitorTrait;
    use NotificationTrait;
    use InvoiceTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if($request->user()->cannot('View My Invoice',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //

        $advice_note=advice_notes::orderBy('advice_note_created_at','DESC')
        ->leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->leftJoin('events','advice_notes.event','=','events.event_id')
        ->where('company_id',Auth::user()->myCompany)
        ->where('event_status',1)
        ->where('advice_note_status','!=','CENCELED')
        ->limit(25)
        ->get(['company_id','advice_notes.advice_note_id','companyName','advice_note_deadline','invoice_type',
        'advice_note_status','registrationID',
        'control_number','balance','total_amount','currency','amount_paid']);
        // get all advice note generated in the system.

        return view('FrontEnd.Invoices.customer_invoices',compact('advice_note')); // return on fair advice note with data
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create My Invoice',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //

      try {
        $user_id=Auth::user()->user_uuid;

        $request->validate(['company_id'=>'required', 'event'=>'required', ]); //request validation

        //after if booking is comfirmed, generate advice note
        $adviceNote="INV-00".date('y')."-".random_int(5000,9999)."-".random_int(1000,4999); //invoice id created here
        advice_notes::create([
            'advice_note_id'=>$adviceNote,
            'user_id'=>Auth::user()->user_uuid,
            'company_id'=>$request->company_id,
            'advice_note_status'=>'GENERATED', // advice note status
            'event'=>$request->event,
        ]);

        //if advice note generated update booths to partial booked.
            DB::update("update booked_booths set advice_note_id='$adviceNote' where companyID ='$request->company_id' and advice_note_id is null and userID='$user_id'");
            DB::update("update service_charges set advice_note_id='$adviceNote' where company_id ='$request->company_id' and advice_note_id is null and user_id='$user_id'");
            DB::update("update booked_pavilions set advice_note_id='$adviceNote' where companyID ='$request->company_id' and advice_note_id is null and userID='$user_id'");
        // end of updatig

         // register as exhibitor
    $company_id=Auth::user()->user_uuid;
    $event_id=session('event_id');

    $this->RegisterCompanyAsExhibitor($company_id,$event_id); // register as Exhibitor

    $id=$adviceNote; // advice note Id

     $this->SetDeadline($id); // set deadline
    $this->CreateParticulars($id); // create particulars
    $this->CreateBill($id); // create bill for this particulars with specified Advice note ID
    $this->GenerateInvoice($id); // generate invoice, this will be sent through APIs to request Control Number from EGPG

    return redirect()->route('my_invoices.index')->with('success','Advice Note created Successfully!');
      } catch (\Throwable $th) {
       return back()->with('fail','Invoice Generated successfully!, The rest system will work for you!');
      }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if($request->user()->cannot('View My Invoice',Role::class))
        {
            return response()->json(['status'=>2,'fail'=>'You do not have permission to perform this Action']);
        }
        //
        $inv = advice_notes::leftJoin('companies', 'companies.companyID', '=', 'advice_notes.company_id')
            ->leftJoin('company_details', 'advice_notes.company_id', '=', 'company_details.companyID')
            ->leftJoin('users', 'advice_notes.accountant_id', '=', 'users.user_uuid')
            ->leftJoin('roles', 'users.role', '=', 'roles.id')
            ->where('advice_notes.advice_note_id', $id)->first();
        $approved_by = $inv->first_name . ' ' . $inv->middle_name . ' ' . $inv->last_name; // approved by
        $title = $inv->name;
        $approved_at = $inv->approved_at;

        //advice note details
        $adviceNotes = advice_notes::leftJoin('companies', 'companies.companyID', '=', 'advice_notes.company_id')
            ->leftJoin('company_details', 'advice_notes.company_id', '=', 'company_details.companyID')
            ->leftJoin('users', 'advice_notes.user_id', '=', 'users.user_uuid')
            ->leftJoin('roles', 'users.role', '=', 'roles.id')
            ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
            ->where('advice_notes.advice_note_id', $id)
            ->get([
                'advice_notes.advice_note_id', 'companyName', 'advice_note_created_at', 'companyAddress', 'companyLocation',
                'companyPhone', 'personalPhone', 'first_name', 'last_name', 'companyEmail', 'roles.name', 'advice_note_deadline', 'advice_note_description', 'tinNo', 'users.user_signature', 'registrationNo', 'advice_note_status',
                'advice_notes.updated_at', 'event_name', 'event_descriptions', 'vat', 'total_amount', 'currency', 'advice_notes.control_number',
                'rejection_comment', 'total_amount', 'account_type', 'discount', 'discount_reasons', 'discount_attachment', 'initially_assessed_by',
                'finally_assessed_by', 'rejection_reasons', 'balance', 'amount_paid', 'change_currency', 'new_deadline', 'sp_invoice_no'
            ]);
        //end

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
                    'particulars.advice_note_id', 'companyName', 'particular_name', 'cost_for_one_particular',
                    'particular_quantity', 'type_of_currency', 'particular_category', 'particular_description',
                    'particular_type',
                ]
            ); // end

        $url = 'SAMS|' . $id;

        $settings = DefaultSettings::get(['invoice_other_information']);

        $sac_officer = advice_notes::leftJoin('users', 'advice_notes.user_id', '=', 'users.user_uuid')->where('advice_note_id', $id)->first(); // sac office
        $sac_officer_name = $sac_officer->first_name . " " . $sac_officer->last_name;

        $transfer = BankAccount::get(['account_number', 'account_name', 'swift_code', 'account_currency', 'bank_name']);


        return view('FrontEnd.Invoices.printable-invoice',compact('adviceNotes',
        'particulars',
        'approved_by',
        'url',
        'title',
        'approved_at',
        'settings',
        'sac_officer_name',
        'transfer')); // return invoice details
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
        if($request->user()->cannot('View My Invoice',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //

        if($request->vat=='on'){ $vat_status=0.18; }
        else { $vat_status=Null; }

        try {
            $adviceNote=advice_notes::find($id);
           $adviceNote->update([
            'advice_note_description'=>$request->description,
            'advice_note_deadline'=>$request->deadline_date,
            'vat'=>$vat_status,
            'advice_note_status'=>'PENDING'
        ]);

        // register as exhibitor
        $company_id=Auth::user()->user_uuid;
        $event_id=session('event_id');
        $this->RegisterCompanyAsExhibitor($company_id,$event_id);
        // end of registration

        return back()->with('success','Advice Note Informations Updated successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','some thing went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->user()->cannot('Delete My Invoice',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        try {
             $company_id=Auth::user()->user_uuid; // for self booking company ID is same as user uuid
             $this->RemoveAdviceNoteFromList($id,$company_id); // cancel advice note and remove company from exhibitor;
             return back()->with('success','Invoice Canceled Successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong!');
        }

    }


    private function getParticulars($id){
        $particulars=advice_notes::leftJoin('particulars','advice_notes.advice_note_id','=','particulars.advice_note_id')
        ->leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->where('advice_note_id','INV-0022-5485-4778')
        ->get(['particulars.advice_note_id','companyName','particular_name','cost_for_one_particular',
        'particular_quantity','type_of_currency','particular_category','particular_description','particular_type']);
        return response()->json(['status'=>1,'particulars'=>$particulars]);
    }

}
