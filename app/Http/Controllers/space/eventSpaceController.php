<?php

namespace App\Http\Controllers\space;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\settings\events;

class eventSpaceController extends Controller
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
        if($request->user()->cannot('VIEW_EVENT_SPACE_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_EVENT_SPACE_SUBMENU'));
        }
        //
        $events=events::orderBy('event_start_date','ASC')->get()->where('event_status',1);
        return view('space.event-spaces',compact('events'));
    }

}
