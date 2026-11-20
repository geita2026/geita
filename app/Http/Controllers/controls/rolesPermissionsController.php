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
        if($request->user()->cannot('View Roles Permissions',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        //
        $roles=DB::table('roles')->get();
        $permissions=DB::table('permissions')->get();
        return view('controls.role_permissions',compact('roles','permissions'));
    }
}
