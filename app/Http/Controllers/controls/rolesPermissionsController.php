<?php

namespace App\Http\Controllers\controls;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Contracts\Role;
use Illuminate\Support\Facades\DB;

class rolesPermissionsController extends Controller
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
        if($request->user()->cannot('VIEW_ROLES_PERMISSIONS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_ROLES_PERMISSIONS'));
        }

        //
        $roles=DB::table('roles')->get();
        $permissions=DB::table('permissions')->get();
        return view('controls.role_permissions',compact('roles','permissions'));
    }
}
