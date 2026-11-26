<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\space\booths;
use App\Models\space\pavilions;
use Illuminate\Http\Request;
use App\Models\booking\booked_booths;
use App\Models\booking\booked_pavilions;
use App\Models\settings\events;

class pavilionReportController extends Controller
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
        if($request->user()->cannot('VIEW_PAVILION_REPORT_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_PAVILION_REPORT_SUBMENU'));
        }
        //

        $from_date=date('Y-m-d',strtotime($request->from_date));
        $to_date=date('Y-m-d',strtotime($request->to_date));

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

        $pavilions=pavilions::leftJoin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')
        ->leftJoin('user_spaces','pavilions.pavilionID','=','user_spaces.pavilionID')
        ->leftJoin('users','user_spaces.assignedTo','=','users.user_uuid')
        ->leftJoin('roles','users.role','=','roles.id')
        ->get();

        $booked_booths=pavilions::rightJoin('booths','pavilions.pavilionID','=','booths.pavilionID')
        ->get();

        return view('reports.pavilions-report',compact('pavilions','booked_booths','from_date','to_date'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        if($request->user()->cannot('VIEW_PAVILION_REPORT_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_PAVILION_REPORT_SUBMENU'));
        }
        return view('reports.booked-unbooked');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        //
        if($request->user()->cannot('VIEW_PAVILION_REPORT_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_PAVILION_REPORT_SUBMENU'));
        }
        $pavilions=pavilions::leftJoin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')

        ->leftJoin('user_spaces','pavilions.pavilionID','=','user_spaces.pavilionID')
        ->leftJoin('users','user_spaces.assignedTo','=','users.user_uuid')
        ->leftJoin('roles','users.role','=','roles.id')
        ->orderBy('pavilionName','ASC')
        ->get();

        $booked_booths=booked_booths::leftJoin('booths','booked_booths.boothID','=','booths.boothID')
        ->get();

        $booked_pavilion=booked_pavilions::get();
        return view('reports.booked-unbooked',compact('pavilions','booked_pavilion','booked_booths'));
    }
}
