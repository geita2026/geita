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
        if($request->user()->cannot('VIEW_FACIAL_NAME_GROUP_SUBMENU',Permission::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_FACIAL_NAME_GROUP_SUBMENU'));
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
        if($request->user()->cannot('CREATE_FACIAL_NAME_GROUP',Permission::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_FACIAL_NAME_GROUP'));
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
        if($request->user()->cannot('UPDATE_FACIAL_NAME_GROUP',Permission::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_FACIAL_NAME_GROUP'));
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
        if($request->user()->cannot('DELETE_FACIAL_NAME_GROUP',Permission::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DELETE_FACIAL_NAME_GROUP'));
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
