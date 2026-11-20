<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\companies;
use App\Models\exhibition\exhibitors;
use App\Models\sectors;
use App\Models\settings\events;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\UserRegistrationTrait;
use App\Models\booking\booked_booths;
use App\Models\booking\booked_pavilions;
use App\Models\Companies\CompanyAdmin;
use App\Models\ePermit\ApplicationInvoice;
use App\Models\ePermit\ePermitApplication;
use App\Models\ePermit\PromotionEventType;
use App\Models\exhibition\exhibition_products;
use App\Models\Inquiry\BookingInquiry;
use App\Models\setting\ExchangeRate;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Traits\ThirdPartIntergrationTraits;

class userDashboardController extends Controller
{
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
            session(['moduleNo' => 1]); // for SAMS;
        $activeEvent = events::where('event_status', 1)->first();
        if (!$activeEvent) {
            $request->session()->forget('liveEvent');

            if ($request->user()->can('View Events', Role::class)) {
                return redirect()->route('Exhibition-Events.index')
                    ->with('info', 'Create or activate an exhibition event to open the Trade Fair dashboard.');
            }

            return redirect()->route('startApp')
                ->with('fail', 'Ask an administrator to activate an exhibition event.');
        }

        $event_id = $activeEvent->event_id;
        $request->session()->put('liveEvent', $event_id);
        $companies = companies::where('status', 1)->get()->count(); // return all registered companies
        $sectorCount = sectors::get()->count();

        $total_events = events::get()->count();

        $events = events::orderBy('event_start_date', 'DESC')->get(['event_name', 'event_id']);
        $exhibitors = exhibitors::get(['exhibitor_event']);
        $currentExhibitors = exhibitors::where('exhibitor_event', session('liveEvent'))->count();
        $allExhibitors = exhibitors::count();

        $accounts = User::where('user_uuid', Auth::user()->user_uuid)->get();
        foreach ($accounts as $key) {
            $account_type = $key->account_type;
        }

        $myCompany = companies::where('companyID', Auth::user()->myCompany)->get();

        $exhibited_products = exhibition_products::where('event', $event_id)->count(); // count all products exhibited

        $total_invoices = advice_notes::leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
            ->where('event_status', 1)->where('advice_note_status', '!=','CANCELED')->count(); // count all invoice Produced.

        $expected_tzs = advice_notes::leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
            ->where('event_status', 1)->where('advice_note_status', '!=','CANCELED')->where('currency', 'TZS')->sum('total_amount'); // Collection TZS.

        $expected_usd = advice_notes::leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
            ->where('event_status', 1)->where('advice_note_status', '!=','CANCELED')->where('currency', 'USD')->sum('total_amount'); // COLLECTION USD.

        $amount_paid_tzs = advice_notes::leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
            ->where('event_status', 1)->where('advice_note_status', '!=','CANCELED')->where('currency', 'TZS')->sum('amount_paid'); // Amount Paid TZS.

        $amount_paid_usd = advice_notes::leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
            ->where('event_status', 1)->where('advice_note_status', '!=','CANCELED')->where('currency', 'USD')->sum('amount_paid'); // Amount Paid USD.


        $booths = booked_booths::leftJoin('booths', 'booked_booths.boothID', '=', 'booths.boothID')
            ->leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
            ->where('companyID', Auth::user()->user_uuid)->orderBy('boothNo', 'ASC')->get();

        $pavilions = booked_pavilions::leftJoin('pavilions', 'booked_pavilions.pavilionID', '=', 'pavilions.pavilionID')
            ->leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
            ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
            ->where('advice_note_status', '!=','CANCELED')->where('companyID', Auth::user()->user_uuid)
            ->where('companyID', Auth::user()->user_uuid)->get();

        $sectors = sectors::get();
        $eventName = $activeEvent->event_name;

        // $judging = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
        //     ->leftJoin('judging_categories', 'exhibitors.judgingCategory', '=', 'judging_categories.categoryId')
        //     ->whereIn('exhibitor_id',CompanyAdmin::where('adminId',Auth::user()->user_uuid)->get(['companyId']))
        //     ->where('judging_status',1)
        //     ->where('exhibitor_event', session('liveEvent'))->get();

        $newRequest = BookingInquiry::where('eventId', $event_id)->where('inquiryStatus', 0)->count();
        $attendedRequest = BookingInquiry::where('eventId', $event_id)->where('inquiryStatus', '!=','CANCELED')->count();
        $myCompany =CompanyAdmin::where('adminId',Auth::user()->user_uuid)->get();
        $myCompanyCount =CompanyAdmin::where('adminId',Auth::user()->user_uuid)->count();
        session(['myCompany'=>$myCompanyCount]);
        return view('dashboard', compact(
            'allExhibitors',
            'companies',
            'sectors',
            'booths',
            'pavilions',
            'total_events',
            'events',
            'exhibitors',
            'companies',
            'myCompany',
            'currentExhibitors',
            'exhibited_products',
            'total_invoices',
            'expected_tzs',
            'expected_usd',
            'amount_paid_tzs',
            'amount_paid_usd',
            'sectorCount',
            'eventName',
            'newRequest',
            'attendedRequest'
        ));
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500').$th->getMessage());
        }
    }

    public function ePermitDashboard(Request $request)
    {
        try {
            if ($request->user()->cannot('View ePermitDashboard', Role::class)) {
                return back()->with('fail', config('Messages.401'));
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
