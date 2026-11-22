<?php

namespace App\Http\Controllers\booking;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_pavilions;
use Illuminate\Http\Request;
use App\Models\settings\events;
use App\Models\space\space_categories;
use App\Models\space\pavilions;
use Spatie\Permission\Models\Role;

class eventBookingController extends Controller
{
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
        if ($request->user()->cannot('Book Space', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        // Use the same active event session as the main booking controller.
        $event_id = session('liveEvent');

        if (!$event_id) {
            return back()->with('fail', 'No active exhibition event has been selected.');
        }

        $event = Events::where('event_id', $event_id)->first();

        if (!$event) {
            return back()->with('fail', 'The selected exhibition event could not be found.');
        }

        $event_name = $event->event_name;

        $space_categories = space_categories::where('category_event', $event_id)
            ->orderBy('category_name', 'ASC')
            ->get();

        $spaces = pavilions::get();

        return view(
            'booking.space-category-booking',
            compact('space_categories', 'event_id', 'event_name', 'spaces')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        try {
            if($request->user()->cannot('Book Space',Role::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        $spaces=pavilions::leftJoin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')
        ->where('space_status','UNRESERVED')->orderBy('pavilionName','ASC')->distinct()
        ->where('pavilionCategory',$id)->get([
            'pavilionName','pavilions.pavilionID','pavilion_size','category_name','cost_one_msq','type_of_currency',
        'numberOfPartitions'
        ]);
        
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

        return view('booking.space_list',compact('spaces','bookedPavilions','pavilionCategories','bookedBooths'));
        } catch (\Throwable $th) {
           return back()->with('fail',config('Messages.500').$th->getMessage());
        }

    }

}
