<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\settings\events;
use App\Models\Tickets\ProducedTicket;
use App\Models\Tickets\VipTicketBookings;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class VipTicketSuppyController extends Controller
{


    public function store(Request $request){

        if (session('requestCount')>=5) {
            return back()->with('fail','Too may attempts, service is temporary Unavailable');
         }

        $count=session('requestCount')+1;
        session(['requestCount'=>$count]);

        $request->validate([
            'controlNumber'=>'required|exists:vip_ticket_bookings,control_number',
            'phoneNumber'=>'required|exists:vip_ticket_bookings,phone_number',
        ]);
         
        $liveEvent=events::where('event_status',1)->first()->event_id;
        $ticket=VipTicketBookings::where('control_number',$request->controlNumber)
        ->where('phone_number',$request->phoneNumber)->where('bookingEvent',$liveEvent)->first();

        if ($ticket) {

            if ($ticket->amount_paid>=$ticket->ticket_amount) {
                return redirect()->route('myTicket.show',['myTicket'=>$ticket->ticket_booking_id]);
            }  
            else {
                return back()->with('fail','Sorry we are not yet receiving your payments, please wait for a while once we receive your payments we will send to you a ticket');
            } 
        }
        else {
            return back()->with('fail','Your control number and phone number submited does not correlate, you have only '.(5-session('requestCount')).' chances to repeat again!');
        }
        
    }

    public function edit(Request $request,$id){
        // session::forget('requestCount');
        return view('FrontEnd.Public_Pages.VipTickets.FetchMyTicket');
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
            $tickets=VipTicketBookings::where('ticket_booking_id',$id)->first();
        if($tickets)
        {
            $eventId = events::where('event_status', 1)->first()->event_id;
            $ticket=VipTicketBookings::where('ticket_booking_id',$id)->first();
            
            $ticketNumbers = ProducedTicket::leftJoin('vip_ticket_bookings', 'produced_tickets.bookingId', '=', 'vip_ticket_bookings.ticket_booking_id')
          ->leftJoin('events','vip_ticket_bookings.bookingEvent','=','events.event_id')
          ->where('bookingId', $id)->count();

            $tickets = ProducedTicket::leftJoin('vip_ticket_bookings', 'produced_tickets.bookingId', '=', 'vip_ticket_bookings.ticket_booking_id')
          ->leftJoin('events','vip_ticket_bookings.bookingEvent','=','events.event_id')
          ->where('bookingId', $id)
            ->paginate(100,[
                'customerName',
                'ticketName',
                'ticketAmount',
                'ticketNo',
                'control_number',
                'ticketDate',
                'ticketStatus',
                'ticketId',
                'ticket_booking_id',
                'serviceCode',
                'event_name',
                'phone_number'
            ]);
            // ---- Ticket delivery ----
            // OFFICIAL: HTML ticket card (smooth QR + Download PDF/PNG/Print).
            return view('Tickets.TicketCard', compact('tickets'));

            // To REVERT to the dompdf PDF, comment the line above and uncomment
            // the block below:
            // $pdf = PDF::loadView('Tickets.DownloadableVipTicketReceipt',compact('tickets'))->setPaper([0,0,298,457]);
            // $pdf->getDomPDF('A7')->setHttpContext(stream_context_create([
            //     'ssl' => ['allow_self_signed'=>true,'verify_peer'=>false,'verify_peer_name'=>false],
            // ]));
            // return $pdf->stream(date('d_m_Y').'_sabasaba_ticket'.'.pdf');
            // ---- end ticket delivery ----
        }
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500'));
        }
    }

    /**
     * HTML ticket card (browser-rendered) with a smooth qr-code-styling QR and
     * client-side Download PNG / Download PDF / Print. Additive — the PDF route
     * show() above is untouched and stays as the fallback.
     */
    public function ticketCard($id)
    {
        try {
            $exists = VipTicketBookings::where('ticket_booking_id', $id)->first();
            if (!$exists) {
                return back()->with('fail', 'Ticket not found');
            }
            $tickets = ProducedTicket::leftJoin('vip_ticket_bookings', 'produced_tickets.bookingId', '=', 'vip_ticket_bookings.ticket_booking_id')
                ->leftJoin('events', 'vip_ticket_bookings.bookingEvent', '=', 'events.event_id')
                ->where('bookingId', $id)
                ->get([
                    'customerName', 'ticketName', 'ticketAmount', 'ticketNo', 'control_number',
                    'ticketDate', 'ticketStatus', 'ticketId', 'ticket_booking_id', 'serviceCode',
                    'event_name', 'phone_number',
                ]);
            return view('Tickets.TicketCard', compact('tickets'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }




    public function resendTicket($reference){
          
    }


}
