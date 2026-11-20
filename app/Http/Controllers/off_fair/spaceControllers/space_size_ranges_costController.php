<?php

namespace App\Http\Controllers\off_fair\spaceControllers;

use App\Http\Controllers\Controller;
use App\Models\off_fair\booking\space_size_category_costs;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class space_size_ranges_costController extends Controller
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
        if($request->user()->cannot('View Off Fair Space Costs',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $costs=space_size_category_costs::get();
        return view('Off-Fair.off_fair_spaces.space_size_ranges_cost',compact('costs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Off Fair Space Costs',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $request->validate([
            'space_size_range_name'=>'required',
            'sales_status'=>'required',
            'size_range_cost'=>'required|min:0|numeric',
        ]);

        $registered=space_size_category_costs::create([
            'space_size_category_cost_id'=>Str::uuid(),
            'area_size_name'=>$request->space_size_range_name,
            'sales_status'=>$request->sales_status,
            'size_range_cost'=>$request->size_range_cost,
            'user_id'=>Auth::user()->user_uuid
        ]);
        if($registered)
        {
            return back()->with('success','Space size range Cost saved successfully!');
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
        if($request->user()->cannot('Update Off Fair Space Costs',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //

        $cost=space_size_category_costs::find($id);
        if($cost==Null)
        {
            return back()->with('fail','Something went wrong...! please try again.');
        }
        $updated=$cost->update([
            'area_size_name'=>$request->area_size_name,
            'sales_status'=>$request->sales_status,
            'size_range_cost'=>$request->size_range_cost,
            'user_id'=>Auth::user()->user_uuid
        ]);

        if($updated)
        {
            return back()->with('success','Space size range Cost updated successfully!');
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
        if($request->user()->cannot('Delete Off Fair Space Costs',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $cost=space_size_category_costs::find($id);
        if($cost==Null)
        {
            return back()->with('fail','Something went wrong...! please try again.');
        }
        $deleted=$cost->delete();
        if($deleted)
        {
            return back()->with('success','Space size range Cost Deleted successfully!');
        }
        else
        {
            return back()->with('fail','Something went wrong...! please try again.');
        }

    }
}
