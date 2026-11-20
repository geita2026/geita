<?php

namespace App\Http\Controllers\controls;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class rolesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->user()->cannot('Manage Roles', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $roles = DB::table('roles')->orderBy('id')->get();
        return view('controls.controls', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('Manage Roles', Role::class,)) {
            return back()->with('fail', config('Messages.401'));
        }

        $roles = DB::table('roles')->get();
        $allPermission = DB::table('permissions')->orderBy('name', 'ASC')->get();
        return view('controls.role_permissions', compact('roles', 'allPermission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Manage Roles', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $request->validate([
            'role_name' => 'required|string|max:100|unique:roles,name',
            'permission' => 'required'
        ]);

        try {
            Role::create(['name' => $request->role_name]);
            $role = Role::where('name', $request->role_name)->first();
            $role->syncPermissions($request->permission);
            return redirect()->route('roles.index')->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('Manage Roles', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        $roles = Role::where('roles.id', $id)->get();

        $myPermissions = DB::table('permissions')->leftJoin('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('role_id', $id)
            ->orderBy('permissions.name', 'ASC')->get();

        $allPermission = DB::table('permissions')->orderBy('permissions.name', 'ASC')->get();
        return view('controls.show_roles_permissions', compact('roles', 'allPermission', 'myPermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //
        if ($request->user()->cannot('View Roles', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $roles = Role::where('roles.id', $id)
            ->with('permissions')
            ->get();

        $permissions = DB::table('permissions')->orderBy('id', 'ASC')
            ->get();
        return view('controls.edit_control', compact('roles', 'permissions'));
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

        try {
            if ($request->user()->cannot('Update Roles', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $validator = Validator::make($request->all(), [
                'role_name' => 'required|string|max:100'
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            $role = Role::find($id);
            $role->update(['name' => $request->role_name]);
            $role->syncPermissions($request->permission);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('Delete Roles', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $role = role::find($id);
            if ($role->name == 'Super Admin') {
                return back()->with('fail', config('Messages.401'));
            }
            $role->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
