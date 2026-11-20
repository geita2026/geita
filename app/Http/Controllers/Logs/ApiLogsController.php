<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\Logs\ApiLogs;
use Illuminate\Http\Request;

class ApiLogsController extends Controller
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
        if($request->user()->cannot('View Api Logs',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

     $data=ApiLogs::orderBy('response_date','DESC')->paginate(20);
     $response=$data;
     return view('audit.api_logs',compact('response'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->user()->cannot('Delete Api Logs',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        try {
            ApiLogs::find($id)->delete();
            return back()->with('success','Api Log deleted successfull!');
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong!');
        }
    }
}
