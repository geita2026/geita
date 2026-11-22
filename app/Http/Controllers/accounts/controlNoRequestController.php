<?php

namespace App\Http\Controllers\accounts;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\advice_note\advice_notes;
use App\Models\advice_note\particulars;
use Illuminate\Support\Facades\Auth;
use App\Models\setting\BankAccount;
use App\Models\setting\DefaultSettings;
use App\Traits\BillingTrait;
use Spatie\Permission\Models\Role;

class controlNoRequestController extends Controller
{
    use BillingTrait;
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
        if($request->user()->cannot('VIEW_CONTROL_NO_REQUEST_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_CONTROL_NO_REQUEST_SUBMENU'));
        }
        //
            $new_on_fair_advice_note_count=advice_notes::leftJoin('events','advice_notes.event','=','events.event_id')
            ->where('event_status',1)->where('advice_note_status','UNDER_REVIEW')->count();
            // return $new_on_fair_advice_note_count;

            $on_fair_advice_note_count=advice_notes::leftJoin('events','advice_notes.event','=','events.event_id')
            ->where('event_status',1)
            ->where('advice_note_status','!=','CANCELED')->count();

            return view('accounts.approved-advice-notes', compact('new_on_fair_advice_note_count', 'on_fair_advice_note_count'));
    }

    /**id
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->user()->cannot('VIEW_CONTROL_NO_REQUEST',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_CONTROL_NO_REQUEST'));
        }
        //
        if (!empty($request->search)) {
        $adviceNotes=advice_notes::leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->leftJoin('users','advice_notes.user_id','=','users.user_uuid')
        ->leftJoin('events','advice_notes.event','=','events.event_id')
        ->orderBy('advice_note_created_at','DESC') ->limit(100)
        ->where('event_status',1)
        ->where('advice_note_status','!=','CANCELED') // canceled invoice
        ->where('advice_note_status','!=','UNDER_REVIEW') // generated Invoice
        ->where('advice_note_status','!=','REJECTED') // rejected invoice
        ->get(['advice_notes.advice_note_id','companyName','advice_note_status','companyPhone',
        'advice_note_deadline','advice_notes.user_id','control_number','amount_paid','currency','total_amount','payment_mode']);
        }

        $advice_notes=advice_notes::leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->orderBy('advice_note_created_at','desc')
        ->where('advice_note_status','!=','CANCELED') // canceled invoice
        ->where('advice_note_status','!=','UNDER_REVIEW') // generated Invoice
        ->where('advice_note_status','!=','REJECTED') // rejected invoice
        ->get();

        $adviceNotes=advice_notes::leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->leftJoin('users','advice_notes.user_id','=','users.user_uuid')
        ->leftJoin('events','advice_notes.event','=','events.event_id')
        ->where('event_status',1)
        ->where('advice_note_status','!=','CANCELED') // canceled invoice
        ->where('advice_note_status','!=','UNDER_REVIEW') // generated Invoice
        ->where('advice_note_status','!=','REJECTED') // rejected invoice
        ->orderBy('advice_note_created_at','DESC') ->limit(100)
        ->get(['advice_notes.advice_note_id','companyName','advice_note_status','companyPhone',
        'advice_note_deadline','advice_notes.user_id','control_number','amount_paid','currency','total_amount','payment_mode']);


        return view('accounts.advice-note-list',compact('adviceNotes','advice_notes'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if($request->user()->cannot('APPROVE_CONTROL_NO_REQUEST',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('APPROVE_CONTROL_NO_REQUEST'));
        }

        $inv=advice_notes::leftJoin('companies','companies.companyID','=','advice_notes.company_id')
        ->leftJoin('company_details','advice_notes.company_id','=','company_details.companyID')
        ->leftJoin('users','advice_notes.accountant_id','=','users.user_uuid')
        ->leftJoin('roles','users.role','=','roles.id')
        ->where('advice_notes.advice_note_id',$id)->first();
        $approved_by=$inv->first_name.' '.$inv->middle_name.' '.$inv->last_name; // approved by
        $title=$inv->name; $approved_at=$inv->approved_at;

        //advice note details
        $adviceNotes=advice_notes::leftJoin('companies','companies.companyID','=','advice_notes.company_id')
        ->leftJoin('company_details','advice_notes.company_id','=','company_details.companyID')
        ->leftJoin('users','advice_notes.user_id','=','users.user_uuid')
        ->leftJoin('roles','users.role','=','roles.id')
        ->leftJoin('events','advice_notes.event','=','events.event_id')
        ->where('advice_notes.advice_note_id',$id)
        ->get(['advice_notes.advice_note_id','companyName','advice_note_created_at','companyAddress','companyLocation',
        'companyPhone','personalPhone','first_name','last_name','companyEmail','roles.name','advice_note_deadline','advice_note_description'
        ,'tinNo','users.user_signature','registrationNo','advice_note_status','event_name','event_descriptions',
        'vat','rejection_comment','control_number','account_type','discount','currency','total_amount','balance','amount_paid','change_currency','exchange_rate'
        ]);
        //end



        $particulars=advice_notes::leftJoin('particulars','advice_notes.advice_note_id','=','particulars.advice_note_id')
        ->leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->where('advice_notes.advice_note_id',$id)
        ->get(['particulars.advice_note_id','companyName','particular_name','cost_for_one_particular',
        'particular_quantity','type_of_currency','particular_category','particular_description','particular_type']);

        $url=$id;
        $settings=DefaultSettings::get(['invoice_other_information']);

        $sac_officer=advice_notes::leftJoin('users','advice_notes.user_id','=','users.user_uuid')->where('advice_note_id',$id)->first(); // sac office
        $sac_officer_name=$sac_officer->first_name." ".$sac_officer->last_name;
        $transfer=BankAccount::get();
        return view('accounts.printable-advice-note',compact('adviceNotes','particulars','approved_by','title','approved_at','url','settings','sac_officer_name','transfer')); // return invoice preview
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
        if($request->user()->cannot('REJECT_CONTROL_NO_REQUEST',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('REJECT_CONTROL_NO_REQUEST'));
        }
        //

        try
        {

            #************* CHANGING AMOUNT TO REQUESTED CURRENCY**************#
            if($request->change_amount==1)
            {
                $setExchangeRate=advice_notes::find($id)->update(['exchange_rate'=>$request->exchange_rate,'currency'=>'TZS']);
                if($setExchangeRate)
                {
                    $particulars=particulars::where('advice_note_id',$id)->get();
                    foreach ($particulars as $value)
                    {
                      $newRate=$value->cost_for_one_particular*$request->exchange_rate;
                      particulars::find($value->particular_id)->update(['cost_for_one_particular'=>$newRate,'type_of_currency'=>'TZS']);
                    }

                   $this->CreateBill($id); // Change bill to new Value with exchange rate;
                    return back()->with('success','Exhange rate updated successfully!');
                }
                else
                {
                    return back()->with('fail','Failed to set exchange rate');
                }
            }

            #***********END***************************************************#


        advice_notes::find($id)->update([
        'advice_note_status'=>'REJECTED',
        'rejection_comment'=>$request->rejection_comment,
        'accountant_id'=>Auth::user()->user_uuid
    ]); // rejecting invoice

        return redirect()->route('controlNoRequests.create')->with('success','Invoice Rejected successfully!');
       }

       catch (\Throwable $th)
       {
       return back()->with('fail','Something went wrong!'.$th->getMessage());
       }


    }


}
