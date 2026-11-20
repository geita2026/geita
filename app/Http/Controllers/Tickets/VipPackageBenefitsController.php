<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\VipTicketBenefit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class VipPackageBenefitsController extends Controller
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
        if($request->user()->cannot('View Vip Tickets Benefits',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $benefits=VipTicketBenefit::get();
        return view('Tickets.VipTicketsPackageBenefits',compact('benefits'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Vip Tickets Benefits',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //

        try {
            VipTicketBenefit::create([
                'benefit_id'=>Str::uuid(),
                'benefit_name'=>$request->benefit_name,
                'benefit_descriptions'=>$request->benefit_descriptions,
                'benefit_cost'=>$request->benefit_cost,
                'currency'=>$request->currency,
            ]);
            return back()->with('success','Benefit added successfully!');
        } catch (\Throwable $th)
        {
            return back()->with('fail','Something went wrong!');
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
        if($request->user()->cannot('Update Vip Tickets Benefits',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //

        try {
            VipTicketBenefit::find($id)->update([
                'benefit_name'=>$request->benefit_name,
                'benefit_descriptions'=>$request->benefit_descriptions,
                'benefit_cost'=>$request->benefit_cost,
                'currency'=>$request->currency,
            ]);
            return back()->with('success','Benefit updated successfully!');
        } catch (\Throwable $th)
        {
            return back()->with('fail','Something went wrong!'.$th->getMessage());
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
        if($request->user()->cannot('Delete Vip Tickets Benefits',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        try {
            VipTicketBenefit::find($id)->delete();
            return back()->with('success','Benefit Delete successfully!');
        } catch (\Throwable $th)
        {
            return back()->with('fail','Something went wrong!');
        }
    }
}
