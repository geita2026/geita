<?php

namespace App\Http\Controllers\off_fair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\off_fair\off_fair_utilities;
use phpDocumentor\Reflection\Types\Null_;
use Spatie\Permission\Contracts\Role;

class utilityController extends Controller
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
        if($request->user()->cannot('Manage Utilities',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $utilities=off_fair_utilities::orderBy('utility_name','ASC')
        ->get(['utility_created_at','utility_id','utility_name','utility_cost','utility_description','utility_priolity'])
        ->where('existence_status',Null);
        return view('Off-Fair.off_fair_spaces.off_fair_utilities',compact('utilities'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Manage Utilities',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $request->validate([
            'utility_name'=>'required|unique:off_fair_utilities,utility_name',
            'utility_cost'=>'required|numeric',
            'utility_priolity'=>'required',
            'utility_descriptions'=>'max:100',
        ]);
        $registered=off_fair_utilities::create([
            'utility_id'=>Str::uuid(),
            'utility_name'=>$request->utility_name,
            'utility_priolity'=>$request->utility_priolity,
            'utility_description'=>$request->utility_descriptions,
            'utility_cost'=>$request->utility_cost,
            'utility_created_by'=>Auth::user()->user_uuid,
        ]);

        if($registered)
        {
            return back()->with('success','Off Fiar Utility Registered successfully!');
        }
        else
        {
            return back()->with('fail','Something went wrong, it might be due to connection problems!');
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
        if($request->user()->cannot('Manage Utilities',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //

        $request->validate([
            'utility_name'=>'required',
            'utility_cost'=>'required|numeric',
            'utility_priolity'=>'required',
            'utility_descriptions'=>'max:100',
        ]);

        $utility=off_fair_utilities::find($id);
        $update=$utility->update([
            'utility_name'=>$request->utility_name,
            'utility_priolity'=>$request->utility_priolity,
            'utility_description'=>$request->utility_descriptions,
            'utility_cost'=>$request->utility_cost,
            'utility_created_by'=>Auth::user()->user_uuid,
        ]);
        if($update)
        {
            return back()->with('success','Off Fair Utility Updated Successfully!!');
        }
        else
        {
            return back()->with('fail','Something went wrong, it might be due to connection problems!!');
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
        if($request->user()->cannot('Delete Utilities',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $utility=off_fair_utilities::find($id);
        if($request->deletion_status==1)
        {
            $deleted=$utility->delete();
        }
        else
        {
            $deleted=$utility->update([
                'existence_status'=>'Partial_Deleted',
            ]);
        }

        if($deleted)
        {
            return back()->with('success','Off Fair Utility Deleted Successfully!!');
        }
        else
        {
            return back()->with('fail','Something went wrong, it might be due to connection problems!!');
        }
    }
}
