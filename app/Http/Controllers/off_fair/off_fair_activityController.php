<?php

namespace App\Http\Controllers\off_fair;

use App\Http\Controllers\Controller;
use App\Models\off_fair\off_fair_activities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class off_fair_activityController extends Controller
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
        if($request->user()->cannot('View Off Fair Activities',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $activities=off_fair_activities::get();
        return view('Off-Fair.off_fair_activities',compact('activities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Off Fair Activities',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $request->validate([
            'activity_name'=>'required',
            'activity_cost'=>'numeric',
            'activity_description'=>'max:100'
        ]);
        $registered=off_fair_activities::create([
            'activity_id'=>Str::uuid(),
            'activity_name'=>$request->activity_name,
            'activity_cost'=>$request->activity_cost,
            'activity_descriptions'=>$request->activity_descriptions,
            'activity_created_by'=>Auth::user()->user_uuid,
        ]);

        if($registered)
        {
            return back()->with('success','Off Fair activities Created successfully!');
        }
        else
        {
            return back()->with('fail','Something went wrong...! please try again.');
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
        if($request->user()->cannot('Update Off Fair Activities',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $request->validate([
            'activity_name'=>'required',
            'activity_cost'=>'numeric',
            'activity_description'=>'max:100'
        ]);
        $activity=off_fair_activities::find($id);
        $updated=$activity->update([
            'activity_name'=>$request->activity_name,
            'activity_cost'=>$request->activity_cost,
            'activity_descriptions'=>$request->activity_descriptions,
            'activity_created_by'=>Auth::user()->user_uuid,
        ]);

        if($updated)
        {
            return back()->with('success','Off Fair activities Upadated successfully!');
        }
        else
        {
            return back()->with('fail','Something went wrong...! please try again.');
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
        if($request->user()->cannot('Delete Off Fair Activities',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $activity=off_fair_activities::find($id);
        $deleted=$activity->delete();
        if($deleted)
        {
            return back()->with('success','Off Fair activities Deleted successfully!');
        }
        else
        {
            return back()->with('fail','Something went wrong...! please try again.');
        }

    }
}
