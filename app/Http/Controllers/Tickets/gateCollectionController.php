<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\PosTransaction;
use App\Models\Tickets\ProducedTicket;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class gateCollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($request->user()->cannot('view-gate-collection', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            session(['moduleNo' => 1]);
            $totalCollections = PosTransaction::where('ticketDate', 'LIKE', '%' . date('Y-m-d') . '%')->sum('ticketAmount');
            $exactlyCollected = PosTransaction::where('ticketStatus', 1)->where('ticketDate', 'LIKE', '%' . date('Y-m-d') . '%')->sum('ticketAmount');
            $overallCollection = PosTransaction::where('eventId', session('liveEvent'))->sum('ticketAmount');
            $overallDemagedTickets = PosTransaction::where('ticketStatus',0)->where('eventId', session('liveEvent'))->where('ticketDate', 'LIKE', '%' . date('Y-m-d') . '%')->sum('ticketAmount');
       

            return view('Tickets.TransactionList', compact('totalCollections', 'exactlyCollected', 'overallCollection','overallDemagedTickets'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500').$th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Manage Collections', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }

        try {
            PosTransaction::create([
                'locationId' => Str::uuid(),
                'locationName' => $request->locationName,
                'locationDescription' => $request->locationDescription,
                'collectionstatus' => $request->collectionstatus,
            ]);
            return back()->with('success', 'location added successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong!' . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        if ($request->user()->cannot('Manage Collections', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }

        try {
            PosTransaction::find($id)->update([
                'ticketDescription' => $request->ticketDescription,
                'ticketStatus' => $request->ticketStatus,
            ]);

            return back()->with('success', 'Ticket Marked as Destroyed!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('Manage Collections', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }
        try {
            PosTransaction::find($id)->delete();
            return back()->with('success', 'location deleted successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'something went wrong!');
        }
    }
}
