<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use App\Models\settings\events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class eventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List events
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('View Events', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this action.');
        }

        $events = events::orderBy('event_start_date', 'DESC')->get([
            'event_id',
            'event_name',
            'event_location',
            'event_start_date',
            'event_end_date',
            'event_status',
            'event_descriptions'
        ]);

        return view('settings.exhibition-events-settings', compact('events'));
    }

    /**
     * Switch active event explicitly
     */
    public function create(Request $request)
    {
        DB::transaction(function () use ($request) {

            events::where('event_status', 1)->update(['event_status' => 0]);

            events::where('event_id', $request->liveEvent)
                ->update(['event_status' => 1]);

            session(['liveEvent' => $request->liveEvent]);
        });

        return back()->with('success', 'Active event changed successfully.');
    }

    /**
     * Store new event
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Create Events', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this action.');
        }

        $makeActive = $request->event_status === 'on';

        DB::transaction(function () use ($request, $makeActive) {

            if ($makeActive) {
                events::where('event_status', 1)->update(['event_status' => 0]);
            }

            events::create([
                'event_id'           => Str::uuid(),
                'event_name'         => $request->event_name,
                'event_location'     => $request->event_location,
                'event_start_date'   => $request->event_start_date,
                'event_end_date'     => $request->event_end_date,
                'event_descriptions' => $request->event_descriptions,
                'event_status'       => $makeActive ? 1 : 0,
            ]);
        });

        $this->ensureSingleActiveEvent();

        return back()->with('success', 'Event created successfully.');
    }

    /**
     * Update event
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('Update Events', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this action.');
        }

        $event      = events::findOrFail($id);
        $makeActive = $request->event_status === 'on';

        // ❗ Prevent deactivating the last active event (NO EXCEPTION)
        if (!$makeActive && $event->event_status == 1) {
            $activeCount = events::where('event_status', 1)->count();

            if ($activeCount <= 1) {
                return back()->with(
                    'fail',
                    'At least one event must remain active. Activate another event first.'
                );
            }
        }

        DB::transaction(function () use ($event, $request, $makeActive) {

            if ($makeActive) {
                events::where('event_status', 1)
                    ->where('event_id', '!=', $event->event_id)
                    ->update(['event_status' => 0]);
            }

            $event->update([
                'event_name'         => $request->event_name,
                'event_location'     => $request->event_location,
                'event_start_date'   => $request->event_start_date,
                'event_end_date'     => $request->event_end_date,
                'event_descriptions' => $request->event_descriptions,
                'event_status'       => $makeActive ? 1 : 0,
            ]);
        });

        $this->ensureSingleActiveEvent();

        return back()->with('success', 'Event updated successfully.');
    }

    /**
     * Delete event
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('Delete Events', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this action.');
        }

        $event = events::findOrFail($id);

        if ($event->event_status == 1) {
            $activeCount = events::where('event_status', 1)->count();

            if ($activeCount <= 1) {
                return back()->with(
                    'fail',
                    'You cannot delete the only active event.'
                );
            }
        }

        try {
            $event->delete();
            return back()->with('success', 'Event deleted successfully.');
        } catch (\Throwable $th) {
            return back()->with(
                'fail',
                'Event cannot be deleted. It may be referenced by other records.'
            );
        }
    }

    /**
     * Final safety net
     */
    private function ensureSingleActiveEvent(): void
    {
        $activeEvents = events::where('event_status', 1)
            ->orderBy('event_start_date', 'DESC')
            ->get();

        if ($activeEvents->count() === 0) {
            events::orderBy('event_start_date', 'DESC')
                ->limit(1)
                ->update(['event_status' => 1]);
        }

        if ($activeEvents->count() > 1) {
            $keep = $activeEvents->first();

            events::where('event_status', 1)
                ->where('event_id', '!=', $keep->event_id)
                ->update(['event_status' => 0]);
        }
    }
}
