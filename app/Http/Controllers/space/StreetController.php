<?php

namespace App\Http\Controllers\space;

use App\Http\Controllers\Controller;
use App\Models\space\Street;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class StreetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($request->user()->cannot('Manage Spaces', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $streets=Street::get();
            return view('space.Street',compact('streets'));
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if ($request->user()->cannot('Manage Spaces', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $validetor=Validator::make($request->all(),[
                'streetName'=>'required'
            ]);
            if ($validetor->fails()) {
                return back()->with('fail',implode(',',$validetor->getMessageBag()->all()));
            }
            Street::create([
                'streetId'=>Str::uuid(),
                'streetName'=>$request->streetName,
                'streetDescription'=>$request->streetDescription,
            ]);
            return back()->with('success',config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500').$th->getMessage());
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
        try {
            if ($request->user()->cannot('Manage Spaces', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $validetor=Validator::make($request->all(),[
                'streetName'=>'required'
            ]);
            if ($validetor->fails()) {
                return back()->with('fail',implode(',',$validetor->getMessageBag()->all()));
            }
            Street::find($id)->update([
                'streetName'=>$request->streetName,
                'streetDescription'=>$request->streetDescription,
            ]);
            return back()->with('success',config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500'));
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
        try {
            if ($request->user()->cannot('Delete Street', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            Street::find($id)->delete();
            return back()->with('success',config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500'));
        }
    }
}
