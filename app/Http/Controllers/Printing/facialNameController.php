<?php

namespace App\Http\Controllers\Printing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportFacialNames;
use App\Models\Printing\FacialName;
use App\Models\Printing\FacialNameGroup;

class facialNameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request)
    {
        if($request->user()->cannot('View Facial Names',Permission::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $groups=FacialNameGroup::get();
        //
        $companies=FacialName::leftJoin('facial_name_groups','facial_name_groups.group_id','=','facial_names.group_id')->get();
        return view('FacialNames.facial_names',compact('companies','groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->user()->cannot('View Facial Names',Permission::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        $companies=FacialName::leftJoin('facial_name_groups','facial_name_groups.group_id','=','facial_names.group_id')->get();
        return view('FacialNames.print_facial_name',compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Facial Names',Permission::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        try {
            $request->validate([
                'file'=>'file'
            ]);
            session(['group_id'=>$request->group_id]);
            // return session('group_id');
            Excel::import(new ImportFacialNames,
            $request->file('file')->store('files'));
            return back()->with('success','File uploaded successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong! ERROR: '.$th->getMessage());
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
        if($request->user()->cannot('Delete Facial Names',Permission::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        try {
            $name=FacialName::find($id);
        $name->delete();
        return  back()->with('success','Facial Name deleted successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return  back()->with('fail','SOmething went wrong, ERROR: '.$th->getMessage());
        }
    }
}
