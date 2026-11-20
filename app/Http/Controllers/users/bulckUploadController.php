<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use Illuminate\Support\Str;

class bulckUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
    }
    public function index()
    {
        return view('users.BulkUploads');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Users',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $request->validate([
            'my_file'=>'required|file|mimes:xlsx,xlsm,xlsb,xltx,xls'
        ]);

        $uploaded=Excel::import(new UsersImport, $request->file('my_file')->store('temp'));
        if($uploaded)
        {
            return back()->with('success','Permissions uploaded successfully in database');
        }
        else
        {
            return back()->with('fail','something went wrong!');
        }

    }
}
