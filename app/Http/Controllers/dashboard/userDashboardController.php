<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\companies;
use App\Models\exhibition\exhibitors;
use App\Models\sectors;
use App\Models\settings\events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\System;
use App\Traits\UserRegistrationTrait;
use App\Models\booking\booked_pavilions;
use App\Models\Companies\CompanyAdmin;
use App\Models\ePermit\ApplicationInvoice;
use App\Models\ePermit\ePermitApplication;
use App\Models\ePermit\PromotionEventType;
use App\Models\exhibition\exhibition_products;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Traits\ThirdPartIntergrationTraits;

class userDashboardController extends Controller
{
    use System;
    use UserRegistrationTrait;
    use ThirdPartIntergrationTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function StartApplication(Request $request)
    {
       
        $request->session()->forget('moduleNo');
        // $ExchangeRate = ExchangeRate::where('lastUpdatedAt','LIKE','%'.date('Y-m-d ').'%')->first();
        // if (!$ExchangeRate && config('global.APP_STATUS')==1) {
        //     $this->getAllExchageRate();
        // }

        $myCompany=companies::find(Auth::user()->user_uuid);
        $exists=CompanyAdmin::where('companyId',$myCompany->companyID ?? Null)
        ->where('adminId',Auth::user()->user_uuid)->first();
        if ($myCompany) {
            if (!$exists) {
                CompanyAdmin::create([
                    'referenceId'=>Str::uuid(),
                    'adminId'=>Auth::user()->user_uuid,
                    'companyId'=>$myCompany->companyID,
                ]);
            }
        }
        $myCompany =CompanyAdmin::where('adminId',Auth::user()->user_uuid)->count();
        session(['myCompany'=>$myCompany]);

        // Auto-fix: if users.myCompany is NULL but company_admins has a record, backfill it
        if (!Auth::user()->myCompany) {
            $companyAdmin = CompanyAdmin::where('adminId', Auth::user()->user_uuid)->first();
            if ($companyAdmin) {
                Auth::user()->update(['myCompany' => $companyAdmin->companyId]);
            }
        }

        return view('FrontEnd.Dashboard.StartDashboard');
    }
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $request->session()->put('moduleNo', 1);
            $event = events::where('event_status', 1)->first();

            if (!$event) {
                $request->session()->forget('liveEvent');

                if ($user->can('VIEW_EXHIBITION_EVENT_SUBMENU', Role::class)) {
                    return redirect()->route('Exhibition-Events.index')
                        ->with('info', 'Create or activate an exhibition event to open the Trade Fair dashboard.');
                }

                return redirect()->route('startApp')
                    ->with('fail', 'Ask an administrator to activate an exhibition event.');
            }

            $companyIds = CompanyAdmin::where('adminId', $user->user_uuid)
                ->whereNotNull('companyId')->distinct()->pluck('companyId');
            $request->session()->put('liveEvent', $event->event_id);
            $request->session()->put('myCompany', $companyIds->count());

            $isStaff = (int) $user->account_type === 1;
            $canViewInvoices = $user->can('MANAGE_INVOICES', Role::class);
            $canViewCollections = $canViewInvoices
                || ($isStaff && $user->can('VIEW_COLLECTION_REPORTS'));
            $canViewBookings = !$isStaff
                || $user->can('VIEW_PENDING_BOOKING', Role::class)
                || $user->can('VIEW_SAMMARY_REPORT');
            $bookingOpen = $this->checkApplicationStatus();

            $invoices = advice_notes::query()
                ->where('advice_notes.event', $event->event_id)
                ->where('advice_notes.advice_note_status', '!=', 'CANCELED')
                ->when(!$isStaff, fn ($query) => $query->whereIn('advice_notes.company_id', $companyIds));

            $invoiceTotals = collect();
            $recentInvoices = collect();
            if ($canViewCollections) {
                $invoiceTotals = (clone $invoices)
                    ->selectRaw('currency, COUNT(*) AS invoice_count, SUM(total_amount) AS billed, SUM(amount_paid) AS paid')
                    ->selectRaw('SUM(CASE
                        WHEN balance > 0 THEN balance
                        WHEN balance IS NULL AND total_amount > COALESCE(amount_paid, 0)
                            THEN total_amount - COALESCE(amount_paid, 0)
                        ELSE 0
                    END) AS outstanding')
                    ->groupBy('currency')
                    ->get()
                    ->keyBy('currency');
            }

