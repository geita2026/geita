<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Notifications\BulkSms;
use App\Models\Tickets\OnlineTicket;
use App\Models\Tickets\ProducedTicket;
use App\Models\Tickets\VipTicketBookings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\IntergrationTraits;
use App\Traits\NotificationTrait;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class TicketBookingControllers extends Controller
{
    use NotificationTrait;
    use IntergrationTraits;
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
        if ($request->user()->cannot('view-ticket-booking', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        session(['phone_number' => '']);
        session(['date' => date('Y')]);
        // ADDITION: optional Service Code filter (TANTRADE / MPESA / MIXX-YAS ...).
        // Remembered in the session so it survives pagination; when blank it is a
        // no-op and the listing behaves exactly as before.
        session(['serviceCode' => $request->serviceCode ?? '']);
        if ($request->source == 1) {
            session(['phone_number' => $request->phone_number]);
            session(['date' => date('Y-m-d', strtotime($request->collectionDate))]);
            $ticket_order = VipTicketBookings::orderBy('ticket_amount', 'DESC')
                ->where('phone_number', 'LIKE', '%' . session('phone_number') . '%')
                ->where('ticket_booked_on', 'LIKE', '%' . session('date') . '%')
                ->when(session('serviceCode'), fn($q) => $q->where('serviceCode', session('serviceCode')))
                ->paginate(10);
        } else {
            $ticket_order = VipTicketBookings::orderBy('ticket_booked_on', 'DESC')->orderBy('ticket_amount', 'DESC')
                ->where('ticket_booked_on', 'LIKE', '%' . session('date') . '%')
                ->when(session('serviceCode'), fn($q) => $q->where('serviceCode', session('serviceCode')))
                ->paginate(10);
        }

        // ADDITION: distinct service codes to populate the filter dropdown, so
        // any current or future channel appears automatically.
        $serviceCodes = VipTicketBookings::whereNotNull('serviceCode')
            ->where('serviceCode', '!=', '')
            ->distinct()
            ->orderBy('serviceCode')
            ->pluck('serviceCode');


        $totalExpected = ProducedTicket::where('ticketDate', 'LIKE', '%' . session('date') . '%')
            ->sum('ticketAmount');

        $totalCollection = ProducedTicket::where('ticketDate', 'LIKE', '%' . session('date') . '%')
            ->sum('ticketAmount');

        $totalTicket = ProducedTicket::leftJoin('vip_ticket_bookings', 'produced_tickets.bookingId', '=', 'vip_ticket_bookings.ticket_booking_id')
            ->where('bookingEvent', session('liveEvent'))
            ->where('ticket_booked_on', 'LIKE', '%' . session('date') . '%')
            ->where('phone_number', 'LIKE', '%' . session('phone_number') . '%')->count();
        return view('Tickets.VipTickets', compact('ticket_order', 'totalCollection', 'totalTicket', 'totalExpected', 'serviceCodes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('view-ticket-booking', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        try {
            $requests = OnlineTicket::orderBy('requestedAt', 'DESC')
                ->leftJoin('vip_ticket_packages', 'online_tickets.packageId', '=', 'vip_ticket_packages.vip_package_id')
                ->whereNotIn('requestNo', VipTicketBookings::where('bookingEvent', session('liveEvent'))->get(['requestId']))
                ->where('requestStatus', 1)->paginate(25);

            return view('Tickets.UnConfirmedRequests', compact('requests'));
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong!' . $th->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            if ($request->user()->cannot('resend-control-number', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $reference = $request->bookingId;
            $booking = VipTicketBookings::find($reference);
            $booking->update([
                'phone_number' => $request->customerNo,
            ]);
            $this->SentControlNoNotification($reference);
            return back()->with('success', 'Message will be sent in One Minute');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('resend-ticket', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        try {
            $reference = $id;
            $booking = VipTicketBookings::find($reference);
            $booking->update([
                'phone_number' => $request->customerNo,
            ]);
            $ticketNumber = ProducedTicket::where('bookingId', $reference)->pluck('ticketNo')->toArray();
        
            #******* PHONE NUMBER **********#
            $message = '*** ISSUED TICKET(s) ***
Amount: ' . number_format($booking->amount_paid, 2) . ' ' . $booking->currency . '
Ticket No: ' . implode(' , ',$ticketNumber) . '
Download your Ticket: '. rtrim(config('app.url'), '/') . '/myTicket/' . $booking->ticket_booking_id . '
Do not share this link with anybody!';

            BulkSms::create([
                'sms_id' => Str::uuid(),
                'customer_name' => $booking->customer_name,
                'phone_number' => $booking->phone_number,
                'sms_body' => $message,
                'sms_sent_on' => date('Y-m-d'),
            ]);
            #********* END PHONE NUMBER ********#
            return back()->with('success', 'Message will be sent in one Minute');
        } catch (\Throwable $th) {
            return back()->with('fail', 'something went wrong!' . $th->getMessage());
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

        try {
            if ($request->user()->cannot('cancel-booking', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $bookings = VipTicketBookings::where('ticket_booking_id', $id)->first();
            $customer_no = $bookings->sp_customer_no;
            $invoice_no = $bookings->sp_invoice_no;
            $canceledby = Auth::user()->first_name . " " . Auth::user()->last_name;
            $reference = $id;
            $reasons = 'Customer Failed to pay online Ticket on time, as an invoice specified';
            $requestId = $bookings->requestId;
            if (ProducedTicket::where('bookingId', $id)->exists()) {
                return back()->with('fail', 'This control Number has tickets');
            }
            #************ if has control number *********#
            if ($bookings->control_number != null && config('global.APP_STATUS') == 1) {
                $data = array('customer_no' => $customer_no, 'invoice_no' => $invoice_no, 'reference' => $reference, 'cancelledby' => $canceledby, 'reason' => $reasons); // data array
                $status = $this->CancelInvoiceFromSp($data, $id); // if their is control number in invoice, cancel control number first from billing sy
                if ($status == 1) {
                    VipTicketBookings::find($id)->delete();
                    OnlineTicket::where('requestNo', $requestId)->delete();
                    return back()->with('success', 'Invoice canceled successfully!');
                } elseif ($status == 0) {
                    return back()->with('fail', 'Failed to cancel control Number');
                }
                #End with control no
            }
            #********** HASS NO CONTROL NUMBER ***********#

            OnlineTicket::where('requestNo', $requestId)->delete();
            VipTicketBookings::where('ticket_booking_id', $id)->delete();
            #********** END HAS CONTROL NUMBER ***********#
            return back()->with('success', 'Ticket booking canceled successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'ERROR: E_1001' . $th->getMessage());
        }
    }
}
