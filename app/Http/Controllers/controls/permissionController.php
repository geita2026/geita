<?php

namespace App\Http\Controllers\controls;

use App\Http\Controllers\Controller;
use App\Support\PermissionNames;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        if($request->user()->cannot('VIEW_PERMISSION_SUBMENU',Permission::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_PERMISSION_SUBMENU'));
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
        if($request->user()->cannot('CREATE_PERMISSION',Permission::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_PERMISSION'));
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

        if($request->user()->cannot('CREATE_PERMISSION',Permission::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_PERMISSION'));
        }

        $name = $request->input('permission_name');
        $name = is_string($name) ? PermissionNames::canonical($name) : $name;
        $guard = 'web';
        $validator = Validator::make(['permission_name' => $name], [
            'permission_name' => [
                'required', 'string', 'max:125',
                Rule::unique('permissions', 'name')->where('guard_name', $guard),
            ],
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        }

        Permission::create(['name' => $name, 'guard_name' => $guard]);

        return response()->json(['status' => 1, 'success' => 'Permission Registered Successfully!']);

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
        if($request->user()->cannot('UPDATE_PERMISSION',Permission::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_PERMISSION'));
        }
        $permission = Permission::findOrFail($id);
        $name = $request->input('permission_name');
        if (is_string($name)) {
            $request->merge(['permission_name' => PermissionNames::canonical($name)]);
        }
        $data = $request->validate([
            'permission_name' => [
                'required', 'string', 'max:125',
                Rule::unique('permissions', 'name')->where('guard_name', $permission->guard_name)->ignore($permission),
            ],
        ]);
        $permission->update(['name' => $data['permission_name']]);

        return back()->with('success', 'Permission Updated successfully!');

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
        if($request->user()->cannot('DELETE_PERMISSION',Permission::class))
        {
            abort(403, $this->permissionDeniedMessage('DELETE_PERMISSION'));
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
