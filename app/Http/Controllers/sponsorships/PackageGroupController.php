<?php

namespace App\Http\Controllers\sponsorships;

use App\Http\Controllers\Controller;
use App\Models\sponsorships\PackageGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class PackageGroupController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($request->user()->cannot('MANAGE_PACKAGE_GROUP', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_PACKAGE_GROUP'));
            }
            $packageCategories = PackageGroup::get();
            return view('sponsorship.PackageGroups', compact('packageCategories'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('MANAGE_PACKAGE_GROUP', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_PACKAGE_GROUP'));
            }
            $packageCategories = PackageGroup::where('packageGroupId', $id)->get();
            return view('sponsorship.UpdatePackageGroups', compact('packageCategories'));
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
        if ($request->user()->cannot('MANAGE_PACKAGE_GROUP', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_PACKAGE_GROUP'));
        }
        $request->validate([
            'packageGroupName' => 'required',
            'packageGroupDescription' => 'required',
            'backgroundColor' => 'required',
            'textColor'=>'required'
        ]);
        try {
            PackageGroup::find($id)->update([
                'packageGroupName'=>$request->packageGroupName,
                'packageGroupDescription'=>$request->packageGroupDescription,
                'backgroundColor'=>$request->backgroundColor,
                'textColor'=>$request->textColor
            ]);
            return redirect()->route('package_groups.index')->with('success',config('Messages.200'));
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
    public function destroy($id)
    {
        //
    }
}
