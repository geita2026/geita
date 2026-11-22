<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\advice_note\advice_notes;
use App\Models\settings\events;
use Spatie\Permission\Models\Role;

class exhibitionCollectionController extends Controller
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
        if($request->user()->cannot('MANAGE_COLLECTION_REPORTS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('MANAGE_COLLECTION_REPORTS'));
        }
        //

        if($request->from_date OR $request->to_date){
            $from_date=date('Y-m-d',strtotime($request->from_date));
            $to_date=date('Y-m-d',strtotime($request->to_date));
        }

        else
        {

            $from_date=date('Y-m-d',strtotime(now()->subMonth()));
            $to_date=date('Y-m-d');
        }

        $events=events::orderBy('event_start_date','DESC')->get(); // get all events
        $event=events::orderBy('event_start_date','ASC')->get(); //
        if($request->event_name)
        {
            $event_id=$request->event_name;
        }
        else
        {

            foreach ($event as $key) {
               $event_id=$key->event_id;
            }
        }

        session(['from_date'=>$from_date]); session(['to_date'=>$to_date]); session(['event_id'=>$event_id]);


        $eventName=events::get()->where('event_id',$event_id);
        foreach($eventName as $value){ $event_name=$value->event_name; }

        $bills=advice_notes::where('event',$event_id)->where('advice_note_status','!=','CENCELED')
        ->whereBetween('advice_note_created_at',[$from_date." 00:00:00",$to_date." 23:59:59"])
        ->get(['total_amount','amount_paid','currency','total_vat_amount']);

        $nvoiceCount=advice_notes::where('event',$event_id)->where('advice_note_status','!=','CENCELED')
        ->whereBetween('advice_note_created_at',[$from_date." 00:00:00",$to_date." 23:59:59"])->count();


        return view('reports.exhibition_collection_report',compact('events','event_name','from_date','to_date','bills','event_id','nvoiceCount'));
    }
}
