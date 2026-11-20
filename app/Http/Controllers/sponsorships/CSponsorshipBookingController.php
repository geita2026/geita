<?php

namespace App\Http\Controllers\sponsorships;

use App\Http\Controllers\Controller;
use App\Models\setting\Country;
use App\Models\sponsorships\sponsorship_package_contents;
use App\Models\sponsorships\sponsorship_packages;
use Google\Service\Dfareporting\Resource\Countries;
use Illuminate\Http\Request;

class CSponsorshipBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packages=sponsorship_packages::where('packageStatus',1)->get();
        $country=Country::get();
        $benefit = sponsorship_package_contents::leftJoin('sponsorship_benefits','sponsorship_package_contents.benefitId','=','sponsorship_benefits.benefitId')
        ->where('sponsorship_package_contents.benefitId','!=',Null)->get();
    
        return view('FrontEnd.Public_Pages.SponsorShips.SponsorshipsCBooking',compact('packages','country','benefit'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
