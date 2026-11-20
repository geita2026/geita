<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\ExhibitionBadges\CompanyMember;
use App\Models\settings\events;
use App\Models\Stickers\CarSticker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\VistorsTrait;

class ValidationApiController extends Controller
{
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
            $id = str_starts_with($qrcode, 'SAMS|') ? substr($qrcode, 5) : $qrcode;

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
        $sticker = CarSticker::where('plate_number', $plateNumber)
            ->where('event_id', $eventId)->first();

        if ($sticker) {
            return response()->json([
                'status' => 1,
                'message' => 'VALID CAR STICKER',
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'INVALID DATA',
        ]);
    }
}
