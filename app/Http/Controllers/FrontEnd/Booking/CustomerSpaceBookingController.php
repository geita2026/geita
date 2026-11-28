<?php

namespace App\Http\Controllers\FrontEnd\Booking;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_pavilions;
use Illuminate\Http\Request;
use App\Models\settings\events;
use App\Models\space\booths;
use App\Models\space\space_categories;
use App\Models\space\pavilions;

class CustomerSpaceBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $spaceCategories = space_categories::orderBy('category_name', 'ASC')
            ->leftJoin('events', 'space_categories.category_event', '=', 'events.event_id')
            ->where('event_status', 1)->get([
                'category_id',
                'category_name',
                'cost_one_msq',
                'type_of_currency',
                'category_icon',
                'event_name',
                'category_descriptions'
            ]);
        return view('FrontEnd.Public_Pages.OnFairBookings.ExhibitionSpaceCategories', compact('spaceCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $spaces = pavilions::leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')->get()->where('event', $request->event_id);
        return view('FrontEnd.Booking.exhibition_spaces', compact('spaces'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $category = space_categories::query()
            ->join('events', 'space_categories.category_event', '=', 'events.event_id')
            ->where('events.event_status', 1)
            ->where('space_categories.category_id', $id)
            ->firstOrFail(['space_categories.*', 'events.event_name']);

        $spaceCategories = space_categories::where('category_event', $category->category_event)
            ->where('category_id', '!=', $id)
            ->orderBy('category_name')
            ->get(['category_id', 'category_name']);

        $spaces = pavilions::where('pavilionCategory', $id)
            ->where('space_status', 'UNRESERVED')
            ->orderBy('pavilionName')
            ->get(['pavilionID', 'pavilionName', 'pavilionDescription', 'pavilion_size', 'numberOfPartitions']);

        $booths = booths::whereIn('pavilionID', $spaces->pluck('pavilionID'))
            ->where('bookingStatus', '!=', 'SUNSPENDED')
            ->get(['boothID', 'pavilionID', 'bookingStatus']);
        $heldSpaceIds = booked_pavilions::where('exhibition_year', $category->category_event)
            ->whereIn('pavilionID', $spaces->pluck('pavilionID')->merge($booths->pluck('boothID')))
            ->distinct()->pluck('pavilionID')->flip();
        $boothsByPavilion = $booths->groupBy('pavilionID');

        $spaces = $spaces->map(function ($space) use ($boothsByPavilion, $heldSpaceIds) {
            $registeredBooths = $boothsByPavilion->get($space->pavilionID, collect());
            $isHeld = $heldSpaceIds->has($space->pavilionID);
            $available = $isHeld ? 0 : $registeredBooths->filter(fn ($booth) =>
                $booth->bookingStatus !== 'RESERVED' && !$heldSpaceIds->has($booth->boothID))->count();
            $hasBooths = $registeredBooths->isNotEmpty() || $space->numberOfPartitions > 0;

            return [
                'pavilion' => $space,
                'registeredBoothCount' => $registeredBooths->count(),
                'availableBoothCount' => $available,
                'hasBooths' => $hasBooths,
                'canBook' => !$isHeld && (!$hasBooths || $available > 0),
            ];
        })->sortByDesc('canBook')->values();

        return view('FrontEnd.Public_Pages.OnFairBookings.ListOfSpace', compact('spaces', 'spaceCategories', 'category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $pavilion = pavilions::findOrFail($id);
        $mapPavilions = $pavilion->sharedMapPavilions();
        $booths = $mapPavilions->isEmpty() ? $pavilion->mapBooths()->get() : collect();
        $event = events::where('event_status', 1)->first();

        return view('FrontEnd.Public_Pages.OnFairBookings.AvailableStand', compact('booths', 'pavilion', 'event', 'mapPavilions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {}
}
