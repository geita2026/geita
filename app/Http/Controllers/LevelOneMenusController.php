<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storelevel_one_menusRequest;
use App\Http\Requests\Updatelevel_one_menusRequest;
use App\Models\setting\level_one_menus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class LevelOneMenusController extends Controller
{
    /***
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
        if ($request->user()->cannot('View All Menu', Role::class)) {
            return back()->with(config('Messages.401'));
        }
        //
        $level_one_menus = level_one_menus::get();
        return view('controls.level_one_menues', compact('level_one_menus'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('Create Menu', Role::class)) {
            return back()->with(config('Messages.401'));
        }
        //
        return view('controls.register_level_one_menu');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Storelevel_one_menusRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Storelevel_one_menusRequest $request)
    {
        if ($request->user()->cannot('Create Menu', Role::class)) {
            return back()->with(config('Messages.401'));
        }
        //

        $request->validate([
            'level_one_common_name' => 'required|max:50',
            'level_one_menu_status' => 'required',
            'level_one_icon_name' => 'required',
            'level_one_menu_link' => 'required',
            'level_one_gate_name' => 'required',
        ]);

        try {
            $register = level_one_menus::create([
                'level_one_menu_id' => Str::uuid(),
                'level_one_common_name' => $request->level_one_common_name,
                'level_one_menu_status' => $request->level_one_menu_status,
                'level_one_icon_name' => $request->level_one_icon_name,
                'level_one_menu_link' => $request->level_one_menu_link,
                'level_one_gate_name' => $request->level_one_gate_name,
                'moduleNo' => $request->moduleNo,
            ]);
            return redirect()->route('system_menus_level_one.index')->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail', 'Something went wrong, please try after some minutes');
        }
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\setting\level_one_menus  $level_one_menus
     * @return \Illuminate\Http\Response
     */
    public function edit(level_one_menus $level_one_menus, Request $request, $id)
    {
        try {
            if ($request->user()->cannot('Update Menu', Role::class)) {
                return back()->with(config('Messages.401'));
            }
            //
            $level_one_menus = level_one_menus::get()->where('level_one_menu_id', $id);
            return view('controls.edit_level_one_menu', compact('level_one_menus'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Updatelevel_one_menusRequest  $request
     * @param  \App\Models\setting\level_one_menus  $level_one_menus
     * @return \Illuminate\Http\Response
     */
    public function update(Updatelevel_one_menusRequest $request, $id)
    {
        if ($request->user()->cannot('Create Menu', Role::class)) {
            return back()->with(config('Messages.401'));
        }
        //
        $request->validate([
            'level_one_common_name' => 'required|max:50',
            'level_one_menu_status' => 'required',
            'level_one_icon_name' => 'required',
            'level_one_menu_link' => 'required',
            'level_one_gate_name' => 'required',
            'moduleNo' => 'required'
        ]);
        try {
            level_one_menus::find($id)->update([
                'level_one_common_name' => $request->level_one_common_name,
                'level_one_menu_status' => $request->level_one_menu_status,
                'level_one_icon_name' => $request->level_one_icon_name,
                'level_one_menu_link' => $request->level_one_menu_link,
                'level_one_gate_name' => $request->level_one_gate_name,
                'moduleNo' => $request->moduleNo,
            ]);
            return redirect()->route('system_menus_level_one.index')->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\setting\level_one_menus  $level_one_menus
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('Create Menu', Role::class)) {
                return back()->with(config('Messages.401'));
            }
            level_one_menus::find($id)->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
