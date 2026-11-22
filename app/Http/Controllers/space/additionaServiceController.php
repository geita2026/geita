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
        if ($request->user()->cannot('VIEW_ADDITIONAL_SERVICE_SUBMENU', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_ADDITIONAL_SERVICE_SUBMENU'));
        }
        $events = events::orderBy('event_start_date', 'DESC')->where('event_status', 1)->get();
        $services = additional_services::leftJoin('events', 'additional_services.service_event', '=', 'events.event_id')->get();
        $income = Income::orderBy('gfs_code')->get();
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
        if ($request->user()->cannot('CREATE_ADDITIONAL_SERVICE', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('CREATE_ADDITIONAL_SERVICE'));
        }
        $validator = Validator::make($request->all(), [
            'service_name' => 'required|string|max:255|unique:additional_services,serviceName',
            'service_cost' => 'required|numeric',
            'service_description' => 'nullable|string|max:100',
            'type_of_currency' => 'required',
            'service_type' => 'required',
            'service_priolity' => 'required',
            'service_event' => 'required|exists:events,event_id',
            'gfs_code' => 'required|exists:incomes,gfs_code',
            'is_printable' => 'nullable|boolean',
            'self_booking' => 'required|boolean',
            'relateToSpaceSize' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
            }

            return back()->withErrors($validator, 'newService')->withInput();
        }

        try {
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
        } catch (\Throwable $th) {
            report($th);
            if ($request->expectsJson()) {
                return response()->json(['status' => 2, 'fail' => config('Messages.500')], 500);
            }

            return back()->with('fail', config('Messages.500'))->withInput();
        }

        if ($request->expectsJson()) {
            return response()->json(['status' => 1, 'success' => 'Service registered successfully.']);
        }

        return redirect()->route('additional_services.index')->with('success', 'Service registered successfully.');
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
        if ($request->user()->cannot('UPDATE_ADDITIONAL_SERVICE', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('UPDATE_ADDITIONAL_SERVICE'));
        }
        $request->validate([
            'service_name' => 'required',
            'service_cost' => 'required|numeric',
            'service_description' => 'max:100',
            'type_of_currency' => 'required',
            'service_type' => 'required',
            'service_priolity' => 'required',
            'service_event' => 'required',
            'gfs_code' => 'required|exists:incomes,gfs_code',
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
        if ($request->user()->cannot('DELETE_ADDITIONAL_SERVICE', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('DELETE_ADDITIONAL_SERVICE'));
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
