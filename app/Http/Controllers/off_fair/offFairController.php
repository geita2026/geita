<?php

namespace App\Http\Controllers\off_fair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\off_fair\booking\off_fair_space_bookings;
use App\Models\off_fair\customers\off_fair_customers;
use Spatie\Permission\Models\Role;

class offFairController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if($request->user()->cannot('View Off Fair Booking',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        $booked_spaces=off_fair_space_bookings::get();
        $customers=off_fair_customers::get(['customer_id','customer_name']);
        return view('Off-Fair.off-fair',compact('booked_spaces','customers'));
        //
    }
}
