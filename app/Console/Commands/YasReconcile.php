<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Traits\YasMandateTrait;
use App\Traits\TsrnSupplier;
use App\Models\Collections\YasTransaction;
use App\Models\Tickets\VipTicketBookings;
use App\Models\Tickets\ProducedTicket;
use App\Models\Tickets\OnlineTicket;

/**
 * Reconcile YAS payments via TQS (Query Transaction Details).
 *
 * YAS's success callback is unreliable in production, so we POLL YAS instead:
 * for every still-PUSHED transaction we ask TQS for the real status
 * (EXTERNALREFID = our push ReferenceID) and act on it:
 *   - Posted    → payment confirmed → settle the booking, issue the ticket(s),
 *                 send the SMS, mark the transaction SUCCESS.
 *   - Declined  → mark FAILED and remove the unpaid booking.
 *   - Submitted → still pending, leave it for the next run.
 *
 * This is the backstop that guarantees paying customers get their ticket even
 * when no success callback arrives. Run it once to clear a backlog, and on a
 * 1-minute schedule for ongoing sales.
 */
class YasReconcile extends Command
{
    use YasMandateTrait;   // yasQueryStatus()
    use TsrnSupplier;      // generateTicketNo(), sendTicketMsg()

    protected $signature = 'yas:reconcile {--days=3 : Only look back this many days} {--limit=300 : Max transactions per run}';

    protected $description = 'Confirm YAS payments via TQS and issue tickets for the ones actually paid (backstop for missing success callbacks).';

    public function handle(): int
    {
        $since  = Carbon::now()->subDays((int) $this->option('days'));
        $before = Carbon::now()->subSeconds(30); // give the callback a brief head start

        $pending = YasTransaction::where('status', YasTransaction::STATUS_PUSHED)
            ->where('created_at', '>=', $since)
            ->where('created_at', '<=', $before)
            ->orderBy('created_at')
            ->limit((int) $this->option('limit'))
            ->get();

        $posted = 0; $declined = 0; $stillPending = 0;

        foreach ($pending as $txn) {
            $res = $this->yasQueryStatus($txn->reference_id, $txn->customer_msisdn);
            $status = strtolower((string) $res['status']);

            if ($status === 'posted') {
                $this->settle($txn, $res['txnid']);
                $posted++;
            } elseif ($status === 'declined') {
                $txn->update([
                    'status'        => YasTransaction::STATUS_FAILED,
                    'error'         => 'TQS: Declined',
                    'response_code' => $res['resultCode'],
                ]);
                $this->discardUnpaidBooking($txn->booking_id);
                $declined++;
            } else {
                // Submitted / unknown / not-found — still pending, leave it.
                $stillPending++;
            }
        }

        $this->info("yas:reconcile — checked {$pending->count()}: {$posted} settled, {$declined} declined, {$stillPending} pending.");
        return self::SUCCESS;
    }

    /** Confirmed paid: settle the booking + issue tickets, then mark SUCCESS. */
    private function settle(YasTransaction $txn, ?string $txnid): void
    {
        if ($txn->booking_id) {
            $booking = VipTicketBookings::where('ticket_booking_id', $txn->booking_id)
                ->where('amount_paid', 0)
                ->first();
            if ($booking) {
                $booking->update(['amount_paid' => $txn->amount, 'ticket_status' => 1]);
                $this->generateTickets($booking->ticket_booking_id);
                $this->sendTicketMsg($booking->ticket_booking_id);
            }
        }
        $txn->update([
            'status'             => YasTransaction::STATUS_SUCCESS,
            'paid_at'            => Carbon::now(),
            'mfs_transaction_id' => $txnid ?: $txn->mfs_transaction_id,
            'error'              => null,
        ]);
    }

    /** Issue ProducedTicket rows for a paid booking (mirrors the callback). */
    private function generateTickets($bookingId): void
    {
        $booking = VipTicketBookings::find($bookingId);
        if (! $booking || ProducedTicket::where('bookingId', $bookingId)->exists()) {
            return;
        }
        $tickets = OnlineTicket::leftJoin('vip_ticket_packages', 'online_tickets.packageId', '=', 'vip_ticket_packages.vip_package_id')
            ->where('requestNo', $booking->requestId)
            ->get();
        foreach ($tickets as $value) {
            for ($i = 1; $i <= $value->quantity; $i++) {
                ProducedTicket::create([
                    'ticketId'     => Str::uuid(),
                    'ticketAmount' => $value->package_cost,
                    'bookingId'    => $booking->ticket_booking_id,
                    'ticketNo'     => $this->generateTicketNo(),
                    'ticketName'   => $value->package_discriptions,
                    'ticketStatus' => 1,
                    'customerName' => $booking->customer_name,
                    'packageId'    => $value->packageId,
                    'visitDate'    => $value->visitDate,
                    'plateNumber'  => $value->plateNumber,
                ]);
            }
        }
    }

    /** Remove an unpaid MIXX-YAS booking (guarded — never touches a paid one). */
    private function discardUnpaidBooking($bookingId): void
    {
        if (! $bookingId) {
            return;
        }
        $booking = VipTicketBookings::where('ticket_booking_id', $bookingId)
            ->where('amount_paid', 0)
            ->where('serviceCode', 'MIXX-YAS')
            ->first();
        if ($booking) {
            OnlineTicket::where('requestNo', $booking->requestId)->delete();
            $booking->delete();
        }
    }
}
