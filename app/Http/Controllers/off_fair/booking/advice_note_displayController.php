<?php

namespace App\Http\Controllers\off_fair\booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\off_fair\advice_notes\off_fair_advice_notes;
use App\Models\off_fair\booking\off_fair_space_bookings;
use App\Models\setting\BankAccount;
use App\Models\setting\DefaultSettings;

class advice_note_displayController extends Controller
{
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
        if($request->user()->cannot('View Confirmed Off Fair Invoice',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        // list of advice notes to Accountant dashboard

        $advice_notes=off_fair_advice_notes::leftJoin('off_fair_customers','off_fair_advice_notes.advice_note_owned_by','=','off_fair_customers.customer_id')
        ->orderBy('advice_note_created_at','DESC')->where('off_fair_advice_note_status','!=',0)
        ->get(['off_fair_advice_note_id','customer_id','customer_name','advice_note_created_at','off_fair_advice_note_status',
        'advice_note_id','off_fair_advice_note_deadline','advice_note_attended_by','off_fair_advice_note_deadline','advice_note_updated_at']);

        return view('accounts.off-fair-advice-notes',compact('advice_notes'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if($request->user()->cannot('View Confirmed Off Fair Invoice',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $adviceNotes=off_fair_advice_notes::leftJoin('off_fair_customers','off_fair_advice_notes.advice_note_owned_by','=','off_fair_customers.customer_id')
        ->leftJoin('users','off_fair_advice_notes.advice_note_generated_by','=','users.user_uuid')
        ->leftJoin('roles','users.role','=','roles.id')->where('off_fair_advice_note_id',$id)->get();



        $booking_dates=off_fair_space_bookings::get(['start_date','end_date','advice_note_id'])->where('advice_note_id',$id);

        $particulars=off_fair_advice_notes::leftJoin('off_fair_booking_particulars','off_fair_advice_notes.off_fair_advice_note_id','=','off_fair_booking_particulars.advice_note_id')
        ->where('off_fair_advice_note_id',$id)->get();

        $defaultSettings=DefaultSettings::get();
        $sac_officer=off_fair_advice_notes::leftJoin('users','off_fair_advice_notes.advice_note_generated_by','=','users.user_uuid')->where('off_fair_advice_note_id',$id)->first(); // sac office
        $sac_officer_name=$sac_officer->first_name." ".$sac_officer->last_name;
        $url='SAMS|'.$id;


        $inv=off_fair_advice_notes::leftJoin('users','off_fair_advice_notes.advice_note_attended_by','=','users.user_uuid')
        ->leftJoin('roles','users.role','=','roles.id')->where('off_fair_advice_note_id',$id)->first();
        $approved_by=$inv->first_name.' '.$inv->middle_name.' '.$inv->last_name; // approved by
        $title=$inv->name; $approved_at=$inv->advice_note_updated_at;
        $transfer=BankAccount::get();
        return view('accounts.printable-off-fair-advice-note',compact('adviceNotes','particulars','booking_dates',
        'defaultSettings','sac_officer_name','url','approved_by','title','approved_at','transfer'));


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
        if($request->user()->cannot('Update Confirmed Off Fair Invoice',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $advice_note=off_fair_advice_notes::find($id);
        $returned=$advice_note->update([
            'off_fair_advice_note_status'=>'RETURNED',
        ]);

        if($returned)
        {
            return back()->with('success','Advice note returned to SAC office Successfully!!');
        }
        else
        {
            return back()->with('fail','Something went wrong...! please repeat again');
        }
    }

}
