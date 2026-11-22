<?php

namespace App\Http\Controllers\Guidelines;

use App\Http\Controllers\Controller;
use App\Models\guidelines\Guideline;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class GuidelinesController extends Controller
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
        if($request->user()->cannot('VIEW_DITF_GUIDELINE_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_DITF_GUIDELINE_SUBMENU'));
        }

        $guidelines=Guideline::get();
        return view('guideline.guidelines',compact('guidelines'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { if($request->user()->cannot('CREATE_DITF_GUIDELINE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_DITF_GUIDELINE'));
        }

        $request->validate([
            'guideline_title'=>'required',
            'guideline_descriptions'=>'required'
        ]);

        Guideline::create([
            'guideline_id'=>Str::uuid(),
            'guideline_title'=>$request->guideline_title,
            'guideline_descriptions'=>$request->guideline_descriptions,
        ]);

        return back()->with('success','Registered successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if($request->user()->cannot('VIEW_DITF_GUIDELINE_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_DITF_GUIDELINE_SUBMENU'));
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
    { if($request->user()->cannot('UPDATE_DITF_GUIDELINE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_DITF_GUIDELINE'));
        }
        try {
            Guideline::find($id)->update([
            'guideline_title'=>$request->guideline_title,
            'guideline_descriptions'=>$request->guideline_descriptions,
            ]);
            return back()->with('success','Data updated successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    { if($request->user()->cannot('DELETE_DITF_GUIDELINE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DELETE_DITF_GUIDELINE'));
        }

        try {
           Guideline::find($id)->delete();
           return back()->with('success','Data deleted successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong!');
        }

    }
}
