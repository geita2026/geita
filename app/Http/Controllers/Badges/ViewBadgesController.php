<?php

namespace App\Http\Controllers\Badges;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use Illuminate\Http\Request;
use App\Models\ExhibitionBadges\CompanyMember;
use App\Models\companies;
use App\Models\ExhibitionBadges\BadgeGroup;
use App\Models\Notifications\BulkSms;
use App\Models\setting\DefaultSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ViewBadgesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->user()->cannot('View Badges List', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }

        $badges = CompanyMember::leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
            ->leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
            ->where('member_id', $request->badge_id)->get();

        $id      = $request->badge_id;
        $balance = 0;

        return view('Validations.BadgesValidations', compact('badges', 'balance', 'id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('View Badges List', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }
        //

        $groups = BadgeGroup::orderBy('group_name', 'ASC')->get();
        $members = CompanyMember::select('company_members.company_id', 'company_members.badge_status')->get();
        $companies = companies::get();
        $PrintedBadges   = CompanyMember::where('badge_status', 'PRINTED')->count();
        $UnprintedBadges = CompanyMember::where('badge_status', 'UNPRINTED')->count();

        return view('Badges.badges_list', compact('groups', 'members', 'companies', 'PrintedBadges', 'UnprintedBadges'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('Print Exhibitor Badges', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }

        # ── Resolve target badges (bulk form or single id) ──────────────
        if ($request->badges == 'badges') {
            if ($request->badge_id == null) {
                return back()->with('fail', 'Please select atlease one badge to print!');
            }
            $PrintSessionId = Str::uuid();
            foreach ($request->badge_id as $value) {
                CompanyMember::find($value)->update(['print_session_id' => $PrintSessionId]);
            }
            $query = CompanyMember::where('print_session_id', $PrintSessionId);
        } else {
            $query = CompanyMember::where('company_members.member_id', $id);
        }

        $badges = $query
            ->leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
            ->leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
            ->orderBy('badge_status', 'ASC')
            ->select([
                'company_members.member_id',
                'company_members.first_name',
                'company_members.last_name',
                'company_members.associateCompany',
                'companies.companyName',
                'badge_groups.group_name',
                'badge_groups.group_background_color',
                'badge_groups.text_color',
            ])
            ->get();

        if ($badges->isEmpty()) {
            return back()->with('fail', 'No badges found for your search, please try search new badges again!');
        }

        # ── Mark as PRINTED + optional SMS ──────────────────────────────
        $notify = DefaultSettings::first()->print_notification == 1;
        foreach ($badges as $b) {
            CompanyMember::find($b->member_id)->update(['badge_status' => 'PRINTED']);
            if ($notify) {
                // $this->BadgePrintingNotification($b->member_id);
            }
        }

        # ── Build payload for the A4 sheet ──────────────────────────────
        # QR is rendered client-side by qr-code-styling (inline <script>
        # per badge — synchronous, no shared-state race).
        $payload = $badges->map(fn($b) => [
            'id'         => $b->member_id,
            'name'       => strtoupper(trim(($b->first_name ?? '') . ' ' . ($b->last_name ?? ''))),
            'company'    => strtoupper(trim($b->associateCompany ?? $b->companyName ?? '')),
            'group'      => strtoupper($b->group_name ?? 'EXHIBITOR'),
            'stripColor' => $b->group_background_color ?? '#13487e',
            'textColor'  => $b->text_color ?? '#ffffff',
        ])->values()->all();

        # layout supports: '6' (6-up), '4' (4-up 88x120), '4xl' (4-up 86x127)
        $layout = (string) $request->input('layout', '6');
        if (!in_array($layout, ['6', '4', '4xl'], true)) {
            $layout = '6';
        }
        $perPage = ($layout === '6') ? 6 : 4;

        return view('Badges.NewBadgePrintSheet', [
            'badges'  => $payload,
            'perPage' => $perPage,
            'layout'  => $layout,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('Validate Badges', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }

        // $badges=CompanyMember::leftJoin('companies')
        // ->where('member_id',$request->badge_id)->get();

        $badges = CompanyMember::leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
            ->leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
            ->leftJoin('events', 'company_members.event', '=', 'events.event_id')->where('event_status', 1)
            ->where('member_id', $request->badge_id)->get();

        $badge = CompanyMember::find($request->badge_id);
        if ($badge) {
            $company_id = $badge->company_id;
            $event = $badge->event;
        } else {
            $company_id = '';
            $event = '';
        }
        $invoice = advice_notes::where(['event' => $event, 'company_id' => $company_id, 'advice_note_status' => 'APPROVED'])->where('balance', '>', 0)->get();

        $balance = 0;
        foreach ($invoice as $key => $value) {
            $balance = $value->balance + $balance;
        }
        $id = $request->badge_id;
        return view('Validations.BadgesValidations', compact('badges', 'balance', 'id'));
    }



    #******************** BADGE PRINT NOTIFICATION *****************#
    function BadgePrintingNotification($badge_id)
    {
        if (config('global.APP_STATUS') == 1) {
            $badge = CompanyMember::where('badge_status', 'UNPRINTED')->where('member_id', $badge_id)->first();
            if ($badge && Str::length($badge->phone_number) == 12) {
                $sms_content = "Your badge is ready for collection!
Please visit TanTrade HQ to pick up your badge.
Download TanTrade App for more information.";
                $sent_on = date('Y-m-d');
                BulkSms::create([
                    'sms_id' => Str::uuid(),
                    'phone_number' => $badge->phone_number,
                    'sms_body' => $sms_content,
                    'sms_sent_on' => $sent_on,
                    'customer_name' => $badge->first_name . ' ' . $badge->last_name,
                ]);
            }
        }
    }
    #*************************** END BADGE PRINTING NOTIFICATION ****************#


    public function backPage(Request $request)
    {
        if ($request->user()->cannot('Print Exhibitor Badges', Role::class)) {
            return back()->with('fail', 'You do not have permission to perform this Action');
        }
        #BackPage
        #PVCBackPage
        $badges = PDF::loadView('Badges.PVCBackPage')->setPaper('A4', 'portrait');
        $badges->render();
        return $badges->stream("Exhibitor_Badges_" . date('d_m'));
    }
}
