<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\companies;
use App\Models\Companies\CompanyAdmin;
use App\Models\exhibition\exhibitors;
use App\Models\sectors;
use App\Models\settings\events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    public function SearchEvent(Request $request)
    {
        try {
            $search = $request->search;
            $events = events::orderby('event_name', 'asc')->where('event_name', 'like', '%' . $search . '%')
                ->limit(config('global.DATA_LIMIT'))->get();
            return response()->json([
                'status' => 1,
                'events' => $events,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong!' . $th->getMessage(),
            ]);
        }
    }


    public function searchCompanies(Request $request)
    {
        try {
            $search = $request->get('q');
            if (Auth::user()->account_type == 1) {
                $data = companies::where(function ($query) use ($search) {
                    $query->orWhere('companyName', 'LIKE', '%' . $search . '%');
                    $query->orWhere('registrationID', 'LIKE', '%' . $search . '%');
                })->where('status', 1)
                    ->limit(10)
                    ->get(['companyID', 'companyName', 'companyPhone', 'companyEmail', 'registrationID']);
            } elseif (Auth::user()->account_type == 2) {
                $data = companies::leftJoin('company_admins', 'companies.companyID', '=', 'company_admins.companyId')
                    ->where(function ($query) use ($search) {
                        $query->orWhere('companyName', 'LIKE', '%' . $search . '%');
                        $query->orWhere('registrationID', 'LIKE', '%' . $search . '%');
                    })->where('status', 1)->where('adminId', Auth::user()->user_uuid)
                    ->limit(10)
                    ->get(['company_admins.companyID', 'companyName', 'companyPhone', 'companyEmail', 'registrationID']);
            }
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json(['companyName' => $th->getMessage()]);
        }
    }

    public function searchInvoices(Request $request)
    {
        $search = $request->get('q');
        if (Auth::user()->account_type == 1) {
            $data = advice_notes::orderBy('advice_note_created_at', 'DESC')
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->where('advice_note_status', '!=', 'CANCELED')
                ->where('total_amount', '!=', Null)
                ->where(function ($query) use ($search) {
                    $query->orWhere('companyName', 'LIKE', '%' . $search . '%');
                    $query->orWhere('control_number', 'LIKE', '%' . $search . '%');
                    $query->orWhere('advice_note_id', 'LIKE', '%' . $search . '%');
                })
                ->limit(20)
                ->get([
                    'company_id',
                    'advice_notes.advice_note_id',
                    'companyName',
                    'advice_note_deadline',
                    'invoice_type',
                    'advice_note_status',
                    'advice_note_description',
                    'advice_note_created_at',
                    'registrationID',
                    'companyLocation',
                    'companyPhone',
                    'control_number',
                    'balance',
                    'total_amount',
                    'currency',
                    'amount_paid'
                ]);
        } elseif (Auth::user()->accaount_type == 2) {
            $data = advice_notes::orderBy('advice_note_created_at', 'DESC')
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
                ->leftJoin('company_admins', 'advice_notes.company_id', '=', 'company_admins.companyId')
                ->where('company_admins.adminId', '=', Auth::user()->user_uuid)
                ->where('advice_note_status', '!=', 'CANCELED')
                ->where(function ($query) use ($search) {
                    $query->orWhere('companyName', 'LIKE', '%' . $search . '%');
                    $query->orWhere('control_number', 'LIKE', '%' . $search . '%');
                    $query->orWhere('advice_note_id', 'LIKE', '%' . $search . '%');
                    $query->orWhere('companyPhone', 'LIKE', '%' . $search . '%');
                    $query->orWhere('total_amount', 'LIKE', '%' . $search . '%');
                    $query->orWhere('advice_note_created_at', 'LIKE', '%' . $search . '%');
                    $query->orWhere('advice_note_description', 'LIKE', '%' . $search . '%');
                })
                ->get([
                    'company_id',
                    'advice_notes.advice_note_id',
                    'companyName',
                    'advice_note_deadline',
                    'invoice_type',
                    'advice_note_status',
                    'advice_note_description',
                    'advice_note_created_at',
                    'registrationID',
                    'companyLocation',
                    'companyPhone',
                    'control_number',
                    'balance',
                    'total_amount',
                    'currency',
                    'amount_paid'
                ]);
        }
        return response()->json($data);
    }

    public function TsrnVerification(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'TSRN' => 'required|min:13|max:13'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $exists = companies::where('registrationID', 'LIKE', '%' . $request->TSRN . '%')->first();
            if ($exists) {
                return response()->json([
                    'status' => 1,
                    'message' => 'VERIFIED!!',
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'DOES NOT EXIST',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'SOMETHING WENT WRONG!',
            ]);
        }
    }


    public function searchSectors(Request $request)
    {
        $search = $request->get('q');
        $data = sectors::where(function ($query) use ($search) {
            $query->orWhere('sectorName', 'LIKE', '%' . $search . '%');
        })->get(['sectorID', 'sectorName']);
        return response()->json($data);
    }


    public function searchExhibitor(Request $request)
    {
        try {

            $search = $request->get('q');
            if (Auth::user()->account_type == 1) {
                $data = companies::where(function ($query) use ($search) {
                    $query->orWhere('companyName', 'LIKE', '%' . $search . '%');
                    $query->orWhere('registrationID', 'LIKE', '%' . $search . '%');
                })->whereIn('companyID', exhibitors::where('exhibitor_event', session('liveEvent'))->get(['exhibitor_id']))
                    ->where('status', 1)
                    ->limit(10)
                    ->get(['companyID', 'companyName', 'companyPhone', 'companyEmail', 'registrationID']);
            } elseif (Auth::user()->account_type == 2) {
                $data = companies::leftJoin('company_admins', 'companies.companyID', '=', 'company_admins.companyId')
                    ->where(function ($query) use ($search) {
                        $query->orWhere('companyName', 'LIKE', '%' . $search . '%');
                        $query->orWhere('registrationID', 'LIKE', '%' . $search . '%');
                    })->where('status', 1)->where('adminId', Auth::user()->user_uuid)
                    ->whereIn('companies.companyID', exhibitors::where('exhibitor_event', session('liveEvent'))->get(['exhibitor_id']))
                    ->limit(10)
                    ->get(['company_admins.companyID', 'companyName', 'companyPhone', 'companyEmail', 'registrationID']);
            }
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json(['companyName' => $th->getMessage()]);
        }
    }
}
