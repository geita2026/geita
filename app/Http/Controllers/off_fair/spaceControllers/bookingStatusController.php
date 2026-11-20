<?php

namespace App\Http\Controllers\off_fair\spaceControllers;
use App\Http\Controllers\Controller;
use App\Models\off_fair\booking\off_fair_space_bookings;
use App\Models\off_fair\off_fair_spaces;
use Illuminate\Http\Request;

class bookingStatusController extends Controller
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
        if($request->user()->cannot('Off Fair Booking Status',User::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $spaces=off_fair_spaces::get();
        $booked_spaces=off_fair_space_bookings::get()->where('start_date','>=',date('Y-m-d'));
        return view('Off-Fair.off_fair_booking.booking-schedules',compact('spaces','booked_spaces'));
    }
}
