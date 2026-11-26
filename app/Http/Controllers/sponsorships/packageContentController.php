<?php

namespace App\Http\Controllers\sponsorships;

use App\Http\Controllers\Controller;
use App\Models\settings\events;
use App\Models\sponsorships\sponsorship_package_contents;
use App\Models\sponsorships\sponsorship_packages;
use App\Models\sponsorships\SponsorshipBenefit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class packageContentController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('MANAGE_SPONSORSHIP', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPONSORSHIP'));
        }
        //

        $validator = Validator::make($request->all(), [
            'package_id' => 'required',
            'benefitTitle' => 'required',
            'benefitCost' => 'required'
        ]);
        if ($validator->fails()) {
            return back()->with('fail',implode(',',$validator->getMessageBag()->all()));
        }
        $package = sponsorship_packages::find($request->package_id);
        $exist = sponsorship_package_contents::where('package_id', $request->package_id)
            ->where('benefitId', $request->benefitTitle)->first();
        if (!$exist) {
            $package_amount = $request->benefitCost + $package->package_amount;
            $package->update([
                'package_amount' => $package_amount,
            ]);
            sponsorship_package_contents::create([
                'package_content_id' => Str::uuid(),
                'package_id' => $request->package_id,
                'benefitId' => $request->benefitTitle,
                'content_description' => $request->content_description,
                'contentCost' => $request->benefitCost,
                'created_by' => Auth::user()->user_uuid,
                'eventId' => events::where('event_status', 1)->first()->event_id,
            ]);
            return back()->with('success', config('Messages.200'));
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
            if ($request->user()->cannot('DELETE_PACKAGE_BENEFITS', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('DELETE_PACKAGE_BENEFITS'));
            }
            $benefit = sponsorship_package_contents::leftJoin('sponsorship_benefits', 'sponsorship_package_contents.benefitId', '=', 'sponsorship_benefits.benefitId')
                ->find($id);
            $package = sponsorship_packages::find($benefit->package_id);
            $currentAmount = $package->package_amount - $benefit->contentCost;
            $package->update(['package_amount' => $currentAmount]);
            $benefit->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
