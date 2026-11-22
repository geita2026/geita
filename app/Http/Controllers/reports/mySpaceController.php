<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\space\pavilions;

class mySpaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if($request->user()->cannot('VIEW_MY_SPACE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_MY_SPACE'));
        }
        //
        $specific=$request->space_name;
        $spaces=pavilions::get(['pavilionName','pavilionID']);
        return view('reports.my-space',compact('spaces','specific'));
    }
    
}
