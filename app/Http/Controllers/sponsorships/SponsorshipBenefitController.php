<?php

namespace App\Http\Controllers\sponsorships;

use App\Http\Controllers\Controller;
use App\Models\sponsorships\sponsorship_package_contents;
use App\Models\sponsorships\sponsorship_packages;
use App\Models\sponsorships\SponsorshipBenefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class SponsorshipBenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($request->user()->cannot('MANAGE_SPONSORSHIP', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPONSORSHIP'));
            }
            $benefits = SponsorshipBenefit::get();
            return view('sponsorship.PackagesBenefits', compact('benefits'));
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
            $validator = Validator::make($request->all(), [
                'benefitTitle' => 'required|unique:sponsorship_benefits,benefitTitle',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            SponsorshipBenefit::create([
                'benefitId' => Str::uuid(),
                'benefitTitle' => $request->benefitTitle,
                'benefitDescription' => $request->benefitDescription,
                'benefitStatus' => $request->benefitCost,
            ]);
            return back()->with('success', config('Messages.200'));
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
        try {
            if ($request->user()->cannot('MANAGE_SPONSORSHIP', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPONSORSHIP'));
            }
            $validator = Validator::make($request->all(), [
                'benefitTitle' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            SponsorshipBenefit::find($id)->update([
                'benefitTitle' => $request->benefitTitle,
                'benefitDescription' => $request->benefitDescription,
                'benefitStatus' => $request->benefitCost,
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
    public function destroy($id)
    {
        try {
            $benefit = sponsorship_package_contents::leftJoin('sponsorship_benefits', 'sponsorship_package_contents.benefitId', '=', 'sponsorship_benefits.benefitId')
                ->where('sponsorship_package_contents.benefitId', $id)->get();
            foreach ($benefit as  $value) {
                $packageId = $value->package_id;
                $benefitCost = $value->benefitCost;
                $package = sponsorship_packages::find($packageId);
                $package->update(['package_amount' => $package->package_amount - $benefitCost]);
            }
            SponsorshipBenefit::find($id)->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
