<?php

namespace App\Http\Controllers\booking;

use App\Http\Controllers\Controller;
use App\Models\advice_note\service_charges;
use App\Models\companies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\space\additional_services;
use App\Models\settings\events;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class serviceBookingController extends Controller
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
            if ($request->user()->cannot('Book Services', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            //
            $booked_services = service_charges::leftJoin('companies', 'service_charges.company_id', '=', 'companies.companyID')
                ->leftJoin('additional_services', 'service_charges.service_id', '=', 'additional_services.serviceID')
                ->leftJoin('users', 'service_charges.user_id', '=', 'users.user_uuid')
                ->where('service_charges.user_id', Auth::user()->user_uuid)->where('advice_note_id', Null)->get();
            $i = 0;
            foreach ($booked_services as $key) {
                $currency = $key->type_of_currency;
                $i++;
            }
            if ($request->user()->cannot('Book-all-service', Role::class)) {
                if ($i > 0) {
                    $services = additional_services::orderBy('serviceName', 'ASC')->where('service_event', session('liveEvent'))
                        ->where('type_of_currency', $currency)->where('gfs_code', '!=', Null)->where('self_booking', 1)
                        ->get(['serviceID', 'serviceName', 'serviceCost', 'type_of_currency', 'service_priolity', 'service_type', 'service_event']);
                } else {
                    $services = additional_services::orderBy('serviceName', 'ASC')->where('service_event', session('liveEvent'))
                        ->where('gfs_code', '!=', Null)->where('self_booking', 1)
                        ->get(['serviceID', 'serviceName', 'serviceCost', 'type_of_currency', 'service_priolity', 'service_type', 'service_event']);
                }
            } else {
                if ($i > 0) {
                    $services = additional_services::orderBy('serviceName', 'ASC')->where('service_event', session('liveEvent'))
                        ->where('type_of_currency', $currency)->where('gfs_code', '!=', Null)
                        ->get(['serviceID', 'serviceName', 'serviceCost', 'type_of_currency', 'service_priolity', 'service_type', 'service_event']);
                } else {
                    $services = additional_services::orderBy('serviceName', 'ASC')->where('service_event', session('liveEvent'))
                        ->where('gfs_code', '!=', Null)
                        ->get(['serviceID', 'serviceName', 'serviceCost', 'type_of_currency', 'service_priolity', 'service_type', 'service_event']);
                }
            }
            $i = 0;
            foreach ($services as $value) {
                $i++;
            }
            if ($i == 0) {
                return back()->with('fail', 'No service found under this event, contact system admin for more help.');
            }

            return view('booking.book-service', compact('services', 'booked_services'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('Book Services', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        //
        $booked_services = service_charges::leftJoin('companies', 'service_charges.company_id', '=', 'companies.companyID')
            ->leftJoin('additional_services', 'service_charges.service_id', '=', 'additional_services.serviceID')
            ->leftJOin('users', 'service_charges.user_id', '=', 'users.user_uuid')
            ->where('service_charges.user_id', Auth::user()->user_uuid)
            ->where('advice_note_id', Null)
            ->get();

        $services = additional_services::orderBy('serviceName', 'ASC')
            ->where('gfs_code', '!=', Null)
            ->get(['serviceID', 'serviceName', 'serviceCost', 'type_of_currency', 'service_priolity', 'service_type']);


        $companies = companies::orderBy('companyName', 'ASC')
            ->get(['companyID', 'companyName']);

        $events = events::get();
        return view('booking.book-service', compact('companies', 'services', 'booked_services', 'events'));
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
            if ($request->user()->cannot('Book Services', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            if (config('global.BOOKING_STATUS') == 0) {
                return back()->with('fail', 'Exciting news! Our bookings aren’t open just yet, but stay tuned—reserve your spot soon for an unforgettable experience!');
            }
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required',
                'service_name' => 'required',
                'service_quantity' => 'required|numeric|min:1',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            service_charges::create([
                'service_charge_id' => Str::uuid(),
                'company_id' => $request->customer_name,
                'user_id' => Auth::user()->user_uuid,
                'service_id' => $request->service_name,
                'exhibition_year' => session('liveEvent'),
                'service_quantity' => $request->service_quantity,
            ]);
            return back()->with('success', config('Messages.200'));
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
        if ($request->user()->cannot('Book Services', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        session(['event_id' => $id]);
        $event_id = session('event_id');

        $booked_services = service_charges::leftJoin('companies', 'service_charges.company_id', '=', 'companies.companyID')
            ->leftJoin('additional_services', 'service_charges.service_id', '=', 'additional_services.serviceID')
            ->leftJoin('users', 'service_charges.user_id', '=', 'users.user_uuid')
            ->where('service_charges.user_id', Auth::user()->user_uuid)->where('advice_note_id', Null)->get();
        $i = 0;
        foreach ($booked_services as $key) {
            $currency = $key->type_of_currency;
            $i++;
        }

        if ($i > 0) {
            $services = additional_services::orderBy('serviceName', 'ASC')->where('service_event', $event_id)
                ->where('type_of_currency', $currency)->where('gfs_code', '!=', Null)->where('self_booking', 1)
                ->get(['serviceID', 'serviceName', 'serviceCost', 'type_of_currency', 'service_priolity', 'service_type', 'service_event']);
        } else {

            $services = additional_services::orderBy('serviceName', 'ASC')->where('service_event', $event_id)
                ->where('gfs_code', '!=', Null)->where('self_booking', 1)
                ->get(['serviceID', 'serviceName', 'serviceCost', 'type_of_currency', 'service_priolity', 'service_type', 'service_event']);
        }

        $i = 0;
        foreach ($services as $value) {
            $i++;
        }
        if ($i == 0) {
            return redirect()->route('book-services.index')->with('fail', 'No service found under this event, contact system admin for more help.');
        }



        $companies = companies::orderBy('companyName', 'ASC')->get(['companyID', 'companyName']);

        return view('booking.book-service', compact('companies', 'services', 'booked_services'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('Cancel Service Booking', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //

        $service = service_charges::find($id);
        $delete = $service->delete();
        if ($delete) {
            return back()->with('success', 'Booked service Canceled Successfully!');
        }
    }
}
