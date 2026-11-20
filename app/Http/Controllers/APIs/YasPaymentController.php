<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Traits\APITraits;
use App\Traits\TsrnSupplier;
use App\Traits\YasMandateTrait;
use App\Models\Collections\YasTransaction;
use App\Models\Tickets\OnlineTicket;
use App\Models\Tickets\ProducedTicket;
use App\Models\Tickets\VipTicketBookings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * YAS / Tigo Pesa "Debit Mandate" (USSD PUSH) payment flow.
 *
 *   initiatePush()  we ask YAS to debit the customer's wallet (USSD prompt).
 *   callback()      YAS tells us the outcome; on success we issue the tickets.
 *
 * Every step is persisted to the yas_transactions ledger for clean auditing.
 */
class YasPaymentController extends Controller
{
    use APITraits;        // AuthenticateRequest()
    use TsrnSupplier;     // generateTicketNo(), sendTicketMsg()
    use YasMandateTrait;  // getYasToken(), yasPushBillPay(), makeYasReference()

    /**
     * STEP 1 - Initiate a USSD push for a ticket booking.
     * Body: token, spCode, bookingId, phoneNumber
     */
    public function initiatePush(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token'       => 'required',
                'spCode'      => 'required',
                'bookingId'   => 'required',
                'phoneNumber' => 'required|digits:12|starts_with:255',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }

            if ($this->AuthenticateRequest($request->spCode, $request->token) == 0) {
                return response()->json(['status' => 0, 'message' => config('global.102')], 401);
            }

            $booking = VipTicketBookings::find($request->bookingId);
            if (! $booking) {
                return response()->json(['status' => 0, 'message' => 'BOOKING DOES NOT EXIST']);
            }

            $amount = (float) $booking->ticket_amount;
            if ($amount <= 0) {
                return response()->json(['status' => 0, 'message' => 'Invalid booking amount']);
            }

            $referenceId = $this->makeYasReference();

            // Record the attempt BEFORE calling out, so nothing is lost.
            $txn = YasTransaction::create([
                'reference_id'    => $referenceId,
                'booking_id'      => $booking->ticket_booking_id,
                'customer_msisdn' => $request->phoneNumber,
                'biller_msisdn'   => config('yas.biller_msisdn'),
                'amount'          => $amount,
                'currency'        => $booking->currency ?: 'TZS',
                'remarks'         => $booking->TicketTitle ?: 'DITF ticket payment',
                'status'          => YasTransaction::STATUS_PENDING,
            ]);

            $result = $this->yasPushBillPay($request->phoneNumber, $amount, $referenceId, $txn->remarks);

            $txn->update([
                'status'           => $result['ok'] ? YasTransaction::STATUS_PUSHED : YasTransaction::STATUS_FAILED,
                'request_payload'  => [
                    'CustomerMSISDN' => $request->phoneNumber,
                    'BillerMSISDN'   => config('yas.biller_msisdn'),
                    'Amount'         => $amount,
                    'ReferenceID'    => $referenceId,
                ],
                'response_payload' => $result['body'],
                'response_code'    => $result['code'],
                'error'            => $result['ok'] ? null : ($result['description'] ?? 'Push failed'),
            ]);

            if (! $result['ok']) {
                return response()->json([
                    'status'      => 0,
                    'message'     => $result['description'] ?: 'Failed to send USSD push',
                    'referenceId' => $referenceId,
                ]);
            }

