<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\booking\booked_pavilions;
use App\Models\exhibition\exhibitors;
use App\Models\sectors;
use App\Models\setting\ApiRequest;
use App\Models\settings\events;
use App\Models\space\booths;
use App\Models\space\pavilions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\APITraits;

class SabaSabaAppController extends Controller
{
    use APITraits;

    public function getToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'gatewayId' => 'required',
                'spCode' => 'required|exists:api_requests,request_name',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $data = ApiRequest::where('request_name', $request->spCode)->get([
                'access_token as accessToken',
                'request_name as spCode'
            ]);
            return response()->json([
                'status' => 1,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('Messages.500'),
            ]);
        }
    }
    public function getAllEvents(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'spCode' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $status = $this->AuthenticateRequest($request->spCode, $request->token);
            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 404);
            }
            $events = events::orderBy('event_status', 'DESC')->get([
                'event_id',
                'event_name',
                'event_start_date',
                'event_end_date',
                'event_descriptions',
                'event_location',
                'event_status'
            ]);
            return response()->json([
                'status' => 1,
                'events' => $events
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('Messages.500') . $th->getMessage(),
            ], 500);
        }
    }

    # ----- GET ALL SECTORS
    public function getAllSectors(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'spCode' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $status = $this->AuthenticateRequest($request->spCode, $request->token);
            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 404);
            }
            $sectors = sectors::where('sectorStatus', 1)
                ->get([
                    'sectorID',
                    'sectorName',
                    'sectorDescription',
                    'sectorImage'
                ]);
            return response()->json([
                'status' => 1,
                'sectors' => $sectors
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('Messages.500') . $th->getMessage(),
            ], 500);
        }
    }

    #---- GET getAllExhibitors
    public function getExhibitorsByEventId(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'spCode' => 'required',
                'exhibitionYear' => 'required|exists:events,event_id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $status = $this->AuthenticateRequest($request->spCode, $request->token);
            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 404);
            }
            $event = $request->exhibitionYear;
            $exhibitors = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
                ->leftJoin('events', 'exhibitors.exhibitor_event', '=', 'events.event_id')
                ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorId')
                ->where('exhibitor_event', $event)
                ->whereIn('exhibitor_id', advice_notes::where('event', $event)->where('advice_note_status', 'APPROVED')->get(['company_id']))
                ->paginate(25, [
                    'participation_id',
                    'companyID',
                    'companyName',
                    'event_name',
                    'sectorName',
                    'website'
                ]);
            return response()->json([
                'status' => 1,
                'exhibitors' => $exhibitors,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('Messages.500') . $th->getMessage(),
            ], 500);
        }
    }


    #---- Get Exhibitor by Sector Id
    public function getExhibitorsBySectorId(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'spCode' => 'required',
                'exhibitionYear' => 'required|exists:events,event_id',
                'sectorId' => 'required|exists:sectors,sectorId',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $status = $this->AuthenticateRequest($request->spCode, $request->token);
            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 404);
            }
            $sectorId = $request->sectorId;
            $event = $request->exhibitionYear;
            $exhibitors = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
                ->leftJoin('events', 'exhibitors.exhibitor_event', '=', 'events.event_id')
                ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorId')
                ->where('companySector', $sectorId)
                ->where('exhibitor_event', $event)
                ->whereIn('exhibitor_id', advice_notes::where('event', $event)->where('advice_note_status', 'APPROVED')->get(['company_id']))
                ->paginate(25, [
                    'participation_id',
                    'companyID',
                    'companyName',
                    'event_name',
                    'sectorName',
                    'website'
                ]);
            return response()->json([
                'status' => 1,
                'exhibitors' => $exhibitors,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('Messages.500') . $th->getMessage(),
            ], 500);
        }
    }


    # Get Exhibitor Location
    #---- Get Exhibitor by Sector Id
    public function getExhibitorLocation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'spCode' => 'required',
                'participation_id' => 'required|exists:exhibitors,participation_id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $status = $this->AuthenticateRequest($request->spCode, $request->token);
            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 404);
            }
            $participation_id = $request->participation_id;
            $exhibitor = exhibitors::find($participation_id);
            
            $partitionLocation = booked_pavilions::leftJoin('booths', 'booked_pavilions.pavilionID', '=', 'booths.boothID')
                ->leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
                ->leftJoin('streets', 'pavilions.spaceStreet', '=', 'streets.streetId')
                ->leftJoin('companies', 'booked_pavilions.companyID', '=', 'companies.companyID')
                ->leftJoin('events', 'booked_pavilions.exhibition_year', '=', 'events.event_id')
                ->where('booked_pavilions.companyID', $exhibitor->exhibitor_id)
                ->where('exhibition_year', $exhibitor->exhibitor_event)
                ->whereIn('booked_pavilions.pavilionID', booths::get(['boothID']))
                ->get([
                    'boothNo as partitionName',
                    'pavilionName',
                    'pavilionDescription',
                    'companyName',
                    'streetName',
                    'pavilions.latitude',
                    'pavilions.longitude',
                ]);

            $pavilionLocation = booked_pavilions::leftJoin('pavilions', 'booked_pavilions.pavilionID', 'pavilions.pavilionID')
                ->leftJoin('streets', 'pavilions.spaceStreet', '=', 'streets.streetId')
                ->leftJoin('companies', 'booked_pavilions.companyID', '=', 'companies.companyID')
                ->leftJoin('events', 'booked_pavilions.exhibition_year', '=', 'events.event_id')
                ->where('booked_pavilions.companyID', $exhibitor->exhibitor_id)
                ->where('exhibition_year', $exhibitor->exhibitor_event)
                ->whereIn('booked_pavilions.pavilionID', pavilions::get(['pavilionID']))
                ->get([
                    'pavilionName',
                    'pavilionDescription',
                    'streetName',
                    'companyName',
                    'pavilions.latitude',
                    'pavilions.longitude',
                ]);
            return response()->json([
                'status' => 1,
                'partitionLocation' => $partitionLocation,
                'pavilionLocation' => $pavilionLocation,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('Messages.500') . $th->getMessage(),
            ], 500);
        }
    }


    public function searchExhibitor(Request $request){
        try {
            $event=$request->exhibitionYear;
            $search=$request->search;
            $exhibitors=exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
            ->leftJoin('events', 'exhibitors.exhibitor_event', '=', 'events.event_id')
            ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorId')
            ->where('exhibitor_event', $event)
            ->whereIn('exhibitor_id', advice_notes::where('event', $event)->where('advice_note_status', 'APPROVED')->get(['company_id']))
            ->where(function ($query) use ($search) {
                $query->orWhere('companyName', 'LIKE', '%' . $search . '%');
                $query->orWhere('registrationID', 'LIKE', '%' . $search . '%');
            })->paginate(5, [
                    'participation_id',
                    'companyID',
                    'companyName',
                    'event_name',
                    'sectorName',
                    'website'
                ]);
                return response()->json([
                    'status'=>1,
                    'exhibitors'=>$exhibitors
                ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>config('Messages.500')
            ]);
        }
    }
}
