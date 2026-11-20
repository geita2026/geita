<?php

namespace App\Http\Controllers\Vistors;

use App\Http\Controllers\Controller;
use App\Models\ExhibitionBadges\CompanyMember;
use App\Models\Stickers\CarSticker;
use App\Models\Tickets\PosTransaction;
use App\Models\Tickets\ProducedTicket;
use App\Models\Vistors\GroundVisitor;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Traits\VistorsTrait;


class GroundVisitorsCountController extends Controller
{
    use VistorsTrait;
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
        
       $date=date('Y-m-d');
        if ($request->date) {
            $date=date('Y-m-d',strtotime($request->date));
        }
        $cars=CarSticker::where('event_id',session('liveEvent'))->count();

        $adult=PosTransaction::where('ticketAmount','>',1000)->where('ticketDate','LIKE','%'.$date.'%')->count();
        $child=PosTransaction::where('ticketAmount','<',3000)->where('ticketDate','LIKE','%'.$date.'%')->count();
        $badges=CompanyMember::where('event',session('liveEvent'))->count();
        return view('Vistors.GroundVisitors', compact('cars','adult','child','badges'));
    }
}
