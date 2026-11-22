<?php

namespace App\Http\Controllers\sponsorships;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_pavilions;
use App\Models\space\booths;
use App\Models\space\pavilions;
use App\Models\sponsorships\sponsorship_packages;
use App\Models\sponsorships\SponsorSpace;
use Google\Service\Directory\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SponsorshipSpacesController extends Controller
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
        if ($request->user()->cannot('VIEW_SPONSORSHIP_SPACE_SUBMENU', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_SPONSORSHIP_SPACE_SUBMENU'));
        }
        $packages = sponsorship_packages::leftJoin('package_groups', 'sponsorship_packages.packageGroup', '=', 'package_groups.packageGroupId')
            ->where('packageStatus', 'PUBLISHED')
            ->whereNotIn('package_id', SponsorSpace::get(['packageId']))->get();

        $sponsor_booths = SponsorSpace::Join('booths', 'sponsor_spaces.space_id', '=', 'booths.boothID')
            ->leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')->get();


        $sponsor_pavilions = SponsorSpace::leftJoin('pavilions', 'sponsor_spaces.space_id', '=', 'pavilions.pavilionID')
            ->leftJoin('sponsorship_packages', 'sponsor_spaces.packageId', '=', 'sponsorship_packages.package_id')
            ->get();

        $pavilions = pavilions::leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
            ->where('category_event', session('liveEvent'))
            ->where('bookingStatus', '!=', 'SUNSPENDED')
            ->whereNotIn('pavilionId', booked_pavilions::where('exhibition_year', session('liveEvent'))->get(['pavilionID']))
            ->whereNotIn('pavilionId', booths::get(['pavilionID']))
            ->whereNotIn('pavilionId', SponsorSpace::get(['space_id']))
            ->get();

        return view('sponsorship.sponsorship_spaces', compact('packages', 'pavilions', 'sponsor_booths', 'sponsor_pavilions'));
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
            if ($request->user()->cannot('CREATE_SPONSORSHIP_SPACE', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('CREATE_SPONSORSHIP_SPACE'));
            }

            if (!empty($request->pavilion_name)) {

                foreach ($request->pavilion_name as $value) {
                    $pavilion = pavilions::find($value);
                    $pavilion->update(['bookingStatus' => 1]);
                    SponsorSpace::create([
                        'booking_id' => Str::uuid(),
                        'space_id' => $value,
                        'packageId' => $request->packageId
                    ]);
                }
            }
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
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

        if ($request->user()->cannot('VIEW_SPONSORSHIP_SPACE_SUBMENU', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_SPONSORSHIP_SPACE_SUBMENU'));
        }

        $pavilions = pavilions::leftJoin('events', 'pavilions.event', '=', 'events.event_id')
            ->where('event_status', 1)->get(['pavilionID', 'pavilionName', 'pavilion_size']);


        $booths = booths::where('booths.pavilionID', $request->space)->get(['boothID', 'boothNo', 'boothSize']);

        return view('sponsorship.sponsorship_spaces', compact('booths', 'pavilions'));
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
            if ($request->user()->cannot('DELETE_SPONSORSHIP_SPACE', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('DELETE_SPONSORSHIP_SPACE'));
            }
            $booking = SponsorSpace::find($id);
            $booking->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
