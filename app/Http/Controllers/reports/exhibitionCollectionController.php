<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\advice_note\advice_notes;
use App\Models\settings\events;
use App\Models\companies;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class exhibitionCollectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the exhibition collection report.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('MANAGE_COLLECTION_REPORTS', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_COLLECTION_REPORTS'));
        }

        try {
            // ---------- Date range ----------
            if ($request->from_date || $request->to_date) {
                $from_date = date('Y-m-d', strtotime($request->from_date));
                $to_date   = date('Y-m-d', strtotime($request->to_date));
            } else {
                $from_date = date('Y-m-d', strtotime(now()->subMonth()));
                $to_date   = date('Y-m-d');
            }

            // ---------- Events ----------
            $events = events::orderBy('event_start_date', 'DESC')->get();

            if ($request->event_name) {
                $event_id = $request->event_name;
            } else {
                $latest = events::orderBy('event_start_date', 'DESC')->first();
                $event_id = $latest ? $latest->event_id : null;
            }

            $eventName = events::where('event_id', $event_id)->first();
            $event_name = $eventName ? $eventName->event_name : null;

            // ---------- Session ----------
            session([
                'from_date' => $from_date,
                'to_date'   => $to_date,
                'event_id'  => $event_id,
            ]);

            // ---------- Summary bills (for totals at top) ----------
            $bills = advice_notes::where('event', $event_id)
                ->where('advice_note_status', '!=', 'CANCELED')
                ->whereBetween('advice_note_created_at', [
                    $from_date . ' 00:00:00',
                    $to_date   . ' 23:59:59',
                ])
                ->get(['total_amount', 'amount_paid', 'currency', 'total_vat_amount']);

            $nvoiceCount = advice_notes::where('event', $event_id)
                ->where('advice_note_status', '!=', 'CANCELED')
                ->whereBetween('advice_note_created_at', [
                    $from_date . ' 00:00:00',
                    $to_date   . ' 23:59:59',
                ])
                ->count();

            // ---------- DETAILED COLLECTION LIST (the missing $data) ----------
            $data = advice_notes::select(
                    'advice_notes.*',
                    'companies.companyName',
                    'companies.companyPhone'
                )
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->where('advice_notes.event', $event_id)
                ->where('advice_notes.advice_note_status', '!=', 'CANCELED')
                ->whereBetween('advice_notes.advice_note_created_at', [
                    $from_date . ' 00:00:00',
                    $to_date   . ' 23:59:59',
                ])
                ->orderBy('advice_notes.advice_note_created_at', 'DESC')
                ->paginate(20)
                ->withQueryString();

            return view('reports.exhibition_collection_report', compact(
                'events',
                'event_name',
                'event_id',
                'from_date',
                'to_date',
                'bills',
                'nvoiceCount',
                'data'
            ));

        } catch (\Throwable $th) {
            Log::error('exhibitionCollectionController@index failed: ' . $th->getMessage());
            return back()->with('fail', config('Messages.500'));
        }
    }
}