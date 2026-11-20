<?php

namespace App\Traits;

use App\Models\companies;
use App\Models\Notifications\BulkSms;
use App\Models\Tickets\ProducedTicket;
use App\Models\Tickets\VipTicketBookings;
use Illuminate\Support\Str;

trait TsrnSupplier
{

    public function getTsrn()
    {
        $TSRN = 'T-' . substr(date('Y'), -3) . '-' . random_int(100, 999) . '-' . random_int(100, 999);
        $exist = companies::where('registrationID', $TSRN)->first();
        if (!$exist) {
            return $TSRN;
        }
    }

    public function generateTicketNo()
    {
        do {
            $ticketNumber = strtolower(Str::random(5)); // Generates a 5-character random string
        } while (ProducedTicket::where('ticketNo', $ticketNumber)->exists());
        return $ticketNumber;
    }

    public function sendTicketMsg($customerId)
    {
        $ticket = VipTicketBookings::find($customerId);
        $ticketNumber = ProducedTicket::where('bookingId', $customerId)
            ->get(['ticketNo'])
            ->pluck('ticketNo')
            ->toArray();

        $message = '*** ISSUED TICKET(s) ***
Amount: ' . number_format($ticket->amount_paid, 2) . ' ' . $ticket->currency . '
Ticket No: ' . implode(' , ', $ticketNumber) . '
Download your Ticket: ' . rtrim(config('app.url'), '/') . '/myTicket/' . $ticket->ticket_booking_id . '
Do not share this link with anybody!';

        BulkSms::create([
            'sms_id' => Str::uuid(),
            'customer_name' => $ticket->customer_name,
            'phone_number' => $ticket->phone_number,
            'sms_body' => $message,
            'sms_sent_on' => date('Y-m-d'),
        ]);
    }
}
