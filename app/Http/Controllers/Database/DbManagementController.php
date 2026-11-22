<?php

namespace App\Http\Controllers\Database;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DbManagementController extends Controller
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
        if($request->user()->cannot('VIEW_PERMISSION_SUBMENU',Permission::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_PERMISSION_SUBMENU'));
        }
        try
        {
            $query='';
            $result=array();
            return view('DB.DatabaseManagement',compact('result','query'));
        }
        catch (\Throwable $th)
        {
           return back()->with('fail',$th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $request->validate([
            'query_statement'=>'required'
        ]);

        try
        {
            $query=$request->query_statement;
            $result=DB::select($query);
            return view('DB.DatabaseManagement',compact('result'));
        }
        catch (\Throwable $th)
        {
           return back()->with('fail',$th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $request->validate([
            'query_statement'=>'required'
        ]);

        try
        {
            $query=$request->query_statement;
            $result=DB::select($query);
            return view('DB.DatabaseManagement',compact('result','query'));
        }
        catch (\Throwable $th)
        {
           return back()->with('fail',$th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
