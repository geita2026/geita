<?php

namespace App\Http\Controllers\Vistors;

use App\Http\Controllers\Controller;
use App\Models\ExhibitionBadges\CompanyMember;
use App\Models\Stickers\CarSticker;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class GroundVisitorsCountController extends Controller
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
        if ($request->user()->cannot('View Visitors', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }

        $cars=CarSticker::where('event_id',session('liveEvent'))->count();

        $badges=CompanyMember::where('event',session('liveEvent'))->count();
        return view('Vistors.GroundVisitors', compact('cars', 'badges'));
    }
}
