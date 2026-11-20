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
        $spaceCategories = space_categories::leftJoin('events', 'space_categories.category_event', '=', 'events.event_id')
            ->orderBy('category_name', 'ASC')
            ->where('category_id', '!=', $id)
            ->where('event_status', 1)->get();

        $event = events::where('event_status', 1)->first();

        $spaces = pavilions::leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
            ->where('space_status', 'UNRESERVED')
            ->where('category_event', $event->event_id)
            ->where('pavilionCategory', $id)->get();

        $spacesList = pavilions::leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
            ->where('space_status', 'UNRESERVED')
            ->where('category_event', $event->event_id)
            ->where('pavilionCategory', $id)->get(['pavilionID']);

        $booths = booths::leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')
            ->whereIn('pavilionID', $spacesList)->get([
                'pavilionID',
                'category_cost',
                'type_of_currency',
                'boothNo'
            ]);
        
        $category = space_categories::where('category_id', $id)->first();


        $bookedPavilions=pavilions::where('pavilionCategory',$id)
        ->whereIn('pavilionID',booked_pavilions::where('exhibition_year',session('liveEvent'))->get(['pavilionID']))->get(['pavilionID']);
        $pavilionCategories=space_categories::where('category_id','!=',$id)
        ->where('category_event',session('liveEvent'))->get(['category_id','category_name']);

        $bookedBooths=booked_pavilions::leftJoin('booths','booked_pavilions.pavilionID','=','booths.boothID')
        ->leftJoin('pavilions','booths.pavilionID','=','pavilions.pavilionID')
        ->where('pavilions.pavilionCategory',$id)
        ->where('booked_pavilions.exhibition_year',session('liveEvent'))
        ->where('advice_note_id','!=',Null)
        ->get('pavilions.pavilionID');

        return view('FrontEnd.Public_Pages.OnFairBookings.ListOfSpace', compact('spaces', 'spaceCategories', 'category', 'booths','bookedPavilions','bookedBooths'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $pavilion = pavilions::find($id);
        $booths = booths::orderBy('boothNo', 'asc')
            ->leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
            ->leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')
            ->where('booths.bookingStatus', '!=', 'SUNSPENDED')
            // ->where('pavilions.space_status', 'UNRESERVED')
            ->where('booths.pavilionID', $id)
            ->get([
                'booths.boothID',
                'pavilionName',
                'boothNo',
                'category_name',
                'boothSize',
                'booths.bookingStatus',
                'floor_plan',
                'category_name',
                'category_cost',
                'type_of_currency',
                'booth_category_id',
            ]);
        $event = events::where('event_status', 1)->first()->event_id;

        $bookedBooths = booths::where('pavilionID', $id)->whereIn('boothID', booked_pavilions::where('exhibition_year', $event)->get('pavilionID'))->get('boothID');
        $event = events::where('event_status', 1)->first()->event_id;

        return view('FrontEnd.Public_Pages.OnFairBookings.AvailableStand', compact('booths', 'pavilion', 'bookedBooths'));
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
