<?php

namespace App\Http\Controllers\guideline;

use App\Http\Controllers\Controller;
use App\Models\guidelines\policies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class sacPolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('guideline.terms_and_condition');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->user()->cannot('CREATE_POLICY',User::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_POLICY'));
        }
        $content=policies::get(['policy_id','policy_content']);
        return view('guideline.add-policy',compact('content'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('CREATE_POLICY',User::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_POLICY'));
        }
        //

        if($request->id)
        {
            $policy=policies::find($request->id);
           $updated= $policy->update([
                'policy_content'=>$request->policy_content,
            ]);

            if($updated)
            {
                return back()->with('success','Policy updated successfully!');
            }

        }

        else
        {
            $saved=policies::create([
                'policy_id'=>Str::uuid(),
                'created_by'=>Auth::user()->user_uuid,
                'policy_content'=>$request->policy_content,
            ]);
            if($saved)
            {
                return back()->with('success','Policy contents saved successfully!');
            }
            else
            {
                return back()->with('fail','Something went wrong!');
            }
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
        $policy=policies::find($id);
        $deleted=$policy->delete();
        if($deleted)
        {
            return back()->with('success','Policy deleted successfully!');
        }
        else
        {
            return back()->with('fail','Something went wrong!');
        }
    }
}
