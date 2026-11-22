<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\APIs\Intergration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class IntergrationMappingController extends Controller
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
        if($request->user()->cannot('VIEW_INTERGRATION_MAPPING_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_INTERGRATION_MAPPING_SUBMENU'));
        }
        $intergrationMapping=Intergration::get();
        return view('settings.IntergrationMapping',compact('intergrationMapping'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('CREATE_INTERGRATION_MAPPING',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_INTERGRATION_MAPPING'));
        }

      try {
        if(Intergration::exists())
        {
            Intergration::first()->update([
                'billing_service_code'=>$request->billing_service_code,
                'billing_access_token'=>$request->billing_access_token,
                'billing_base_url'=>$request->billing_base_url,
                'intergration_created_by'=>Auth::user()->user_uuid
            ]);
        }

        else
        {
            Intergration::create([
                'intergration_id'=>Str::uuid(),
                'billing_service_code'=>$request->billing_service_code,
                'billing_access_token'=>$request->billing_access_token,
                'billing_base_url'=>$request->billing_base_url,
                'intergration_created_by'=>Auth::user()->user_uuid
            ]);
        }
        return back()->with('success','Intergration Setting updated successfully!');
      } catch (\Throwable $th) {
        return back()->with('fail','Something went wrong');
      }
    }

}
