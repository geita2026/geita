<?php

namespace App\Http\Controllers\space;

use App\Http\Controllers\Controller;
use App\Models\Collections\Income;
use App\Models\PriceConfiguration;
use App\Models\settings\events;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\space\additional_services;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class additionaServiceController extends Controller
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
        if ($request->user()->cannot('View Service', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $events = events::orderBy('event_start_date', 'DESC')->where('event_status', 1)->get();
        $services = additional_services::leftJoin('events', 'additional_services.service_event', '=', 'events.event_id')->get();
        $income = Income::get();
        return view('space.additional-service', compact('services', 'events', 'income'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Create Services', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //


        try {

            $validators = Validator::make($request->all(), [
                'service_name' => 'required|unique:additional_services,serviceName',
                'service_cost' => 'required|numeric',
                'service_description' => 'max:100',
                'type_of_currency' => 'required',
                'service_type' => 'required',
                'service_priolity' => 'required',
                'service_event' => 'required',
                'gfs_code' => 'required',
                'self_booking' => 'required',
                'relateToSpaceSize' => 'required'
            ]);

            if ($validators->fails()) {
                return response([
                    'status' => 0,
                    'error' => $validators->errors()->toArray()
                ]);
            }
            additional_services::create([
                'serviceID' => Str::uuid(),
                'userID' => Auth::user()->user_uuid,
                'serviceName' => $request->service_name,
                'serviceCost' => $request->service_cost,
                'serviceDescription' => $request->service_description,
                'type_of_currency' => $request->type_of_currency,
                'service_priolity' => $request->service_priolity,
                'service_type' => $request->service_type,
                'service_event' => $request->service_event,
                'gfs_code' => $request->gfs_code,
                'is_printable' => $request->is_printable,
                'self_booking' => $request->self_booking,
                'relateToSpaceSize' => $request->relateToSpaceSize
            ]);
            return response()->json([
                'status' => 1,
                'success' => 'service_registered_successfully!'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => 2,
                'fail' => config('Messages.500'),
            ], 500);
        }
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
        if ($request->user()->cannot('Update Services', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $request->validate([
            'service_name' => 'required',
            'service_cost' => 'required|numeric',
            'service_description' => 'max:100',
            'type_of_currency' => 'required',
            'service_type' => 'required',
            'service_priolity' => 'required',
            'service_event' => 'required',
            'gfs_code' => 'required',
            'self_booking' => 'required',
            'relateToSpaceSize' => 'required'
        ]);


        try {
            additional_services::find($id)->update([
                'userID' => Auth::user()->user_uuid,
                'serviceName' => $request->service_name,
                'serviceCost' => $request->service_cost,
                'serviceDescription' => $request->service_description,
                'type_of_currency' => $request->type_of_currency,
                'service_priolity' => $request->service_priolity,
                'service_type' => $request->service_type,
                'service_event' => $request->service_event,
                'gfs_code' => $request->gfs_code,
                'is_printable' => $request->is_printable,
                'self_booking' => $request->self_booking,
                'relateToSpaceSize' => $request->relateToSpaceSize
            ]);

            if (!PriceConfiguration::where('eventId', session('liveEvent'))->where('referenceId', $id)->exists()) {
                PriceConfiguration::create([
                    'trackingId' => Str::uuid(),
                    'referenceId' => $id,
                    'configurationName' => $request->service_name,
                    'configDescription' => $request->serviceDescription,
                    'configCost' => $request->service_cost,
                    'currency'=>$request->type_of_currency,
                    'eventId' => session('liveEvent')
                ]);
            }
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail', config('Messages.500'));
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
        if ($request->user()->cannot('Delete Services', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //

        try {
            additional_services::find($id)->delete();
            return back()->with('success', 'Service deleted Successfully!!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail', 'Something went wrong, please try again in some minutes');
        }
    }
}
