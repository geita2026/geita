<?php

namespace App\Http\Controllers\Printing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Printing\FacialNameGroup;
use Illuminate\Support\Str;

class facialNameGroupController extends Controller
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
        if($request->user()->cannot('View Facial Names Group',Permission::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $groups=FacialNameGroup::get();
        return view('FacialNames.facial_name_groups',compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Facial Names Group',Permission::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        try {
            $request->validate([
                'group_name'=>'required',
                'background_color'=>'required',
            ]);

            FacialNameGroup::create([
                'group_id'=>Str::uuid(),
                'group_name'=>$request->group_name,
                'background_color'=>$request->background_color,
                'text_color'=>$request->text_color,
            ]);
            return back()->with('success','Company group registered successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong, ERROR: '.$th->getMessage());
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
        if($request->user()->cannot('Update Facial Names Group',Permission::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        try {
            $request->validate([
                'group_name'=>'required',
                'background_color'=>'required',
            ]);
            $group=FacialNameGroup::find($id);
            $group->update([
                'group_name'=>$request->group_name,
                'background_color'=>$request->background_color,
                'text_color'=>$request->text_color,
            ]);
            return back()->with('success','Company group update successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong, ERROR: '.$th->getMessage());
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
        if($request->user()->cannot('Delete Facial Names Group',Permission::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        try {
            $group=FacialNameGroup::find($id);
            $group->delete();
            return back()->with('success','Group deleted successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong, ERROR: '.$th->getMessage());
        }
    }
}
