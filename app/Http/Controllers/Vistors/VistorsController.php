<?php

namespace App\Http\Controllers\Vistors;

use App\Http\Controllers\Controller;
use App\Models\Vistors\SiteVistor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Stevebauman\Location\Facades\Location;

class VistorsController extends Controller
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
        //



        $limit = 20; // limit by default
        $limit = $request->limit;
        $d1 = date('Y-m-d', strtotime($request->from_date));
        $d2 = date('Y-m-d', strtotime($request->to_date));


        $fromDateWeek = date('Y-m-d h:i:s',strtotime(Carbon::now()->startOfWeek()));
        $toDateWeek =  date('Y-m-d h:i:s',strtotime(Carbon::now()->endOfWeek()));
        $fromDateMonth =  date('Y-m-d h:i:s',strtotime(Carbon::now()->startOfMonth()));
        $toDateMonth =  date('Y-m-d h:i:s',strtotime(Carbon::now()->endOfMonth()));
        $fromDateYear =  date('Y-m-d h:i:s',strtotime(Carbon::now()->subYear()));
        $toDateYear =  date('Y-m-d h:i:s',strtotime(Carbon::now()));
        
        $today=SiteVistor::where('visted_on','LIKE','%'.date('Y-m-d').'%')->count();
        $yesterday=SiteVistor::where('visted_on','LIKE','%'.date('Y-m-d',strtotime(Carbon::yesterday())).'%')->count();
        $thisWeek = SiteVistor::whereBetween('visted_on', [$fromDateWeek, $toDateWeek])->count();
        $thisMonth=SiteVistor::whereBetween('visted_on',[$fromDateMonth,$toDateMonth])->count();
        $thisYear=SiteVistor::whereBetween('visted_on',[$fromDateYear,$toDateYear])->count();
        $totalVisitors = SiteVistor::count();


        if (empty($request->from_date)) {
            $vistors = SiteVistor::orderBy('visted_on', 'DESC')->paginate(config('global.DATA_LIMIT'));
        } else {
            $vistors = SiteVistor::orderBy('visted_on', 'DESC')
                ->whereBetween('visted_on', [$d1 . " 00:00:00", $d2 . " 23:59:59"])->paginate(config('global.DATA_LIMIT'));
        }

        return view('Vistors.vistors', compact('vistors', 'totalVisitors',
        'today','thisWeek','thisMonth','thisYear','yesterday'));
    }
}
