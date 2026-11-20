<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\PointOfSale;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class locationControllers extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        try {
            if ($request->user()->cannot('Manage Location', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $locations = PointOfSale::get();
            return view('Tickets.PointSale', compact('locations'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
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
        if ($request->user()->cannot('Manage Location', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        try {
            PointOfSale::create([
                'locationId' => Str::uuid(),
                'locationName' => $request->locationName,
                'locationDescription' => $request->locationDescription,
                'locationStatus' => $request->locationStatus,
            ]);
            return back()->with('success', config('Messages.201'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
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
        if ($request->user()->cannot('Manage Location', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        try {
            PointOfSale::find($id)->update([
                'locationName' => $request->locationName,
                'locationDescription' => $request->locationDescription,
                'locationStatus' => $request->locationStatus,
            ]);

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
        if ($request->user()->cannot('Manage Location', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        try {
            PointOfSale::find($id)->delete();
            return back()->with('success', config('Messages.204'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
