<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\settings\events;
use App\Models\Stickers\CarSticker;
use App\Models\Tickets\PosTransaction;
use App\Models\Tickets\ProducedTicket;
use App\Models\Tickets\VipTicketBookings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VerifyTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Tickets.VerifyTickets');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Tickets.OpenGate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'ticketNO'=>'required|min:4'
        // ]);
        $exists = VipTicketBookings::where('sp_invoice_no', 'LIKE', '%' . $request->ticketNo . '%')
            ->first();

        if ($exists) {
            $exist1 = ProducedTicket::where('bookingId', $exists->ticket_booking_id)
                ->where('scanCount', 0)->first();
            if ($exist1) {
                ProducedTicket::where('bookingId', $exists->ticket_booking_id)
                    ->update(['scanCount' => 1]);
                return back()->with('success', 'VALID TICKET AND TICKET COST IS: ' . number_format($exists->ticket_amount) . ' TZS');
            } else {
                return back()->with('fail', 'TICKET ALREADY USED ON: ' . date('d-m-Y h:i:s', $exists->ticketUpdateOn));
            }
        } else {
            return back()->with('fail', 'No ticket Found');
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
        session(['gateName' => $request->gateName]);

        $qrcode = str_replace(' ', '', $request->plateNumber);
        $id = $qrcode;
        $status = 0;
        #***************CAR STICKERS**************#
        $eventId = events::where('event_status', 1)->first()->event_id;
        $sesonal = CarSticker::where('plate_number', 'LIKE', '%' . $id . '%')->where('event_id', $eventId)->count();
        $dailyOnline = ProducedTicket::where('plateNumber', 'LIKE', '%' . $id . '%')->count();
        $daily = PosTransaction::where('plateNumber', 'LIKE', '%' . $id . '%')->count();
        $status = 0;
        if ($sesonal > 0 || $daily > 0 || $dailyOnline > 0) {
            $status = 1;
        }
        if (substr($id, 0, 1) == '/') {
            $status = 1;
        }
        if (session('gateName') == 'G1' && $status == 1) {
            Http::get('http://192.168.3.13/open'); // Cobil IN
            // Http::get('http://192.168.3.16/open'); // Cobil OUT
            return back()->with('success', 'Gate ' . session('gateName') . ' Opened');
        }
        if (session('gateName') == 'G2' && $status == 1) {
            Http::get('http://192.168.3.10/open'); // Cobil IN
            // Http::get('http://192.168.3.16/open'); // Cobil OUT
            return back()->with('success', 'Gate ' . session('gateName') . ' Opened');
        }
        if (session('gateName') == 'G3' && $status == 1) {
            // Http::get('http://192.168.3.8/open'); // Cobil IN
            Http::get('http://192.168.3.16/open'); // Cobil OUT
            return back()->with('success', 'Gate ' . session('gateName') . ' Opened');
        } else {
            return back()->with('fail', 'Gate ' . $request->plateNumber . ' Does Not exist!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
