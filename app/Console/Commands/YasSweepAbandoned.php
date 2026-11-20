<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\Collections\YasTransaction;
use App\Models\Tickets\VipTicketBookings;
use App\Models\Tickets\OnlineTicket;

/**
 * Sweep abandoned online YAS bookings — the ones where the customer never
 * approved the USSD prompt (cancelled / session ended / ignored), so no
 * callback ever settled them. Driven by yas_transactions.created_at (the only
 * table here with a reliable timestamp).
 *
 * Safety: a booking is removed ONLY if it is still unpaid (amount_paid = 0) and
 * tagged MIXX-YAS. Paid bookings, control-number bookings, and POS transactions
 * (booking_id null) are never touched. The yas_transactions rows are kept as
 * the audit log.
 *
 * The default 15-minute cutoff is far longer than a YAS USSD prompt lives, so a
 * genuine (late) approval cannot be swept out from under a paying customer.
 */
class YasSweepAbandoned extends Command
{
    protected $signature = 'yas:sweep {--minutes=15 : Remove unpaid MIXX-YAS bookings whose attempt is older than this}';

    protected $description = 'Delete abandoned (unpaid) MIXX-YAS online bookings that never completed payment.';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $cutoff  = Carbon::now()->subMinutes($minutes);

        $stale = YasTransaction::whereIn('status', [
                YasTransaction::STATUS_PENDING,
                YasTransaction::STATUS_PUSHED,
                YasTransaction::STATUS_FAILED,
            ])
            ->whereNotNull('booking_id')
            ->where('created_at', '<', $cutoff)
            ->get();

        $removed = 0;
        foreach ($stale as $txn) {
            // Guard: only an UNPAID MIXX-YAS booking is ever deleted.
            $booking = VipTicketBookings::where('ticket_booking_id', $txn->booking_id)
                ->where('amount_paid', 0)
                ->where('serviceCode', 'MIXX-YAS')
                ->first();
            if ($booking) {
                OnlineTicket::where('requestNo', $booking->requestId)->delete();
                $booking->delete();
                $removed++;
            }
        }

        $this->info("yas:sweep — removed {$removed} abandoned unpaid MIXX-YAS booking(s) older than {$minutes} min.");
        return self::SUCCESS;
    }
}