            if ($canViewInvoices) {
                $recentInvoices = (clone $invoices)
                    ->leftJoin('companies', 'companies.companyID', '=', 'advice_notes.company_id')
                    ->orderByDesc('advice_notes.advice_note_created_at')
                    ->orderByDesc('advice_notes.advice_note_id')
                    ->limit(5)
                    ->get([
                        'advice_notes.advice_note_id', 'advice_notes.advice_note_created_at',
                        'advice_notes.advice_note_status', 'advice_notes.control_number', 'advice_notes.total_amount',
                        'advice_notes.amount_paid', 'advice_notes.balance',
                        'advice_notes.currency', 'companies.companyName',
                    ]);
            }

            $draftSpaces = 0;
            $invoicedSpaces = 0;
            if ($canViewBookings) {
                $spaces = booked_pavilions::where('exhibition_year', $event->event_id);
                $draftSpaces = (clone $spaces)->whereNull('advice_note_id')
                    ->when(!$isStaff, fn ($query) => $query->where('userID', $user->user_uuid))
                    ->count();
                $invoicedSpaces = $spaces
                    ->whereIn('advice_note_id', (clone $invoices)->select('advice_notes.advice_note_id'))
                    ->count();
            }

            $exhibitionStats = [];
            if ($isStaff && $user->can('VIEW_SAMMARY_REPORT')) {
                $exhibitionStats = [
                    ['Exhibitors this event', exhibitors::where('exhibitor_event', $event->event_id)->count(), 'discover_exhibitor.index'],
                    ['Exhibitors across events', exhibitors::count(), 'exhibitors.index'],
                    ['Exhibited products', exhibition_products::where('event', $event->event_id)->count(), 'products.index'],
                    ['Registered companies', companies::where('status', 1)->count(), 'companies.index'],
                    ['Exhibition events', events::count(), 'Exhibition-Events.index'],
                    ['Company sectors', sectors::count(), 'sectors.index'],
                ];
            }

            $participation = collect();
            if ($isStaff && $user->can('VIEW_PARTICIPATION_REPORTS')) {
                $recentEvents = events::orderByDesc('event_start_date')->orderBy('event_id')->limit(6)
                    ->get(['event_id', 'event_name']);
                $exhibitorCounts = exhibitors::whereIn('exhibitor_event', $recentEvents->pluck('event_id'))
                    ->selectRaw('exhibitor_event, COUNT(*) AS exhibitor_count')
                    ->groupBy('exhibitor_event')
                    ->pluck('exhibitor_count', 'exhibitor_event');
                $participation = $recentEvents->map(fn ($edition) => [
                    'label' => $edition->event_name,
                    'value' => (int) ($exhibitorCounts[$edition->event_id] ?? 0),
                ]);
            }

            return view('dashboard', compact(
                'event', 'isStaff', 'bookingOpen', 'canViewBookings', 'canViewInvoices', 'canViewCollections',
                'draftSpaces', 'invoicedSpaces', 'invoiceTotals', 'recentInvoices', 'exhibitionStats', 'participation'
            ));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500').$th->getMessage());
        }
    }

    public function ePermitDashboard(Request $request)
    {
        try {
            if ($request->user()->cannot('VIEW_E_PERMIT_DASHBOARD', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('VIEW_E_PERMIT_DASHBOARD'));
            }
            session(['moduleNo' => 2]);
            $promotionEventTypes = PromotionEventType::get();
            $applications = ePermitApplication::get();

            $permits = ApplicationInvoice::leftJoin('e_permit_applications', 'application_invoices.referenceApplication', '=', 'e_permit_applications.applicationId')
                ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
                ->leftJoin('companies', 'application_invoices.customerId', '=', 'companies.companyID')
                ->leftJoin('promotion_event_types', 'trade_promotion_events.tradeEventType', '=', 'promotion_event_types.eventTypeId')
                ->leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
                ->where('appliedBy', Auth::user()->user_uuid)
                ->where('applicationStatus', 6)->get();
                $companies=companies::leftJoin('company_admins','companies.companyID','=','company_admins.companyId')
                ->where('adminId',Auth::user()->user_uuid)->get();
            return view('ePermit.ePermitDashboard', compact('permits', 'promotionEventTypes', 'applications','companies'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
