<?php

namespace App\Http\Controllers\off_fair\booking;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\Companies\CompanyAdmin;
use App\Models\off_fair\booking\booked_utilities;
use App\Models\off_fair\booking\off_fair_space_bookings;
use Google\Service\Directory\Role;
use Illuminate\Http\Request;
use App\Models\off_fair\customers\off_fair_customers;
use App\Models\off_fair\off_fair_activities;
use App\Models\off_fair\off_fair_spaces;
use App\Models\off_fair\off_fair_utilities;
use App\Models\off_fair\spaces\off_fair_space_categories;
use App\Models\space\space_categories;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class off_fair_space_booking_Controller extends Controller
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
        session(['moduleNo' => 1]);
        if ($request->user()->cannot('Book Space', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $user_id = Auth::user()->user_uuid;
        if (Auth::user()->account_type==1) {
            $customers = companies::orderBy('companyName','ASC')
            ->where('status',1)
            ->get(['companyID','companyName']);
        }
        elseif (Auth::user()->account_type==2) {
            $customers = companies::where('status',1)->whereIn('companyID',CompanyAdmin::where('adminId',$user_id)->get('companyId'))
            ->get(['companyID','companyName']);
        }

        $booked_spaces = off_fair_space_bookings::leftJoin('off_fair_spaces', 'off_fair_space_bookings.off_fair_space_id', '=', 'off_fair_spaces.space_id')
            ->leftJoin('off_fair_activities', 'off_fair_space_bookings.customer_activity', '=', 'off_fair_activities.activity_id')
            ->leftJoin('space_size_category_costs', 'off_fair_spaces.space_category_cost', '=', 'space_size_category_costs.space_size_category_cost_id')
            ->where('advice_note_id', '=', Null)
            ->where('space_booked_by', Auth::user()->user_uuid)
            ->get([
                'off_fair_space_bookings.off_fair_space_id',
                'space_name',
                'space_cost',
                'off_fair_booking_id',
                'space_id',
                'start_date',
                'end_date',
                'activity_name',
                'off_fair_customer_id',
                'space_size',
                'off_fair_space_bookings.advice_note_id',
                'space_category',
                'activity_cost',
                'customer_activity',
                'space_category',
                'size_range_cost','durationCount'
            ]);

        $activities = off_fair_activities::get(['activity_id', 'activity_name', 'activity_cost']);

        $utilities = booked_utilities::leftJoin('off_fair_utilities', 'booked_utilities.booked_utility_id', '=', 'off_fair_utilities.utility_id')
            ->get(['booked_utility_id', 'utility_name', 'utility_cost', 'utility_priolity', 'advice_note_id', 'booked_utilities.user_id'])
            ->where('advice_note_id', Null)
            ->where('user_id', Auth::user()->user_uuid);
        $myCompanies = $customers;
        

        return view('Off-Fair.off_fair_booking.BookedOffFairSpace', compact('booked_spaces', 'myCompanies', 'customers', 'activities', 'utilities'));
    }

    public function create(Request $request){
        $search = $request->get('q');
        $data=off_fair_spaces::orderBy('space_name','ASC')->where(function ($query) use ($search) {
            $query->orWhere('space_name', 'LIKE', '%' . $search . '%');
            $query->orWhere('space_cost', 'LIKE', '%' . $search . '%');
            $query->orWhere('space_descriptions', 'LIKE', '%' . $search . '%');
        })->get(['space_id','space_name','spaceCostWeekend','space_cost']);
        return response()->json($data);
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
            if ($request->user()->cannot('Book Space', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }

            $validator = Validator::make($request->all(), [
                'customer_name' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'selectedSpace'=>'required'
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }

            # check date ranges
            if ($request->start_date > $request->end_date) {
                return back()->with('fail', 'Please select dates correctly and repeate again');
            }
            # end checking date

            # ---- CHECK ANY BOOKING AVAILABLE ------ #
            if (off_fair_space_bookings::where('space_booked_by', '=', Auth::user()->user_uuid)
                ->where('advice_note_id', '=', Null)->exists()
            ) {
                return back()->with('fail', 'There is another Booking in queue.... Kindly complete it to proceed!');
            }
            # ---- END CHECK ANY BOOKING AVAILABLE ------ #

            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date);
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date);
            $durationCount = $startDate->diff($endDate)->days + 1;
            $month=0; session(['month'=>0]);
            if ($durationCount>=31) {
                $month=1; 
                session(['month'=>1]);
                $durationCount= ceil($durationCount/31);
            }

            // cheking if their is another booking in the same date
            $rangeStart = Carbon::parse($request->start_date);
            $rangeEnd = Carbon::parse($request->end_date);
            $bookedSpace = off_fair_spaces::leftJoin('off_fair_space_bookings', 'off_fair_spaces.space_id', '=', 'off_fair_space_bookings.off_fair_space_id')
            ->where('space_id',$request->selectedSpace)
            ->where(function ($query) use ($rangeStart, $rangeEnd) {
                $query->whereBetween('start_date', [$rangeStart, $rangeEnd])
                    ->orWhereBetween('end_date', [$rangeStart, $rangeEnd])
                    ->orWhere(function ($q) use ($rangeStart, $rangeEnd) {
                        $q->where('start_date', '<=', $rangeStart)
                            ->where('end_date', '>=', $rangeEnd);
                    });
            })->first();

            if ($bookedSpace) {
                return back()->with('fail', "This day is already booked ($bookedSpace->start_date - $bookedSpace->end_date), Please select Other spaces are available for booking");
            }
            $id = Str::uuid();
            off_fair_space_bookings::create([
                'off_fair_booking_id' => $id,
                'off_fair_space_id' => $request->selectedSpace,
                'space_booked_by' => Auth::user()->user_uuid,
                'off_fair_customer_id' => $request->customer_name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'durationCount'=>$durationCount,
                'customer_activity' => $request->customer_activity,
            ]);
            // end of inserting data
            if ($month==1) {
               $space=off_fair_space_bookings::leftJoin('off_fair_spaces','off_fair_space_bookings.off_fair_space_id','=','off_fair_spaces.space_id')
               ->where('off_fair_booking_id',$id)->first();
               
               if ($space->space_cost!=30000) {
                $utilities = off_fair_utilities::get(['utility_id', 'utility_priolity'])->where('utility_priolity', '=', 'Mandatory');
                foreach ($utilities as $value) {
                    booked_utilities::create([
                        'utility_booking_id' => Str::uuid(),
                        'booked_utility_id' => $value->utility_id,
                        'booked_by' => $request->customer_name,
                        'user_id' => Auth::user()->user_uuid,
                        'booking_status' => 'PENDING',
                    ]);
                }
               }
            }
            session(['duration'=>$durationCount]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        session(['moduleNo' => 1]);
        session(['selectedSpace' => $id]);
        if ($request->user()->cannot('Book Space', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $user_id = Auth::user()->user_uuid;
        if (Auth::user()->account_type==1) {
            $customers = companies::orderBy('companyName','ASC')
            ->where('status',1)
            ->get(['companyID','companyName']);
        }
        elseif (Auth::user()->account_type==2) {
            $customers = companies::where('status',1)->whereIn('companyID',CompanyAdmin::where('adminId',$user_id)->get('companyId'))
            ->get(['companyID','companyName']);
        }

        $booked_spaces = off_fair_space_bookings::leftJoin('off_fair_spaces', 'off_fair_space_bookings.off_fair_space_id', '=', 'off_fair_spaces.space_id')
            ->leftJoin('off_fair_activities', 'off_fair_space_bookings.customer_activity', '=', 'off_fair_activities.activity_id')
            ->leftJoin('space_size_category_costs', 'off_fair_spaces.space_category_cost', '=', 'space_size_category_costs.space_size_category_cost_id')
            ->where('advice_note_id', '=', Null)
            ->where('space_booked_by', Auth::user()->user_uuid)
            ->get([
                'off_fair_space_bookings.off_fair_space_id',
                'space_name',
                'space_cost',
                'off_fair_booking_id',
                'space_id',
                'start_date',
                'end_date',
                'activity_name',
                'off_fair_customer_id',
                'space_size',
                'off_fair_space_bookings.advice_note_id',
                'space_category',
                'activity_cost',
                'customer_activity',
                'space_category',
                'size_range_cost','durationCount'
            ]);

        $activities = off_fair_activities::get(['activity_id', 'activity_name', 'activity_cost']);

        $utilities = booked_utilities::leftJoin('off_fair_utilities', 'booked_utilities.booked_utility_id', '=', 'off_fair_utilities.utility_id')
            ->get(['booked_utility_id', 'utility_name', 'utility_cost', 'utility_priolity', 'advice_note_id', 'booked_utilities.user_id'])
            ->where('advice_note_id', Null)
            ->where('user_id', Auth::user()->user_uuid);
        $myCompanies = $customers;
        $spaces = off_fair_spaces::where('space_id', $id)->first(['space_name', 'space_cost', 'space_status', 'space_id', 'space_size']);

        return view('Off-Fair.off_fair_booking.BookedOffFairSpace', compact('booked_spaces', 'myCompanies', 'customers', 'activities', 'utilities', 'spaces'));
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
            if ($request->user()->cannot('Cancel Booking', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            booked_utilities::where('user_id', '=', Auth::user()->user_uuid)
                ->where('advice_note_id', '=', NULL)->delete();
            off_fair_space_bookings::find($id)->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
