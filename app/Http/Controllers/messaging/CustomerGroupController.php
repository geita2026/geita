<?php

namespace App\Http\Controllers\messaging;

use App\Http\Controllers\Controller;
use App\Models\Notifications\CustomerGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerGroupController extends Controller
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
        if($request->user()->cannot('VIEW_CUSTOMER_GROUP_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_CUSTOMER_GROUP_SUBMENU'));
        }

        //
        $groups=CustomerGroups::get();
        return view('Messaging.customer_groups',compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('CREATE_CUSTOMER_GROUP',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_CUSTOMER_GROUP'));
        }
        try {
            $request->validate([
                'group_name'=>'required',
            ]);
            CustomerGroups::create([
                'group_id'=>Str::uuid(),
                'group_name'=>$request->group_name,
                'group_descriptions'=>$request->group_descriptions
            ]);
            return back()->with('success','Group registered Successfully!');
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
        if($request->user()->cannot('UPDATE_CUSTOMER_GROUP',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_CUSTOMER_GROUP'));
        }

        try {
            $group=CustomerGroups::find($id);
            $group->update([
                'group_name'=>$request->group_name,
                'group_descriptions'=>$request->group_descriptions,
            ]);
            return back()->with('success','Group updated successfully!');
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
        if($request->user()->cannot('DELETE_CUSTOMER_GROUP',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DELETE_CUSTOMER_GROUP'));
        }

        try {
            $group=CustomerGroups::find($id);
            $group->delete();
            return back()->with('success','Group deleted successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong, ERROR: '.$th->getMessage());
        }
    }
}