            return response()->json([
                'status'      => 1,
                'message'     => 'USSD push sent. Ask the customer to approve the prompt on their phone.',
                'referenceId' => $referenceId,
                'bookingId'   => $booking->ticket_booking_id,
            ]);
        } catch (\Throwable $th) {
            Log::error('YAS initiatePush exception: ' . $th->getMessage());
            return response()->json(['status' => 0, 'message' => config('global.100')], 500);
        }
    }

    /**
     * STEP 2 - Callback from YAS with the purchase outcome.
     * Spec body: Status, Description, MFSTransactionID, ReferenceID, Amount
     * We respond with the expected ResponseCode / ResponseStatus.
     */
    public function callback(Request $request)
    {
        $referenceId = $request->input('ReferenceID');
        $status      = filter_var($request->input('Status'), FILTER_VALIDATE_BOOLEAN);
        $mfsTxnId    = $request->input('MFSTransactionID');
        $amount      = $request->input('Amount');

        Log::info('YAS callback received', $request->all());

        if (! $referenceId) {
            return $this->callbackResponse(false, null, 'Missing ReferenceID');
        }

        $txn = YasTransaction::where('reference_id', $referenceId)->first();
        if (! $txn) {
            return $this->callbackResponse(false, $referenceId, 'Unknown ReferenceID');
        }

        // Idempotency: if we've already settled this one, ack and stop.
        if ($txn->status === YasTransaction::STATUS_SUCCESS) {
            return $this->callbackResponse(true, $referenceId, 'Callback already processed');
        }

        $txn->callback_payload = $request->all();
        $txn->mfs_transaction_id = $mfsTxnId;

        if (! $status) {
            $txn->status = YasTransaction::STATUS_FAILED;
            $txn->error  = $request->input('Description') ?: 'Payment not completed';
            $txn->save();

            // Remove the unpaid booking this attempt created so failed/cancelled
            // attempts don't litter the bookings list. The yas_transactions log
            // (FAILED) is kept as the audit trail.
            $this->discardUnpaidBooking($txn->booking_id);

            return $this->callbackResponse(true, $referenceId, 'Callback successful'); // we acked; payment simply failed
        }

        // ---- Payment successful: settle the booking & issue tickets ----
        try {
            $booking = VipTicketBookings::find($txn->booking_id);
            if ($booking) {
                $booking->update([
                    'amount_paid'   => $amount ?: $txn->amount,
                    'ticket_status' => 1,
                ]);
                $this->generateTickets($booking->ticket_booking_id);
                $this->sendTicketMsg($booking->ticket_booking_id);
            }

            $txn->status  = YasTransaction::STATUS_SUCCESS;
            $txn->paid_at = Carbon::now();
            $txn->error   = null;
            $txn->save();

            return $this->callbackResponse(true, $referenceId, 'Callback successful');
        } catch (\Throwable $th) {
            // Keep the money record; flag for manual reconciliation.
            Log::error('YAS callback settlement error: ' . $th->getMessage(), ['referenceId' => $referenceId]);
            $txn->error = 'Settlement error: ' . $th->getMessage();
            $txn->save();
            // Still ack to YAS so they don't retry forever; reconcile on our side.
            return $this->callbackResponse(true, $referenceId, 'Callback successful');
        }
    }

    /**
     * Delete an online YAS booking that never got paid, plus its OnlineTicket
     * rows. Guarded: it only ever removes a booking that is STILL unpaid
     * (amount_paid = 0) and tagged MIXX-YAS — a settled/paid booking, a
     * control-number booking, or a POS transaction (booking_id null) is never
     * touched.
     */
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

    /**
     * Issue ProducedTicket rows for a paid booking (mirrors the Vodacom flow).
     */
    private function generateTickets($bookingId): void
    {
        $booking = VipTicketBookings::find($bookingId);
        if (! $booking) {
            return;
        }
        if (ProducedTicket::where('bookingId', $bookingId)->exists()) {
            return; // already issued
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

    /** Response body shape YAS expects back from the callback. */
    private function callbackResponse(bool $ok, ?string $referenceId, string $description)
    {
        return response()->json([
            'ResponseCode'        => $ok ? 'BILLER-30-0000-S' : 'BILLER-30-3030-E',
            'ResponseStatus'      => $ok,
            'ResponseDescription' => $description,
            'ReferenceID'         => $referenceId,
        ]);
    }
}
