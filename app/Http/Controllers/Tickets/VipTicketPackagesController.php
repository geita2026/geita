<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Collections\Income;
use App\Models\Tickets\VipTicketPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class VipTicketPackagesController extends Controller
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
        if ($request->user()->cannot('View Vip Packages', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $packages = VipTicketPackage::get();
        $incomes = Income::get();
        return view('Tickets.VipTicketsPackages', compact('packages', 'incomes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Create Vip Packages', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $request->validate([
            'package_name' => 'required',
            'package_cost' => 'required',
            'package_descriptions' => 'max:200|required',
            'gfs_code' => 'required'
        ]);

        try {
            VipTicketPackage::create([
                'vip_package_id' => Str::uuid(),
                'package_name' => $request->package_name,
                'package_color' => $request->package_color ?? "#FFFFFF",
                'package_discriptions' => $request->package_descriptions,
                'package_cost' => $request->package_cost,
                'currency' => $request->currency,
                'gfs_code' => $request->gfs_code,
                'package_description_sw' => $request->package_description_sw,
                'packageStatus' => $request->packageStatus,
            ]);
            return back()->with('success', 'Package added successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong');
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
            if ($request->user()->cannot('update-package', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            VipTicketPackage::find($id)->update([
                'package_name' => $request->package_name,
                'package_discriptions' => $request->package_descriptions,
                'package_cost' => $request->package_cost,
                'currency' => $request->currency,
                'gfs_code' => $request->gfs_code,
                'packageStatus' => $request->packageStatus,
                'package_description_sw' => $request->package_description_sw,
            ]);
            return back()->with('success', 'Package Updated successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
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
        if ($request->user()->cannot('Create Vip Packages', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        try {
            VipTicketPackage::find($id)->delete();
            return back()->with('success', 'Package Deleted successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong!');
        }
    }
}
