<?php

namespace App\Http\Controllers\sponsorships;

use App\Http\Controllers\Controller;
use App\Models\Collections\Income;
use App\Models\sponsorships\PackageGroup;
use App\Models\sponsorships\sponsorship_package_contents;
use App\Models\sponsorships\sponsorship_packages;
use App\Models\sponsorships\SponsorshipBenefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class sponsorshipController extends Controller
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
        try {
            if ($request->user()->cannot('MANAGE_SPONSORSHIP', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPONSORSHIP'));
            }
            $gfs_code = Income::get();

            if ($request->packageGroup) {
                $packageGroup=$request->packageGroup;
                if ($request->packageGroup=='NULL') {
                   $packageGroup=Null;
                }
                session(['packageGroup' => $packageGroup]);
            }


            $packages = sponsorship_packages::leftJoin('package_groups','sponsorship_packages.packageGroup','=','package_groups.packageGroupId')
            ->where('packageGroup', session('packageGroup'))
                ->orderBy('package_amount', 'DESC')->orderBy('package_name', 'ASC')
                ->paginate(10);

            $packageGroups = PackageGroup::get(['packageGroupId', 'packageGroupName']);
            return view('sponsorship.sponsorship', compact('packages', 'gfs_code', 'packageGroups'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            if ($request->user()->cannot('MANAGE_SPONSORSHIP', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPONSORSHIP'));
            }
            $packageGroups = PackageGroup::get(['packageGroupId', 'packageGroupName']);
            return view('sponsorship.create-sponsorship', compact('packageGroups'));
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
        try {
            if ($request->user()->cannot('MANAGE_SPONSORSHIP', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPONSORSHIP'));
            }
            //
            $validator = Validator::make($request->all(), [
                'package_name' => 'required',
                'packageGroup' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }

            sponsorship_packages::create([
                'package_id' => Str::uuid(),
                'package_name' => $request->package_name,
                'package_description' => $request->package_description,
                'type_of_currency' => $request->type_of_currency,
                'created_by' => Auth::user()->user_uuid,
                'gfs_code' => '142301470003', // GFS CODE for Sponsorship
                'packageGroup' => $request->packageGroup,
            ]);
            return redirect()->route('sponsorships.index')->with('success', 'Sponsorship package registered successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
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
        try {
            if ($request->user()->cannot('MANAGE_SPONSORSHIP', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPONSORSHIP'));
            }
            $packages = sponsorship_packages::where('package_id', $id)->get();
            $contents = sponsorship_package_contents::leftJoin('sponsorship_benefits', 'sponsorship_package_contents.benefitId', '=', 'sponsorship_benefits.benefitId')
                ->where('package_id', $id)->where('sponsorship_benefits.benefitId', '!=', Null)->get();
            $packageCost = sponsorship_package_contents::where('package_id', $id)->sum('contentCost');
            sponsorship_packages::where('package_id', $id)->first()->update(['package_amount' => $packageCost]);
            $benefits = SponsorshipBenefit::get();

           

            return view('sponsorship.add-package-contents', compact('packages', 'contents', 'benefits'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
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
            if ($request->user()->cannot('MANAGE_SPONSORSHIP', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPONSORSHIP'));
            }
            $validator = Validator::make($request->all(), [
                'package_name' => 'required',
                'packageStatus' => 'required',
                'type_of_currency' => 'required',
                'packageGroup' => 'required'
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }

            sponsorship_packages::find($id)
            ->update([
                'package_name' => $request->package_name,
                'package_description' => $request->package_description,
                'type_of_currency' => $request->type_of_currency,
                'created_by' => Auth::user()->user_uuid,
                'packageStatus' => $request->packageStatus,
                'gfs_code' => '142301470003',
                'packageGroup' => $request->packageGroup,
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
        try {
            if ($request->user()->cannot('DELETE_SPONSORSHIP_PACKAGE', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('DELETE_SPONSORSHIP_PACKAGE'));
            }
            sponsorship_packages::find($id)->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
