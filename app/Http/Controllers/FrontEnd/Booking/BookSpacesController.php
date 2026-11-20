<?php

namespace App\Http\Controllers\FrontEnd\Booking;

use App\Http\Controllers\Controller;
use App\Models\off_fair\off_fair_spaces;
use App\Models\off_fair\spaces\off_fair_space_categories;
use App\Models\space\space_categories;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

class BookSpacesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $spaceCategories = off_fair_space_categories::get();
        return view('FrontEnd.Public_Pages.OnlineBookingFrontEnd', compact('spaceCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $id = $request->spaceCategory;

        $rangeStart = Carbon::parse($request->start_date);
        $rangeEnd = Carbon::parse($request->end_date);
        session(['start_date'=>$request->start_date]);
        session(['end_date'=>$request->end_date]);
          $bookedSpace = off_fair_spaces::leftJoin('off_fair_space_bookings', 'off_fair_spaces.space_id', '=', 'off_fair_space_bookings.off_fair_space_id')
            ->where('space_category', $id)
            ->where(function ($query) use ($rangeStart, $rangeEnd) {
                $query->whereBetween('start_date', [$rangeStart, $rangeEnd])
                    ->orWhereBetween('end_date', [$rangeStart, $rangeEnd])
                    ->orWhere(function ($q) use ($rangeStart, $rangeEnd) {
                        $q->where('start_date', '<=', $rangeStart)
                            ->where('end_date', '>=', $rangeEnd);
                    });
            })->get();

            $spaces=off_fair_spaces::where('space_category', $id)->get();

        $categories = off_fair_space_categories::where('space_category_id', '!=', $id)->get();
        $category = off_fair_space_categories::find($id);
        return view('FrontEnd.Public_Pages.AvailableSpaces', compact('spaces', 'category', 'categories','bookedSpace'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $spaces = off_fair_spaces::where('space_category', $id)->get();
            $categories = off_fair_space_categories::where('space_category_id', '!=', $id)->get();
            $category = off_fair_space_categories::find($id);
            $bookedSpace=[];
            return view('FrontEnd.Public_Pages.AvailableSpaces', compact('spaces', 'category', 'categories','bookedSpace'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->user()->cannot('Book Space', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }
        //
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
        if ($request->user()->cannot('Book Space', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('Book Space', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }
    }
}
