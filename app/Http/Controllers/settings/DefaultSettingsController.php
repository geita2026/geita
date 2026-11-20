<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\APIs\ValidationApiController;
use App\Http\Controllers\Controller;
use App\Models\setting\DefaultSettings;
use App\Models\settings\events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DefaultSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display system default settings.
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('View Default Settings', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this action.');
        }

        $item   = DefaultSettings::limit(1)->first();
        $events = events::orderByDesc('event_start_date')->get();

        $currentAdminPin = ValidationApiController::adminPin();

        return view('settings.defaultSettings', compact('item', 'events', 'currentAdminPin'));
    }

    /**
     * Update or create default system settings.
     */
    public function update(Request $request)
    {
        if ($request->user()->cannot('Create Default Settings', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this action.');
        }

        
        $validator=Validator::make($request->all(),[
            'notification_channel'=>'required',
            'system_title'=>'required|string|max:150',
            'system_version'=>'required|string|max:20',
            'invoice_deadline_date'=>'required',
            'active_event'=>'required|uuid|exists:events,event_id',
        ]);
        if($validator->fails()){
            return back()->with('fail',$validator->errors()->first());
        }
        DB::beginTransaction();

        try {
            /**
             * 1. ENSURE SINGLE ACTIVE EVENT
             */
            events::where('event_status', 1)
                ->where('event_id', '!=', $request->active_event)
                ->update(['event_status' => 0]);

            events::where('event_id', $request->active_event)
                ->update(['event_status' => 1]);

            /**
             * 2. UPSERT SYSTEM SETTINGS
             */
            DefaultSettings::updateOrCreate(
                ['default_setting_id' => $request->default_setting_id ?? null],
                [
                    'default_setting_id'    => $request->default_setting_id ?? Str::uuid(),
                    'setting_created_by'    => Auth::user()->user_uuid,
                    'system_title'          => $request->system_title,
                    'system_version'        => $request->system_version,
                    'invoice_deadline_date' => $request->invoice_deadline_date,
                    'notification_channel'  => $request->notification_channel,
                    'active_event'          => $request->active_event,
                    'system_environment'    => $request->system_environment,
                    'self_printing'         => $request->boolean('self_printing'),
                    'print_notification'    => $request->boolean('print_notification'),
                    'badging_event'         => $request->active_event,
                    'applicationStatus'     => $request->applicationStatus,
                    'exchangeRate'           => $request->exchangeRate,
                    'allowedNetworks'         => $request->allowedNetworks,
                    'badge_live_test'         => $request->boolean('badge_live_test'),
                    'scanner_pin'             => str_pad((string) ((int) $request->input('scanner_pin', '0000')), 4, '0', STR_PAD_LEFT),
                ]
            );
            DB::commit();

            return back()->with('success', 'System settings updated successfully.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('fail', 'Failed to update system settings. '.$th->getMessage());
        }
    }

    
    
}
