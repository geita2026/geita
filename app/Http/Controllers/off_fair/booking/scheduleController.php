<?php

namespace App\Http\Controllers\off_fair\booking;

use App\Http\Controllers\Controller;
use App\Models\off_fair\booking\off_fair_space_bookings;
use App\Models\off_fair\off_fair_spaces;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class scheduleController extends Controller
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

        if ($request->user()->cannot('View Off Fair Booking Calender', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }

        if (!empty($request->space_id)) {
            $space_id = $request->space_id;
        } else {
            $space_id = Null;
        }

          $events = off_fair_space_bookings::leftJoin('off_fair_customers', 'off_fair_space_bookings.off_fair_customer_id', '=', 'off_fair_customers.customer_id')
            ->leftJoin('off_fair_advice_notes', 'off_fair_space_bookings.advice_note_id', '=', 'off_fair_advice_notes.off_fair_advice_note_id')
            ->where('off_fair_advice_note_status', 3)
            // ->where('start_date','>=',date('Y-m-d'))
            ->where('off_fair_space_id', $space_id)->get();

        $spaces = off_fair_spaces::get();
        $space_name = off_fair_spaces::get()->where('space_id', $space_id);

        return view('Off-Fair.schedules.booking_schedules', compact('events', 'space_name', 'spaces'));
    }
}
