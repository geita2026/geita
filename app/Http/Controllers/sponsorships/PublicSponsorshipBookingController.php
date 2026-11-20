<?php

namespace App\Http\Controllers\sponsorships;

use App\Http\Controllers\Controller;
use App\Models\sponsorships\PackageGroup;
use App\Models\sponsorships\sponsorship_packages;
use Illuminate\Http\Request;

class PublicSponsorshipBookingController extends Controller
{
    public function sponsorshipPackageGroups(Request $request)
    {
        try {
            $spaceCategories = PackageGroup::get();
            return view('sponsorship.PublicPages.SponsorshipPackageGroups',compact('spaceCategories'));
        } catch (\Throwable $th) {
            
        }
    }

    public function sponsorshipPackageList(Request $request,$packageGroupId)
    {
        try {
            $spaceCategories = PackageGroup::where('packageGroupId',$packageGroupId)->first();
            $packages=sponsorship_packages::where('packageGroup',$packageGroupId)
            ->where('packageStatus','PUBLISHED')->get();
            return view('sponsorship.PublicPages.SponsorshipPackageList',compact('spaceCategories','packages'));
        } catch (\Throwable $th) {
            
        }
    }
}
