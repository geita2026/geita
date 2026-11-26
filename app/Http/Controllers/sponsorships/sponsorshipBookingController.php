<?php

namespace App\Http\Controllers\sponsorships;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_sponsorship_packages;
use App\Models\companies;
use App\Models\sponsorships\sponsorship_package_contents;
use App\Models\sponsorships\sponsorship_packages;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\sponsorships\PackageGroup;
use Spatie\Permission\Models\Role;

class sponsorshipBookingController extends Controller
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
            if ($request->user()->cannot('BOOK_SPONSORSHIP', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('BOOK_SPONSORSHIP'));
            }
            //
            $companies = companies::orderBy('companyName', 'ASC')->get(['companyID', 'companyName']);
            $contents = sponsorship_package_contents::get();
            $packages = sponsorship_packages::orderBy('package_name', 'ASC')->where('packageStatus', 1)
                ->get(['package_name', 'package_id', 'type_of_currency', 'package_amount']);

            $packageGroups = PackageGroup::get();
            $booked_package = booked_sponsorship_packages::leftJoin('sponsorship_packages', 'booked_sponsorship_packages.package_id', '=', 'sponsorship_packages.package_id')
                ->leftJoin('companies', 'booked_sponsorship_packages.customer_id', '=', 'companies.companyID')
                ->where('approved_by', Auth::user()->user_uuid)->get()->where('advice_note_id', Null);

            return view('sponsorship.book-sponsorship-package', compact('companies', 'packages', 'contents', 'booked_package', 'packageGroups'));
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
        if ($request->user()->cannot('BOOK_SPONSORSHIP_PACKAGE', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('BOOK_SPONSORSHIP_PACKAGE'));
        }

        // add data to sponsorship booking table
        if ($request->booking) {

            $request->validate([
                'customer_name' => 'required',
                'package_name' => 'required',
            ]);

            $package = sponsorship_packages::find($request->package_name);

            if ($this->checkValidity() == 1) {
                return back()->with('fail', 'There is another booking, Kindly complete it or cancel it to proceed!');
            }
            try {
                booked_sponsorship_packages::create([
                    'booking_id' => Str::uuid(),
                    'customer_id' => $request->customer_name,
                    'package_id' => $request->package_name,
                    'exhibition_year' => session('liveEvent'),
                    'approved_by' => Auth::user()->user_uuid,
                    'customer_amount' => $package->package_amount,
                ]);
                return back()->with('success', 'Package booked successfully!');
            } catch (\Throwable $th) {
                //throw $th;
                return back()->with('fail', 'Something went wrong!') . $th->getMessage();
            }
        }

        //end

    }

    public function edit(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('BOOK_SPONSORSHIP', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('BOOK_SPONSORSHIP'));
            }
            //
            $packages = sponsorship_packages::orderBy('package_name', 'ASC')
                ->where('packageStatus', 'PUBLISHED')->where('packageGroup', $id)
                ->get(['package_name', 'package_id', 'type_of_currency', 'package_amount']);
            $booked_package = booked_sponsorship_packages::leftJoin('sponsorship_packages', 'booked_sponsorship_packages.package_id', '=', 'sponsorship_packages.package_id')
                ->leftJoin('companies', 'booked_sponsorship_packages.customer_id', '=', 'companies.companyID')
                ->where('approved_by', Auth::user()->user_uuid)
                ->where('advice_note_id', Null)->get();
                
            $contents = booked_sponsorship_packages::leftJoin('sponsorship_package_contents', 'booked_sponsorship_packages.package_id', '=', 'sponsorship_package_contents.package_id')
            ->where('approved_by',Auth::user()->user_uuid)
            ->where('advice_note_id',Null)    
            ->get(['content_description']);
        $packageBenefits=array();
        foreach ($contents as  $value) {
            array_push($packageBenefits,$value->content_description);
        }
        $packageBenefits=implode(', ',$packageBenefits);
            return view('sponsorship.BookSponsorshipPackages', compact('packages', 'booked_package','packageBenefits'));
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
        if ($request->user()->cannot('VIEW_SPONSORSHIP_BOOKING', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_SPONSORSHIP_BOOKING'));
        }

        session(['event_id' => $id]); // save event id session.
        $companies = companies::orderBy('companyName', 'ASC')->get(['companyID', 'companyName']);
        $contents = sponsorship_package_contents::get();
        $packages = sponsorship_packages::orderBy('package_name', 'ASC')->get(['package_name', 'package_id', 'from_amount', 'to_amount', 'type_of_currency']);
        $booked_package = booked_sponsorship_packages::leftJoin('sponsorship_packages', 'booked_sponsorship_packages.package_id', '=', 'sponsorship_packages.package_id')
            ->leftJoin('companies', 'booked_sponsorship_packages.customer_id', '=', 'companies.companyID')->get()->where('advice_note_id', Null);



        return view('sponsorship.book-sponsorship-package', compact('companies', 'packages', 'contents', 'booked_package'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        if ($request->user()->cannot('DELETE_SPONSORSHIP_BOOKING', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('DELETE_SPONSORSHIP_BOOKING'));
        }
        //
        $sponsor = booked_sponsorship_packages::find($id);
        if ($sponsor->delete()) {
            return back()->with('success', 'Package Deleted Successfully!');
        }
    }



    public function checkValidity()
    {
        // check if their is any booking of different company
        $booked_package = booked_sponsorship_packages::leftJoin('sponsorship_packages', 'booked_sponsorship_packages.package_id', '=', 'sponsorship_packages.package_id')
            ->leftJoin('companies', 'booked_sponsorship_packages.customer_id', '=', 'companies.companyID')
            ->where('approved_by', Auth::user()->user_uuid)
            ->where('advice_note_id', Null)->first();
        if ($booked_package) {
            return 1;
        } else {
            return 0;
        }
    }
}
