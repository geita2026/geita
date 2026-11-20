<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\ExhibitionBadges\CompanyMember;
use App\Models\Logs\ApiLogs;
use App\Models\settings\events;
use App\Models\Stickers\CarSticker;
use App\Models\Tickets\PosAgent;
use App\Models\Tickets\PosTransaction;
use App\Models\Tickets\ProducedTicket;
use App\Models\Tickets\VipTicketBookings;
use App\Traits\APITraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Traits\VistorsTrait;
use Illuminate\Support\Str;

class ValidationApiController extends Controller
{
    use APITraits;
    use VistorsTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'verifiableData' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all())
                ]);
            }
            $qrcode = $request->verifiableData;

            // ── Parse QR format → extract lookup ID ──────────────────────────
            // Supported formats (newest to oldest):
            //   TICKET|YYYYMMDD|<uuid>           regular day-ticket  (new smart)
            //   TICKET|YYYYMMDD|<uuid>|<secret>  same + agent secret appended
            //   VIP|YYYYMMDD-YYYYMMDD|<uuid>     VIP season ticket   (new smart)
            //   SAMS|<member_id>                 exhibition badge    (existing)
            //   <plain-uuid>                     legacy ticket       (existing)
            if (str_starts_with($qrcode, 'SAMS|')) {
                // Exhibition badge: strip prefix, keep member_id
                $id = substr($qrcode, 5);
            } elseif (str_starts_with($qrcode, 'ETAC|')) {
                // Signed POS ticket: ETAC|YYYYMMDD|uuid|sig. The signature is
                // for offline (hub) verification only — server validates by the
                // bare uuid exactly like a legacy POS ticket. Strip to segment 3.
                $parts = explode('|', $qrcode);
                $id    = $parts[2] ?? $qrcode;
            } elseif (preg_match('/^(TICKET|VIP)\|/', $qrcode)) {
                // Smart ticket format — keep TYPE|DATE|UUID, strip optional |secret
                $parts = explode('|', $qrcode);
                $id    = implode('|', array_slice($parts, 0, 3));
            } else {
                // Legacy: plain UUID or ticketNo
                $id = $qrcode;
            }

            #************* EXHIBITION BADGES ************ #
            $eventId = events::where('event_status', 1)->first()->event_id;
            $badge = CompanyMember::leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
                ->where('event', $eventId)->where('member_id', $id)->first();
            if ($badge) {
                $base64_image = $badge->member_image;
                $company = $badge->associateCompany ?: $badge->companyName; // matches printed badge
                $name = $badge->first_name . ' ' . $badge->last_name; // Badge

                // Group label + colours so the scanner can render the same badge
                // template as the print. Fetched separately to avoid altering the
                // $badge model key used by CountNoOfEntrance()->update().
                $grp = CompanyMember::leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
                    ->where('company_members.member_id', $id)
                    ->select('badge_groups.group_name', 'badge_groups.group_background_color', 'badge_groups.text_color')
                    ->first();
                $group     = $grp->group_name ?? 'EXHIBITOR';
                $groupBg   = $grp->group_background_color ?? '#13487e';
                $groupText = $grp->text_color ?? '#ffffff';

                if ($badge->badge_status == 2) {
                    $count = $badge->scan_count;
                    return response()->json(['status' => 2, 'image' => $base64_image, 'company' => $company, 'name' => $name, 'group_name' => $group, 'group_bg' => $groupBg, 'group_text' => $groupText, 'message' => 'BLOCKED!!'], 200);
                } else {
                    $this->CountNoOfEntrance($badge);
                    $countType = 1; //ADULTS
                    $this->groundCount($countType);
                    $count = $badge->scan_count;
                    return response()->json(['status' => 1, 'image' => $base64_image, 'company' => $company, 'name' => $name, 'group_name' => $group, 'group_bg' => $groupBg, 'group_text' => $groupText, 'message' => 'SCAN COUNT: ' . $count . ' TIMES'], 200);
                }
            }
            #********* END BADGES *************#

            #***************CAR STICKERS**************#
            elseif (CarSticker::where('sticker_id', $id)->where('event_id', $eventId)->exists()) {
                $sticker = CarSticker::leftJoin('companies', 'car_stickers.company_id', '=', 'companies.companyID')
                    ->leftJoin('additional_services', 'car_stickers.reference_sticker', '=', 'additional_services.serviceID')
                    ->where('event_id', $eventId)->where('sticker_id', $id)->first();
                $cost = number_format($sticker->serviceCost + (0.18 * $sticker->serviceCost));
                $countType = 3; //CARS
                $this->groundCount($countType);
                return response()->json([
                    'status' => 1, 'image' => env('APP_URL') . '/public/car_parking.jpg', 'company' => $sticker->companyName,
                    'name' => $sticker->serviceName . ' (' . $cost . ' ' . $sticker->type_of_currency . ')', 'message' => $sticker->plate_number
                ], 200);
            }
            #*************** END CAR STICKERS ***********#


            #****************** ONLINE TICKETS *************#
            elseif (ProducedTicket::where('ticketId', $id)->exists()) {
                $ticket = ProducedTicket::where('ticketId', $id)->first();
                if ($ticket->ticketAmount >= 1500000) {
                    $newCount = $ticket->scanCount + 1;
                    $ticket->update(['scanCount' => $newCount]);
                    if ($ticket->ticketAmount == 1000) {
                        $countType = 2;
                        $this->groundCount($countType);
                    }
                    if ($ticket->ticketAmount == 3000 || $ticket->ticketAmount == 4000) {
                        $countType = 1;
                        $this->groundCount($countType);
                    }
                    return response()->json([
                        'status' => 1, 'image' => '', 'company' => '',
                        'name' => $ticket->ticketName . ' (' . number_format($ticket->ticketAmount) . ') TZS',
                        'ticket_no' => (string) $ticket->ticketNo, 'ticket_type' => (string) $ticket->ticketName,
                        'ticket_price' => 'TZS ' . number_format($ticket->ticketAmount),
                        'printed_by' => '', 'print_time' => (string) ($ticket->visitDate ?? ''),
                        'message' => 'VALID VIP TICKET',
                    ], 200);
                } elseif ($ticket->scanCount > 0) {
                    return response()->json([
                        'status' => 0, 'image' => '', 'company' => '',
                        'name' => '', 'message' => 'Ticket already used',
                        'last_scan_at' => $this->parseLastScanAt($ticket->lastScanTime),
                    ], 200);
                } elseif ($ticket->scanCount == 0) {
                    if ($ticket->ticketAmount == 1000) {
                        $countType = 2;
                        $this->groundCount($countType);
                    }
                    if ($ticket->ticketAmount == 3000 || $ticket->ticketAmount == 4000) {
                        $countType = 1;
                        $this->groundCount($countType);
                    }
                    $newCount = $ticket->scanCount + 1;
                    $ticket->update([
                        'scanCount' => $newCount,
                        'lastScanTime' => date('d-m-Y H:i:s'),
                    ]);
                    return response()->json([
                        'status' => 1, 'image' => '', 'company' => '',
                        'name' => $ticket->ticketName . ' (' . number_format($ticket->ticketAmount) . ') TZS',
                        'ticket_no' => (string) $ticket->ticketNo, 'ticket_type' => (string) $ticket->ticketName,
                        'ticket_price' => 'TZS ' . number_format($ticket->ticketAmount),
                        'printed_by' => '', 'print_time' => (string) ($ticket->visitDate ?? ''),
                        'message' => 'VALID TICKET',
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'image' => '',
                        'company' => '',
                        'name' => '',
                        'message' => 'INVALID ONLINE TICKET',
                    ], 200);
                }
            } elseif (ProducedTicket::where('ticketNo',$id)->exists()) {
                $ticket = ProducedTicket::where('ticketNo',$id)->first();
                if ($ticket->scanCount == 0) {
                    $ticket->update([
                        'scanCount' => 1,
                        'lastScanTime' => date('d-m-Y h:i:s'),
                    ]);
                    return response()->json([
                        'status' => 1,
                        'message' => 'VALID TICKET',
                        'image' => '',
                        'company' => '',
                        'name' => 'Ticket Cost: ' . number_format($ticket->ticketAmount) . ' TZS',
                        'ticket_no' => (string) $ticket->ticketNo,
                        'ticket_type' => (string) $ticket->ticketName,
                        'ticket_price' => 'TZS ' . number_format($ticket->ticketAmount),
                        'printed_by' => '',
                        'print_time' => (string) ($ticket->visitDate ?? ''),
                    ]);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Ticket already used',
                        'last_scan_at' => $this->parseLastScanAt($ticket->lastScanTime),
                        'image' => '',
                        'company' => '',
                        'name' => '',
                    ]);
                }
            }
            #************** END ONLINE TICKET ************#


            #********** LOCAL TICKETS *****************#
            elseif (PosTransaction::where('ticketId', $id)->exists()) {
                $exist = PosTransaction::where('ticketId', $id)->where('scanStatus', 1)->first();
                if ($exist) {
                    return response()->json([
                        'status' => 0,
                        'image' => '',
                        'company' => '',
                        'name' => '',
                        'message' => 'Ticket already used',
                        'last_scan_at' => strtotime($exist->ticketUpdatedAt) ?: null,
                    ]);
                }

                $transaction = PosTransaction::find($id);
                if ($transaction->ticketAmount == 1000) {
                    $countType = 2;
                    $this->groundCount($countType);
                }
                if ($transaction->ticketAmount == 3000 || $transaction->ticketAmount == 4000) {
                    $countType = 1;
                    $this->groundCount($countType);
                }
                $transaction->update([
                    'scanStatus' => 1,
                    'ticketUpdatedAt' => date('Y-m-d h:i:s'),
                ]);
                return response()->json([
                    'status' => 1,
                    'message' => 'VALID TICKET',
                    'image' => '',
                    'company' => '',
                    'name' => 'Ticket Cost: ' . number_format($transaction->ticketAmount) . ' TZS',
                    'ticket_no' => (string) $transaction->ticketNo,
                    'ticket_type' => (string) ($transaction->ticketDescription ?? ''),
                    'ticket_price' => 'TZS ' . number_format($transaction->ticketAmount),
                    // printedBy here is a user UUID, not a name — the real
                    // "Printed by" name rides in the POS ticket's signed QR. Leave
                    // blank so a legacy online-verified POS ticket never shows a UUID.
                    'printed_by' => '',
                    'print_time' => (string) ($transaction->ticketUpdatedAt ?? ''),
                ]);
            }

            #--- PRE PAID -----#
            elseif (VipTicketBookings::where('ticket_booking_id', $id)->exists()) {
                $booking = VipTicketBookings::where('ticket_booking_id', $id)->first();
                if ($booking) {
                    $tickets = ProducedTicket::where('bookingId', $id)->where('scanCount', 0)->first();
                    if ($tickets) {
                        $tickets->update([
                            'scanCount' => 1,
                        ]);
                        if ($tickets->ticketAmount == 1000) {
                            $countType = 2;
                            $this->groundCount($countType);
                        }
                        if ($tickets->ticketAmount == 3000 || $tickets->ticketAmount == 4000) {
                            $countType = 1;
                            $this->groundCount($countType);
                        }
                        return response()->json([
                            'status' => 1,
                            'message' => 'VALID TICKET',
                            'image' => '',
                            'company' => '',
                            'name' => 'Ticket Cost: ' . number_format($tickets->ticketAmount) . ' TZS',
                            'ticket_no' => (string) $tickets->ticketNo,
                            'ticket_type' => (string) $tickets->ticketName,
                            'ticket_price' => 'TZS ' . number_format($tickets->ticketAmount),
                            'printed_by' => '',
                            'print_time' => (string) ($tickets->visitDate ?? ''),
                        ]);
                    } else {

                        return response()->json([
                            'status' => 0,
                            'message' => 'INVALID TICKET',
                            'image' => '',
                            'company' => '',
                            'name' => '',
                        ]);
                    }
                }
            }
            #---- END PRE PAID -----#
            else {
                return response()->json([
                    'status' => 0,
                    'message' => 'INVALID DATA',
                    'image' => '',
                    'company' => '',
                    'name' => '',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 0, 'messsage' => 'ERROR: 00000' . $th->getMessage()], 500);
        }
    }

    // ── Admin PIN ─────────────────────────────────────────────────────────────
    // 4-digit PIN stored in default_settings.scanner_pin.
    // Admins change it from the Default Settings page at any time.
    // The PIN never travels to the Flutter app — only true/false is returned.
    public static function adminPin(): string
    {
        return (string) (\App\Models\setting\DefaultSettings::limit(1)->value('scanner_pin') ?? '0000');
    }

    /**
     * Validate the 4-digit admin PIN submitted from the Flutter app.
     * Rate-limited to 10 requests/minute per IP.
     * Returns only { valid: true|false } — never the PIN itself.
     */
    public function validateSettingsPin(Request $request)
    {
        $submitted = (string) $request->input('pin', '');
        $valid = $submitted === self::adminPin();
        // Audit which device (and agent, if logged in) used the admin PIN.
        \App\Models\setting\DeviceAccessLog::record($request, 'pin', $valid);
        return response()->json(['valid' => $valid]);
    }
    // ── End Admin PIN ─────────────────────────────────────────────────────────

    #************ LOAD TEST TICKETS **********#
    public function loadTestTickets(Request $request)
    {
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = min(100, max(10, (int) $request->get('per_page', 50)));

        $query = ProducedTicket::where('customerName', 'LIKE', 'Load Test User %')
            ->select('ticketId', 'ticketNo', 'ticketName', 'ticketAmount', 'visitDate', 'scanCount')
            ->orderBy('scanCount', 'asc')   // unscanned (0) first
            ->orderBy('ticketDate', 'desc'); // newest within same scanCount

        // Optional "isolated mode" — each test device asks for its own
        // disjoint slice of the pool so the visual scanned-state on phone A
        // never reflects a scan that landed on phone B.  CRC32 hashes the
        // uuid evenly across the ticket space so a 20-shard split gives
        // each device ~25k of the 500k tickets, deterministically.
        $shards = (int) $request->get('shards', 0);
        $shard  = (int) $request->get('shard', 0);
        if ($shards > 1 && $shard >= 1 && $shard <= $shards) {
            $query->whereRaw('MOD(CRC32(ticketId), ?) = ?', [$shards, $shard - 1]);
        }

        $tickets = $query->forPage($page, $perPage)->get();

        return response()->json([
            'status'   => 1,
            'page'     => $page,
            'per_page' => $perPage,
            'has_more' => $tickets->count() === $perPage,
            'data'     => $tickets,
        ]);
    }
    #******** END LOAD TEST TICKETS ********#

    #************ LOAD TEST SCAN STATUS (DELTA POLL) **********#
    /**
     * Returns the current `scanCount` for a list of load-test ticket IDs.
     * The scanner app polls this every ~800ms with the IDs near the
     * visible card so that scans from a second device (e.g. a gun) show
     * up in near-realtime on the first device.
     *
     * Body: { "ticketIds": ["<uuid>", "<uuid>", ...] }
     * Resp: { "scans": { "<uuid>": <int>, ... }, "serverTime": <unix> }
     */
    public function loadTestScanStatus(Request $request)
    {
        $ids = $request->input('ticketIds', []);
        if (!is_array($ids) || count($ids) === 0) {
            return response()->json([
                'scans'      => (object) [],
                'serverTime' => now()->timestamp,
            ]);
        }

        // Cap to avoid huge payloads.
        $ids = array_slice($ids, 0, 500);

        $rows = ProducedTicket::whereIn('ticketId', $ids)
            ->select('ticketId', 'scanCount')
            ->get();

        $scans = [];
        foreach ($rows as $row) {
            $scans[$row->ticketId] = (int) $row->scanCount;
        }

        return response()->json([
            'scans'      => (object) $scans, // force JSON object even when empty
            'serverTime' => now()->timestamp,
        ]);
    }
    #******** END LOAD TEST SCAN STATUS ********#

    #************ OPEN GET **********#
    public function openGate(Request $request)
    {

        $eventId = events::where('event_status', 1)->first()->event_id;

        // $sesonal = CarSticker::where('plate_number', '!=', Null)->where('event_id', $eventId)->get();
        // $dailyOnline = ProducedTicket::where('plateNumber', '!=', Null)->get();
        // $daily = PosTransaction::where('plateNumber', 'LIKE', '!=', Null)->get();

        // foreach ($sesonal as $value) {
        //     $result = str_replace(' ', '', $value->plate_number);
        //     CarSticker::find($value->sticker_id)->update([
        //         'plate_number'=>$result,
        //     ]);
        // }

        // foreach ($dailyOnline as $value) {
        //     $result = str_replace(' ', '', $value->plateNumber);
        //     ProducedTicket::find($value->ticketId)->update([
        //         'plateNumber'=>$result,
        //     ]);
        // }

        // foreach ($daily as $value) {
        //     $result = str_replace(' ', '', $value->plateNumber);
        //     PosTransaction::find($value->ticketId)->update([
        //         'plateNumber'=>$result,
        //     ]);
        // }

        // return 1;

        try {
            $validator = Validator::make($request->all(), [
                'plateNumber' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all())
                ]);
            }
            $qrcode = str_replace(' ','',$request->plateNumber);
            $id = $qrcode;
            $status = 0;

            #***************CAR STICKERS**************#
            $sesonal = CarSticker::where('plate_number', 'LIKE', '%' . $id . '%')->where('event_id', $eventId)->count();
            $dailyOnline = ProducedTicket::where('plateNumber', 'LIKE', '%' . $id . '%')->count();
            $daily = PosTransaction::where('plateNumber', 'LIKE', '%' . $id . '%')->count();
            $status = 0;
            if ($sesonal > 0 || $daily > 0 || $dailyOnline > 0) {
                $status = 1;
            }
            if (substr($id, 0, 1)=='/') {
                $status=1;   
            }

            $gateUser = PosAgent::where('agentToken', $request->token)->first();
            if ($gateUser) {
                if ($gateUser->userName == 'G1' && $status == 1) {
                    Http::get('http://192.168.3.13/open'); // Cobil IN
                    // Http::get('http://192.168.3.16/open'); // Cobil OUT
                    return response([
                        'status' => 1,
                        'message' => 'GATE No. 01 OPENED',
                        'image' => '',
                        'company' => '',
                        'name' => 'GATE No. 01 OPENED',
                    ]);
                }
                if ($gateUser->userName == 'G2' && $status == 1) {
                    Http::get('http://192.168.3.10/open'); // Cobil IN
                    // Http::get('http://192.168.3.16/open'); // Cobil OUT
                    return response([
                        'status' => 1,
                        'message' => 'GATE No. 02 OPENED',
                        'image' => '',
                        'company' => '',
                        'name' => 'GATE No. 02 OPENED',
                    ]);
                }
                if ($gateUser->userName == 'G3' && $status == 1) {
                    // Http::get('http://192.168.3.8/open'); // Cobil IN
                    Http::get('http://192.168.3.16/open'); // Cobil OUT
                    return response([
                        'status' => 1,
                        'message' => 'GATE No. 03 OPENED',
                        'image' => '',
                        'company' => '',
                        'name' => 'GATE No. 03 OPENED',
                    ]);
                } else {
                    return response([
                        'status' => 0,
                        'message' => $qrcode . ' DOES NOT EXIST',
                        'image' => '',
                        'company' => '',
                        'name' => '',
                    ]);
                }
            } else {
                return response([
                    'status' => 0,
                    'message' => 'CHECK USER TOKEN MIGHT BE INCORRECT',
                    'image' => '',
                    'company' => '',
                    'name' => '',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 0, 'messsage' => 'ERROR: 00000' . $th->getMessage()], 500);
        }
    }
    # *********** END OPEN GATE ************* #


    /**
     * Snapshot of server-issued (online / MPESA / web) tickets so a LAN
     * hub can validate them while fully offline. Read-only; does not
     * mutate any ticket. POS tickets are NOT returned — they are verified
     * offline by HMAC signature instead. The hub calls this while it
     * still has internet, then serves the cached set to scanners.
     */
    public function todayTickets(Request $request)
    {
        $event = events::where('event_status', 1)->first();
        $tickets = ProducedTicket::where('ticketStatus', 1)
            ->get(['ticketId', 'ticketNo', 'scanCount', 'lastScanTime', 'visitDate', 'ticketName', 'ticketAmount'])
            ->map(function ($t) {
                return [
                    'ticketId' => $t->ticketId,
                    'ticketNo' => $t->ticketNo,
                    'scanCount' => (int) $t->scanCount,
                    'lastScanAt' => $this->parseLastScanAt($t->lastScanTime),
                    'visitDate' => $t->visitDate,
                    'ticketName' => $t->ticketName,
                    'ticketAmount' => $t->ticketAmount,
                ];
            });
        return response()->json([
            'status' => 1,
            'eventId' => $event ? $event->event_id : null,
            'serverTime' => time(),
            'count' => $tickets->count(),
            'tickets' => $tickets,
        ], 200);
    }

    // Parse lastScanTime stored as 'd-m-Y H:i:s' (or old 'd-m-Y h:i:s') into Unix timestamp
    private function parseLastScanAt(?string $dateStr): ?int
    {
        if (!$dateStr) return null;
        $dt = \DateTime::createFromFormat('d-m-Y H:i:s', $dateStr)
            ?: \DateTime::createFromFormat('d-m-Y h:i:s', $dateStr);
        return $dt ? $dt->getTimestamp() : null;
    }

    #******** SCAN COUNT *********#
    public function CountNoOfEntrance($badge)
    {
        $count = $badge->scan_count + 1;
        $badge->update(['scan_count' => $count]);
    }
    #******** SCAN COUNT ********#



    #***************** eGATE Verifications **************#
    public function eGateVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'spCode' => 'required',
            'token' => 'required',
            'plateNumber' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => implode(',', $validator->getMessageBag()->all())
            ]);
        }
        $plateNumber = $request->plateNumber;
        $spCode = $request->spCode;
        $token = $request->token;
        // $status = $this->AuthenticateRequest($spCode, $token);
        $status = 1;
        if ($status == 0) {
            return response()->json([
                'status' => 0,
                'message' => 'Authentication Error!',
            ]);
        }

        $eventId = events::where('event_status', 1)->first()->event_id;
        $onlineTicket = ProducedTicket::where('plateNumber', $plateNumber)->first();
        if ($onlineTicket) {
            if ($onlineTicket->scanCount > 0) {
                return response()->json([
                    'status' => 0,
                    'message' => 'USED TICKET',
                ]);
            }
            return response()->json([
                'status' => 1,
                'message' => 'VALID CAR TICKET',
            ]);
        }
        $sticker = CarSticker::where('plate_number', $plateNumber)
            ->where('event_id', $eventId)->first();

        if ($sticker) {
            return response()->json([
                'status' => 1,
                'message' => 'VALID CAR STICKER',
            ]);
        }

        $transaction = PosTransaction::where('plateNumber', $plateNumber)
            ->where('eventId', $eventId)->where('scanStatus', 0)->first();

        if ($transaction) {
            $transaction->update(['scanStatus' => 1]);
            return response()->json([
                'status' => 1,
                'message' => 'VALID CAR STICKER',
            ]);
        } {
            return response()->json([
                'status' => 0,
                'message' => 'INVALID DATA',
            ]);
        }
    }
}
