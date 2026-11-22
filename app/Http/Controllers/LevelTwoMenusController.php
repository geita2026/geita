<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storelevel_two_menusRequest;
use App\Http\Requests\Updatelevel_two_menusRequest;
use App\Models\setting\level_one_menus;
use App\Models\setting\level_two_menus;
use Illuminate\Http\Request;
use App\Support\PermissionNames;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class LevelTwoMenusController extends Controller
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
        if($request->user()->cannot('VIEW_ALL_MENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_ALL_MENU'));
        }
        //
        $level_two_menus=level_two_menus::leftJoin('level_one_menuses','level_two_menuses.upper_level_id','=','level_one_menuses.level_one_menu_id')
        ->orderBy('level_two_menu_created_at','DESC')
        ->get();

        $level_one_menus=level_one_menus::orderBy('level_one_common_name','ASC')->get();

        return view('controls.level_two_menus',compact('level_two_menus','level_one_menus'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->user()->cannot('CREATE_MENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_MENU'));
        }
        //
        $level_one_menus=level_one_menus::orderBy('level_one_common_name','ASC')->get();
        return view('controls.register_level_two_menu',compact('level_one_menus'));
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Storelevel_two_menusRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Storelevel_two_menusRequest $request)
    {
        if($request->user()->cannot('CREATE_MENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_MENU'));
        }
        //

        $gate = $request->input('level_two_gate_name');
        if (is_string($gate)) {
            $request->merge(['level_two_gate_name' => PermissionNames::canonical($gate)]);
        }
        $request->validate([
            'level_two_common_name'=>'required|max:50|unique:level_two_menuses,level_two_common_name',
            'level_two_menu_status'=>'required',
            'level_two_icon_name'=>'required',
            'level_two_menu_link'=>'required',
            'level_two_gate_name'=>'required|string|max:125',
        ]);
        try {
            $register=level_two_menus::create([
                'level_two_menu_id'=>Str::uuid(),
                'upper_level_id'=>$request->upper_level_id,
                'level_two_common_name'=>$request->level_two_common_name,
                'level_two_menu_status'=>$request->level_two_menu_status,
                'level_two_icon_name'=>$request->level_two_icon_name,
                'level_two_menu_link'=>$request->level_two_menu_link,
                'level_two_gate_name'=>$request->level_two_gate_name,
            ]);
            return redirect()->route('system_menus_level_two.index',['system_menus_level_two'=>$request->upper_level_id])->with('success',config('Messages.200'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail',config('Messages.500'));
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\setting\level_two_menus  $level_two_menus
     * @return \Illuminate\Http\Response
     */
    public function edit(level_two_menus $level_two_menus,Request $request,$id)
    {
        if($request->user()->cannot('UPDATE_MENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_MENU'));
        }
        //
        $level_two_menus=level_two_menus::leftJoin('level_one_menuses','level_two_menuses.upper_level_id','=','level_one_menuses.level_one_menu_id')
        ->orderBy('level_two_menu_created_at','DESC')
        ->get()->where('level_two_menu_id',$id);

        $level_one_menus=level_one_menus::orderBy('level_one_common_name','ASC')->get(['level_one_common_name','level_one_menu_id']);

        return view('controls.edit_level_two_menu',compact('level_two_menus','level_one_menus'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Updatelevel_two_menusRequest  $request
     * @param  \App\Models\setting\level_two_menus  $level_two_menus
     * @return \Illuminate\Http\Response
     */
    public function update(Updatelevel_two_menusRequest $request, level_two_menus $level_two_menus,$id)
    {
        //
        if($request->user()->cannot('UPDATE_MENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_MENU'));
        }
        //

        $gate = $request->input('level_two_gate_name');
        if (is_string($gate)) {
            $request->merge(['level_two_gate_name' => PermissionNames::canonical($gate)]);
        }
        $request->validate([
            'upper_level_id'=>'required',
            'level_two_common_name'=>'required|max:50',
            'level_two_menu_status'=>'required',
            'level_two_icon_name'=>'required',
            'level_two_menu_link'=>'required',
            'level_two_gate_name'=>'required|string|max:125',
        ]);

        $level_two_menu=level_two_menus::find($id);

        try {
            $update=$level_two_menu->update([
                'upper_level_id'=>$request->upper_level_id,
                'level_two_common_name'=>$request->level_two_common_name,
                'level_two_menu_status'=>$request->level_two_menu_status,
                'level_two_icon_name'=>$request->level_two_icon_name,
                'level_two_menu_link'=>$request->level_two_menu_link,
                'level_two_gate_name'=>$request->level_two_gate_name,
            ]);
            return redirect()->route('system_menus_level_two.index')->with('success',config('Messages.200'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail',config('Messages.500'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\setting\level_two_menus  $level_two_menus
     * @return \Illuminate\Http\Response
     */
    public function destroy(level_two_menus $level_two_menus,Request $request, $id)
    {
        if($request->user()->cannot('DELETE_LEVEL_TWO_MENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DELETE_LEVEL_TWO_MENU'));
        }
        //

        try {
        $level_two_menu=level_two_menus::find($id)->delete();
        return redirect()->route('system_menus_level_two.index')->with('success',config('Messages.200'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail',config('Messages.500'));
        }
    }
}
