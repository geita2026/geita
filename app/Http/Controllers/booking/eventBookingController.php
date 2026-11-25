<?php

namespace App\Http\Controllers\booking;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_pavilions;
use Illuminate\Http\Request;
use App\Models\settings\events;
use App\Models\space\space_categories;
use App\Models\space\booths;
use App\Models\space\pavilions;
use App\Traits\System;
use Spatie\Permission\Models\Role;

class eventBookingController extends Controller
{
    use System;
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
        if ($request->user()->cannot('BOOK_SPACE', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('BOOK_SPACE'));
        }

        return redirect()->route('booking.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('BOOK_SPACE', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('BOOK_SPACE'));
        }

        $eventId = session('liveEvent');
        $category = space_categories::where('category_event', $eventId)->findOrFail($id);
        $event = events::find($eventId);
        $bookingOpen = $this->checkApplicationStatus();
        $pavilionCategories = space_categories::where('category_event', $eventId)
            ->where('category_id', '!=', $id)->orderBy('category_name')
            ->get(['category_id', 'category_name']);

        $spaces = pavilions::where('pavilionCategory', $id)
            ->where('space_status', '!=', 'SUNSPENDED')
            ->orderBy('pavilionName')->orderBy('pavilionID')
            ->get(['pavilionID', 'pavilionName', 'pavilion_size', 'pavilionDescription', 'space_status']);

        $heldSpaceIds = booked_pavilions::where('exhibition_year', $eventId)
            ->select('pavilionID')->distinct();

        $heldPavilions = (clone $heldSpaceIds)->whereIn('pavilionID', $spaces->pluck('pavilionID'))
            ->pluck('pavilionID')->flip();

        $boothCounts = booths::whereIn('booths.pavilionID', $spaces->pluck('pavilionID'))
            ->where('booths.bookingStatus', '!=', 'SUNSPENDED')
            ->leftJoinSub($heldSpaceIds, 'held_spaces', 'booths.boothID', '=', 'held_spaces.pavilionID')
            ->groupBy('booths.pavilionID')
            ->selectRaw('booths.pavilionID, COUNT(*) AS total, SUM(CASE WHEN held_spaces.pavilionID IS NULL AND booths.bookingStatus != ? THEN 1 ELSE 0 END) AS available', ['RESERVED'])
            ->get()->keyBy('pavilionID');

        return view('booking.space_list', compact(
            'category', 'event', 'bookingOpen', 'spaces', 'pavilionCategories', 'heldPavilions', 'boothCounts'
        ));
    }
}
