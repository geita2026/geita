<?php

namespace App\Http\Controllers\controls;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Json;

class permissionController extends Controller
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
        //
        if($request->user()->cannot('View Permissions',Permission::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        $permissions=Permission::orderBy('name','ASC')->paginate(25);
        return view('controls.permissions',compact('permissions'));
    }


     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function fetchPermissions(){
        $permissions=DB::table('permissions')->get();
        return response()->json(['status'=>1,'permissions'=>$permissions]);
     }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        if($request->user()->cannot('Create Permissions',Permission::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        $permissions=DB::table('permissions')->get();
        return response()->json(['status'=>0,'permissions'=>$permissions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if($request->user()->cannot('Create Permissions',Permission::class))
        {
            return back()->with('fail',config('Messages.401'));
        }

        $validator=Validator::make($request->all(),
            [
                'permission_name'=>'required|unique:permissions,name',
            ]);

            if($validator->fails())
            {
                return response()->json(['status'=>0,'error'=>$validator->errors()->toArray()]);
            }
            else
            {

            Permission::create(['name' =>$request->input('permission_name')]);
             return response()->json(['status'=>1,'success'=>'Permission Registered Successfully!']);
            // return back()->with('success','Permission Registered Successfully!');
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
        //
        if($request->user()->cannot('Update Permissions',Permission::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        // return $request;
        $permission=Permission::find($id);
        // return $permission;
        $updated=$permission->update(['name'=>$request->permission_name]);
        return back()->with('success','Permission Updated successfully!');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        //
        if($request->user()->cannot('Delete Permissions',Permission::class))
        {
            abort(403);
        }
        try {
            $permission=Permission::find($id);
       $deleted= $permission->delete();
        if($deleted)
        {
            return back()->with('success','Permission deleted successfully!');
        }
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong please try again');
        }

    }
}
