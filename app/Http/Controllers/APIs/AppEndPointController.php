<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\settings\events;
use App\Models\settings\EventSigningKey;
use App\Models\Tickets\PosAgent;
use App\Models\Tickets\PosTransaction;
use App\Models\Tickets\ProducedTicket;
use App\Models\Tickets\VipTicketBookings;
use App\Models\Tickets\VipTicketPackage;
use App\Models\Collections\YasTransaction;
use App\Traits\YasMandateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AppEndPointController extends Controller
{
    use YasMandateTrait; // getYasToken(), yasPushBillPay(), makeYasReference()
    #--------------- LOGIN ----------#
    public function AppLogin(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            // if (!Auth::attempt(['email' => $request->username, 'password' => $request->password])) {
            //     return response()->json([
            //         'status' => 0, 'message' => 'Incorect username or password'
            //     ], 200);
            // }

            // return md5($request->password);

            if (!PosAgent::where('userName', $request->username)
                ->where('agentPassword', md5($request->password))->where('agentStatus', 1)->exists()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Incorect username or password'
                ], 200);
            }

            $token = Str::random(80);
            $agent = PosAgent::where('userName', $request->username)
                ->where('agentPassword', md5($request->password))->first();
            if ($agent->userName == 'G1' || $agent->userName == 'G2' || $agent->userName == 'G3') {
                if ($agent->agentToken == Null) {
                    $agent->update([
                        'agentToken' => $token,
                    ]);
                }
            } else {
                // Only mint a token when the agent doesn't already have one.
                // Regenerating it on every login invalidates any other device
                // (or the same device's earlier session) already signed in on
                // this account — that device's next background call then gets a
                // 401 and the app auto-logs-out. Keeping the token stable lets
                // the same account stay valid across devices/re-logins. Login
                // timestamps are still recorded as before.
                $update = [];
                if ($agent->agentToken == Null) {
                    $update['agentToken'] = $token;
                }
                if ($agent->firstLogin == null) {
                    $update['firstLogin'] = date('Y-m-d h:i:s');
                } else {
                    $update['lastLogin'] = date('Y-m-d h:i:s');
                }
                $agent->update($update);
            }


            $agentData = PosAgent::leftJoin('pos_lists', 'pos_agents.asignedPos', '=', 'pos_lists.posId')
                ->leftJoin('point_of_sales', 'pos_agents.asignedLocation', '=', 'point_of_sales.locationId')
                ->leftJoin('events', 'pos_agents.asignedEvent', '=', 'events.event_id')
                ->leftJoin('companies', 'pos_agents.adminstrativeCompany', '=', 'companies.companyID')
                ->where('agentId', $agent->agentId)
                ->get([
                    'agentId',
                    'agentName',
                    'locationName',
                    'posName',
                    'agentToken',
                    'event_name',
                    'event_start_date',
                    'event_end_date',
                    'event_location',
                    'companyName as adminCompany',
                ]);

            $ticketSecret = date('dm');
            $signing = $this->activeEventSigningKey();

            // Audit: which agent logged in on which device.
            \App\Models\setting\DeviceAccessLog::record(
                $request,
                'login',
                true,
                $agent->agentId,
                $agent->agentName,
                optional($agentData->first())->posName
            );

            return response()->json([
                'status' => 1,
                'user' => $agentData,
                'ticketSecret' => $ticketSecret,
                // NEW (offline LAN validation): per-event HMAC key, shared by
                // every POS + scanner for this event. Older app builds simply
                // ignore these extra fields.
                'signingKey' => $signing['signingKey'],
                'eventId' => $signing['eventId'],
                'message' => 'Login Successfully!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'ERROR OCCURED!' . $th->getMessage(),
            ]);
        }
    }
    #--------------- END LOGIN ----------#

    /**
     * Get (or lazily create) the HMAC signing key for the active event.
     * Same key for every POS + scanner, so a POS-signed ticket verifies
     * on any device with no internet. Returns nulls if no active event,
     * in which case clients fall back to the existing online-only flow.
     */
    private function activeEventSigningKey(): array
    {
        // Never let signing-key issuance break login. If the table is missing
        // (code deployed before migrate) or anything else fails, fall back to
        // a null key — clients then use the legacy QR / online-only flow.
        try {
            $event = events::where('event_status', 1)->first();
            if (!$event) {
                return ['eventId' => null, 'signingKey' => null];
            }
            $record = EventSigningKey::firstOrCreate(
                ['event_id' => $event->event_id],
                ['signing_key' => bin2hex(random_bytes(32))]
            );
            return ['eventId' => $event->event_id, 'signingKey' => $record->signing_key];
        } catch (\Throwable $e) {
            return ['eventId' => null, 'signingKey' => null];
        }
    }

    #------ Resert Password----#
    public function resertPassword(Request $request)
    {
        try {
            $agent = PosAgent::where('agentToken', $request->agentToken)
                ->where('agentPassword', md5($request->oldPassword))->first();
            if (!$agent) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Authentication Error!'
                ]);
            }
            $token = Str::random(80);
            $agent->update([
                'agentPassword' => md5($request->password),
                'agentToken' => $token,
            ]);
            return response()->json([
                'status' => 1,
                'agentToken' => $token,
                'message' => 'Password changes successfully!'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('Messages.500')
            ]);
        }
    }


    #************** GET VERIFY ADMNISTRATOR PASSWORD  ************#
    public function verifyAdminPassword(Request $request)
    {
        if ($request->adminPassword == date('dmY')) {
            return response()->json([
                'status' => 0,
                'message' => 'success',
            ]);
        }
    }
    #************** GET VERIFY ADMNISTRATOR PASSWORD  ************#


    /**
     * Packages that are on sale right now.
     *
     * Adult entry has two DB rows sharing one gfs_code:
     *   - AET  "Adult Normal Day"    @ 3,000  (everyday)
     *   - SAET "Adult Sabasaba Day"  @ 4,000  (7 July only)
     *
     * Everyday rule: the 4,000 "premium" tier — SAET and any other 4,000 row —
     * is hidden from the gate/public listing.
     *
     * Sabasaba (7 July, DITF): adult entry must sell at 4,000, so on that day
     * only we show SAET and hide the everyday AET. Every other package is
     * untouched, so the rest of the 4,000 tier stays hidden.
     *
     * Fail-safe: AET is only swapped out when an active SAET row actually
     * exists. If SAET were ever disabled/missing, the gate keeps selling the
     * 3,000 AET rather than being left with no adult ticket at all.
     *
     * The date uses the app timezone (Africa/Dar_es_Salaam), so "7 July" is the
     * local calendar day, not UTC.
     *
     * Priced server-side on purpose: postTickets / createConsumerTransaction
     * reject any upload whose ticketAmount != package_cost, so whatever we serve
     * here is exactly what the POS is allowed to charge and bank.
     */
    private function visibleServices()
    {
        $isSabasaba = ((int) date('n') === 7 && (int) date('j') === 7);

        $all = VipTicketPackage::where('packageStatus', 1)
            ->orderBy('package_cost', 'ASC')
            ->get([
                'vip_package_id',
                'package_name',
                'package_discriptions',
                'package_cost',
                'currency',
                'pStatus',
            ]);

        $hasSaet = $all->contains(
            fn ($p) => strtoupper(trim($p->package_name)) === 'SAET'
        );
        $swap = $isSabasaba && $hasSaet;

        return $all->filter(function ($p) use ($swap) {
            $name = strtoupper(trim($p->package_name));
            if ($name === 'SAET') {
                return $swap; // Sabasaba adult (4,000): shown only on 7 July
            }
            if ($name === 'AET') {
                return !$swap; // everyday adult (3,000): hidden on 7 July
            }
            // Everyone else: hide the premium 4,000 tier, as before.
            return (string) $p->package_cost !== '4000';
        })->values();
    }

    #************** GET SERVICES ************#
    public function GetServices(Request $request)
    {
        try {
            // return $request;
            $token = $request->token;
            $status = $this->checkTokenValidity($token);
            if ($status == 0) {
                return response()->json([
                    'status' => 100,
                    'message' => 'Authentication Error!',
                ]);
            }
            $userName = PosAgent::where('agentToken', $token)->first();
            if ($userName->userName == 'G1' || $userName->userName == 'G2' || $userName->userName == 'G3') {
                $services = [];
            } elseif ($userName->agentStatus == 0) {
                $services = [];
            } elseif ($userName->agentStatus == 1) {
                $services = $this->visibleServices();
            }

            return response()->json([
                'status' => 1,
                'services' => $services,
                'message' => 'Data available'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'ERROR OCCURED!',
            ]);
        }
    }

    #************** END GET SERVICES ************#

    #*********** GET PUBLIC SERVICES (no auth) ***********#
    public function GetPublicServices()
    {
        try {
            $services = $this->visibleServices();

            return response()->json([
                'status'   => 1,
                'services' => $services,
                'message'  => 'Data available',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 0,
                'message' => 'ERROR OCCURED!',
            ]);
        }
    }
    #************** END GET PUBLIC SERVICES ************#

    #*********** POST TICKETS **********#
    public function postTickets(Request $request)
    {
        try {
            // if ($this->checkTokenValidity($request['token']) == 0) {
            //     return response()->json([
            //         'status' => 100,
            //         'message' => 'Authentication Error'
            //     ]);
            // }
            $service = VipTicketPackage::where('vip_package_id', $request['serviceId'])->first();
            if ($service->package_cost != $request['ticketAmount']) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Service cost and ticket Amount mismatch!',
                ]);
            }
            $transaction = PosTransaction::where('ticketNO', $request['ticketId'])
                ->oRwhere('ticketId', $request['ticketId'])->first();
            if ($transaction) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Ticket already exists.',
                ]);
            }
            $agent = PosAgent::find($request['printedBy']);
            $eventId = events::where('event_status', 1)->first()->event_id;
            PosTransaction::create([
                'ticketId' => $request['ticketId'],
                'ticketAmount' => $request['ticketAmount'],
                'serviceId' => $request['serviceId'],
                'printedBy' => $request['printedBy'],
                'locationId' => $agent->asignedLocation,
                'posUsed' => $agent->asignedPos,
                'eventId' => $eventId,
                'ticketNo' => $request['ticketNo'],
                'ticketStatus' => 1,
                'scanStatus' => 0,
                'plateNumber' => str_replace(' ', '', $request['plateNumber']),
                // Payment channel: CASH by default, MIXX-YAS when paid via the
                // YAS USSD push. Sent by the eTAC POS upload.
                'paymentChannel' => $request['paymentChannel'] ?? 'CASH',
            ]);
            return response()->json([
                'status' => 1,
                'message' => config('Messages.200'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('Messages.500'),
            ]);
        }
    }

    #**************  ************#
    public function checkTokenValidity($token)
    {
        // ── EVENT KILL-SWITCH (SABASABA 2026) ────────────────────────────────
        // The app auto-logs-out whenever an endpoint returns status 100, which
        // only happens here on a token miss. During the live event that was
        // kicking cashiers off mid-shift (stale tokens after multi-device
        // logins). Force-valid so no endpoint ever returns 100 and nobody is
        // kicked out. postTickets still identifies the agent by printedBy, and
        // GetServices falls back to cached services — so selling continues.
        // REVERT AFTER THE EVENT by restoring the token check below.
        return 1;

        // Original check (restore to re-enable token enforcement):
        // $exist = PosAgent::where('agentToken', $token)->first();
        // if ($exist) {
        //     return 1;
        // }
        // return 0;
    }

    #*********** YAS USSD PUSH FROM POS (pay-then-print) **********#
    /**
     * Initiate a YAS / Tigo Pesa USSD push for a POS sale. The POS calls this,
     * then polls yasPosStatus() and only prints once the status is SUCCESS.
     * Body: token, phoneNumber (255XXXXXXXXX), amount.
     */
    public function yasPosPush(Request $request)
    {
        try {
            // Token-free by design: the POS logs in against the LAN ticketing
            // server, so its agent token is not valid on prod. This mirrors the
            // validate-settings-pin endpoint, which also runs without a token.
            $validator = Validator::make($request->all(), [
                'phoneNumber' => 'required|digits:12|starts_with:255',
                'amount'      => 'required|numeric|min:1',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }

            $referenceId = $this->makeYasReference();

            $txn = YasTransaction::create([
                'reference_id'    => $referenceId,
                'booking_id'      => null,
                'customer_msisdn' => $request['phoneNumber'],
                'biller_msisdn'   => config('yas.biller_msisdn'),
                'amount'          => $request['amount'],
                'currency'        => 'TZS',
                'remarks'         => 'DITF gate ticket (POS)',
                'status'          => YasTransaction::STATUS_PENDING,
            ]);

            $result = $this->yasPushBillPay(
                $request['phoneNumber'],
                (float) $request['amount'],
                $referenceId,
                'DITF gate ticket'
            );

            $txn->update([
                'status'           => $result['ok'] ? YasTransaction::STATUS_PUSHED : YasTransaction::STATUS_FAILED,
                'request_payload'  => [
                    'CustomerMSISDN' => $request['phoneNumber'],
                    'Amount'         => $request['amount'],
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
                'message'     => 'USSD push sent. Ask the customer to approve the prompt.',
                'referenceId' => $referenceId,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 0, 'message' => config('Messages.500')]);
        }
    }

    /**
     * Poll the status of a POS YAS push by ReferenceID.
     * Body: token, referenceId. Returns paymentStatus = PENDING|PUSHED|SUCCESS|FAILED.
     */
    public function yasPosStatus(Request $request)
    {
        try {
            // Token-free (same rationale as yasPosPush).
            $txn = YasTransaction::where('reference_id', $request['referenceId'])->first();
            if (! $txn) {
                return response()->json([
                    'status'        => 0,
                    'message'       => 'Unknown reference',
                    'paymentStatus' => 'UNKNOWN',
                ]);
            }
            return response()->json([
                'status'           => 1,
                'paymentStatus'    => $txn->status, // PENDING | PUSHED | SUCCESS | FAILED
                'description'      => $txn->error
                    ?? ($txn->status === YasTransaction::STATUS_SUCCESS ? 'Payment completed' : null),
                'mfsTransactionId' => $txn->mfs_transaction_id,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 0, 'message' => config('Messages.500')]);
        }
    }

    #*********** CREATE CONSUMER TRANSACTION (self-service / tantradebiasharaapp) **********#
    /**
     * Public consumer endpoint used by the tantradebiasharaapp Flutter app.
     * Accepts one or more tickets in a single request, validates each
     * `ticketAmount` against the canonical `vip_ticket_packages.package_cost`
     * (anti-tampering), checks for duplicate `ticketId`, then inserts a
     * `ProducedTicket` row per ticket. The validator at
     * `ValidationApiController::store` (line 104) matches by
     * `ProducedTicket.ticketId`, so once a row is written the QR scans as
     * VALID TICKET.
     *
     * Request body (JSON):
     * {
     *   "phoneNumber":  "+255712345678",        // from logged-in user
     *   "customerName": "Jane Doe",              // optional; defaults to phone
     *   "tickets": [
     *     {
     *       "ticketId":     "<uuid v1 from the app>",
     *       "ticketNo":     "<app-generated number>",
     *       "serviceId":    "<vip_package_id>",
     *       "ticketAmount": 5000,
     *       "plateNumber":  "T123ABC"            // optional; "" if N/A
     *     }
     *   ]
     * }
     *
     * Response:
     *   { status: 1, message, tickets: [{ticketId, ticketNo}, ...] } on success
     *   { status: 0, message }                                       on failure
     *
     * TODO(payment): there is NO payment verification here. Before exposing
     * this endpoint to production traffic, add one of:
     *   - mobile-money push (M-Pesa / Tigo / Airtel) with a callback that
     *     creates the tickets only after payment is confirmed; or
     *   - a `paymentReference` field that we verify server-side against the
     *     payment provider before inserting; or
     *   - a pre-paid voucher code mechanism.
     * Until then, any caller who can reach the endpoint can mint free
     * tickets — keep this on the staging server only.
     */
    public function createConsumerTransaction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phoneNumber'           => 'required|string',
                'customerName'          => 'nullable|string',
                'tickets'               => 'required|array|min:1',
                'tickets.*.ticketId'    => 'required|string',
                'tickets.*.ticketNo'    => 'required|string',
                'tickets.*.serviceId'   => 'required|string',
                'tickets.*.ticketAmount' => 'required|numeric',
                'tickets.*.plateNumber' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }

            $phone        = $request->input('phoneNumber');
            $customerName = $request->input('customerName') ?: $phone;
            $tickets      = $request->input('tickets');

            // Pre-validation pass: confirm every ticket is well-formed and
            // priced correctly *before* we insert any of them. This way we
            // never end up with a half-saved order.
            $resolved = [];
            foreach ($tickets as $t) {
                $service = VipTicketPackage::where('vip_package_id', $t['serviceId'])->first();
                if (!$service) {
                    return response()->json([
                        'status'  => 0,
                        'message' => 'Unknown service: ' . $t['serviceId'],
                    ]);
                }
                // String compare to avoid float-equality surprises across
                // currencies/locales. `package_cost` is whatever the DB
                // returns; we treat the client value as authoritative only
                // after this check passes.
                if ((string) $service->package_cost !== (string) $t['ticketAmount']) {
                    return response()->json([
                        'status'  => 0,
                        'message' => 'Service cost and ticket Amount mismatch!',
                    ]);
                }
                if (ProducedTicket::where('ticketId', $t['ticketId'])->exists()) {
                    return response()->json([
                        'status'  => 0,
                        'message' => 'Duplicate ticketId: ' . $t['ticketId'],
                    ]);
                }
                $resolved[] = [
                    't'       => $t,
                    'service' => $service,
                ];
            }

            // TODO(payment): insert the payment-verification step here, e.g.
            //   if (!$this->verifyPayment($request->input('paymentReference'))) {
            //       return response()->json(['status' => 0, 'message' => 'Payment required']);
            //   }

            // produced_tickets.bookingId has a NOT NULL + FOREIGN KEY
            // constraint pointing at vip_ticket_bookings.ticket_booking_id.
            // The agent/USSD flows create a real booking row first, then
            // produce tickets against it. For self-service we have no real
            // booking, so we mint a synthetic booking row here and share
            // its id across every ticket in this submission. The validator
            // still looks up by ticketId, so bookingId only matters for
            // referential integrity + reporting, not for scanning.
            $syntheticBookingId = (string) Str::uuid();
            $totalAmount = 0;
            foreach ($resolved as $r) {
                $totalAmount += (float) $r['service']->package_cost;
            }
            $firstService = $resolved[0]['service'];

            // The vip_ticket_bookings schema is heavily constrained:
            //   - booked_package    NOT NULL, FK -> vip_ticket_packages
            //   - gfs_code_mapping  NOT NULL
            //   - payment_deadline  NOT NULL
            //   - ticket_issued     UNIQUE (so we leave NULL on synthetic rows;
            //     the existing flows treat this as an SMS-issued flag)
            //   - control_number    UNIQUE (nullable; leave NULL)
            //   - requestId         UNIQUE (nullable; leave NULL)
            // For self-service we anchor the booking to the first ticket's
            // package so the FK is satisfied, and use today as the
            // payment_deadline since the ticket is being issued immediately.
            $activeEventId = events::where('event_status', 1)->value('event_id');
            VipTicketBookings::create([
                'ticket_booking_id' => $syntheticBookingId,
                'booked_package'    => $firstService->vip_package_id,
                'customer_name'     => $customerName,
                'phone_number'      => $phone,
                // TODO(payment): set amount_paid to 0 and update on the
                // payment provider's callback. Marking it as fully paid
                // now keeps reports honest in this no-payment-yet phase.
                'amount_paid'       => $totalAmount,
                'ticket_amount'     => $totalAmount,
                'currency'          => $firstService->currency,
                'gfs_code_mapping'  => 'SELF_SERVICE',
                'payment_deadline'  => date('Y-m-d'),
                'ticket_issued_on'  => date('Y-m-d H:i:s'),
                'ticket_status'     => 1,
                'bookingEvent'      => $activeEventId,
            ]);

            $created = [];
            foreach ($resolved as $r) {
                $t       = $r['t'];
                $service = $r['service'];
                ProducedTicket::create([
                    'ticketId'     => $t['ticketId'],
                    'ticketNo'     => $t['ticketNo'],
                    'ticketAmount' => $service->package_cost,
                    'ticketName'   => $service->package_discriptions,
                    'ticketStatus' => 1,
                    'scanCount'    => 0,
                    'customerName' => $customerName,
                    'plateNumber'  => str_replace(' ', '', $t['plateNumber'] ?? ''),
                    'visitDate'    => date('Y-m-d'),
                    'bookingId'    => $syntheticBookingId,
                ]);
                $created[] = [
                    'ticketId' => $t['ticketId'],
                    'ticketNo' => $t['ticketNo'],
                ];
            }

            return response()->json([
                'status'  => 1,
                'message' => 'Tickets created successfully',
                'tickets' => $created,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 0,
                'message' => 'ERROR: ' . $th->getMessage(),
            ]);
        }
    }
    #*********** END CREATE CONSUMER TRANSACTION **********#
}
