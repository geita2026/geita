<?php

namespace App\Http\Controllers\Badges;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\companies;
use App\Models\ExhibitionBadges\BadgeGroup;
use App\Models\ExhibitionBadges\BadgesAllocation;
use App\Models\ExhibitionBadges\CompanyMember;
use App\Models\settings\events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Traits\BadgesTraits;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReUseBadgesController extends Controller
{
    use BadgesTraits;

    /**
     * Display a listing of companies with reusable members.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('REUSE_BADGES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('REUSE_BADGES'));
        }

        try {
            $liveEvent = session('liveEvent');

            $companies = companies::select('companies.companyID', 'companies.companyName')
                ->join('company_members', 'company_members.company_id', '=', 'companies.companyID')
                ->where('company_members.event', '!=', $liveEvent)
                ->where('company_members.badge_status', '!=', 'ARCHIVED')
                ->groupBy('companies.companyID', 'companies.companyName')
                ->orderBy('companies.companyName', 'ASC')
                ->paginate(15);

            $reusableCounts = CompanyMember::selectRaw('company_id, COUNT(*) as total')
                ->where('event', '!=', $liveEvent)
                ->where('badge_status', '!=', 'ARCHIVED')
                ->groupBy('company_id')
                ->pluck('total', 'company_id');

            return view('Badges.reuse_badges', compact('companies', 'reusableCounts'));
        } catch (\Throwable $th) {
            Log::error('ReUseBadges index failed: ' . $th->getMessage());
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->route('reuse_badges.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('REUSE_BADGES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('REUSE_BADGES'));
        }

        $request->validate([
            'company_id' => 'required|exists:companies,companyID',
            'associateCompany' => 'nullable|exists:companies,companyID',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'member_group' => 'nullable',
            'phone_number' => 'nullable|string|max:20',
        ]);

        try {
            $companyId = $request->company_id;
            $associateCompany = $request->associateCompany ?: null;
            if ($associateCompany && !companies::where('companyID', $associateCompany)
                ->where('associate_company', $companyId)->exists()) {
                return back()->with('fail', 'Selected associate company does not belong to this parent company.');
            }

            DB::transaction(function () use ($request, $companyId, $associateCompany) {
                $available = $this->CheckBadgeAvailability($companyId, $associateCompany);
                if ($companyId != config('global.COMPANY_EXCEPTION') && $available < 1) {
                    throw new \RuntimeException('No badge allocation is available for this company.');
                }

                CompanyMember::create([
                    'member_id' => (string) Str::uuid(),
                    'company_id' => $companyId,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'member_group' => $request->member_group,
                    'member_image' => $request->member_image,
                    'badge_status' => 'UNPRINTED',
                    'event' => session('liveEvent'),
                    'phone_number' => $request->phone_number,
                    'associateCompany' => $associateCompany,
                ]);

                $this->UpdateUsedBadges(session('liveEvent'), $companyId, $associateCompany);
            });

            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            Log::error('ReUseBadges store failed: ' . $th->getMessage());
            return back()->with('fail', $th instanceof \RuntimeException ? $th->getMessage() : config('Messages.500'));
        }
    }

    /**
     * Display the specified company's reusable members.
     *
     * @param  int  $id  Company ID
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('VIEW_MEMBER_BADGES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_MEMBER_BADGES'));
        }

        try {
            $event = events::where('event_status', 1)->first();

            if (!$event) {
                return back()->with('fail', 'No active event found.');
            }

            $company = companies::where('companyID', $id)->first();

            if (!$company) {
                return back()->with('fail', config('Messages.404'));
            }

            $liveEvent = session('liveEvent');

            $badges  = BadgesAllocation::where('company_id', $id)
                ->where('event_id', $event->event_id)
                ->get();

            $groups = BadgeGroup::orderBy('group_name', 'ASC')->get();

            $members = CompanyMember::select('company_members.*', 'companies.companyName', 'badge_groups.group_name')
                ->leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
                ->leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
                ->where('company_members.company_id', $id)
                ->where('company_members.badge_status', '!=', 'ARCHIVED')
                ->where('company_members.event', '!=', $liveEvent)
                ->orderBy('company_members.first_name', 'ASC')
                ->paginate(10);

            $existBadges = CompanyMember::where('event', '!=', $liveEvent)
                ->where('badge_status', '!=', 'ARCHIVED')
                ->where('company_id', $id)
                ->count();

            $balanceInvoice = advice_notes::where('company_id', $id)
                ->where('event', $event->event_id)
                ->where('advice_note_status', 3)
                ->where('balance', '>', 0)
                ->get();

            $badge     = BadgesAllocation::where('company_id', $id)
                ->where('event_id', $event->event_id)
                ->first();
            $freeBadges = $badge ? $badge->free_badges : 0;

            $allBadge = CompanyMember::select('company_members.*', 'companies.companyName', 'badge_groups.group_name')
                ->leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
                ->leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
                ->where('company_members.company_id', $id)
                ->where('company_members.badge_status', '!=', 'ARCHIVED')
                ->orderBy('company_members.member_added_at', 'DESC')
                ->get();

            session(['companyID' => $id]);

            $associates = companies::where('associate_company', $id)
                ->get(['companyID', 'companyName']);

            return view('Badges.PreviousMembers', compact(
                'groups',
                'members',
                'company',
                'badges',
                'balanceInvoice',
                'freeBadges',
                'associates',
                'allBadge',
                'existBadges'
            ));
        } catch (\Throwable $th) {
            Log::error('ReUseBadges show failed: ' . $th->getMessage());
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Archive a member's image backup.
     *
     * @param  int  $id  Member ID
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $response = Http::timeout(15)->get(
                config('global.BACKUP_BASE_URL') . date('d_m_y') . '/' . $id
            );

            if ($response->successful() && ($response['status'] ?? 0) == 1) {
                return back()->with('success', $response['message']);
            }

            return back()->with('fail', 'Something went wrong!');
        } catch (\Throwable $th) {
            Log::error('ReUseBadges edit failed: ' . $th->getMessage());
            return back()->with('fail', 'Sorry, Backup Failed for this image!');
        }
    }

    /**
     * Move a member from a previous event into the live event.
     *
     * @param  int  $id  Member ID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('VIEW_MEMBER_BADGES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_MEMBER_BADGES'));
        }

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
        ]);

        try {
            $member = CompanyMember::find($id);
            if (!$member) return back()->with('fail', config('Messages.404'));

            $companyId = $member->company_id;
            $associateCompany = $member->associateCompany ?: null;

            DB::transaction(function () use ($member, $companyId, $associateCompany, $request) {
                $available = $this->CheckBadgeAvailability($companyId, $associateCompany);
                if ($companyId != config('global.COMPANY_EXCEPTION') && $available < 1) {
                    throw new \RuntimeException('No badge allocation is available. Please request additional badges.');
                }

                CompanyMember::create([
                    'member_id' => (string) Str::uuid(),
                    'company_id' => $companyId,
                    'first_name' => $request->first_name,
                    'middle_name' => $member->middle_name,
                    'last_name' => $request->last_name,
                    'member_group' => $member->member_group,
                    'member_image' => $member->member_image,
                    'phone_number' => $member->phone_number,
                    'badge_status' => 'UNPRINTED',
                    'print_session_id' => null,
                    'search_session_id' => null,
                    'scan_count' => 0,
                    'event' => session('liveEvent'),
                    'associateCompany' => $associateCompany,
                ]);

                $member->update(['badge_status' => 'ARCHIVED']);
                $this->UpdateUsedBadges($member->event, $companyId, $associateCompany);
                if ($member->event != session('liveEvent')) {
                    $this->UpdateUsedBadges(session('liveEvent'), $companyId, $associateCompany);
                }
            });

            return back()->with('success', $member->first_name . ' ' . $member->last_name . ' moved to current event');
        } catch (\Throwable $th) {
            Log::error('ReUseBadges update failed: ' . $th->getMessage());
            return back()->with('fail', $th instanceof \RuntimeException ? $th->getMessage() : config('Messages.500'));
        }
    }

    /**
     * Archive a member (soft delete) and remove their image.
     *
     * @param  int  $id  Member ID
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('VIEW_MEMBER_BADGES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_MEMBER_BADGES'));
        }

        try {
            $badge = CompanyMember::find($id);

            if (!$badge) {
                return back()->with('fail', config('Messages.404'));
            }

            $companyId = $badge->company_id;
            $associateCompany = $badge->associateCompany ?: null;
            $eventId = $badge->event;
            $imageName = $badge->member_image
                ? str_replace(rtrim(config('app.url'), '/') . '/public/', '', $badge->member_image)
                : null;

            DB::transaction(function () use ($badge, $companyId, $associateCompany, $eventId) {
                $badge->update(['badge_status' => 'ARCHIVED']);
                $this->UpdateUsedBadges($eventId, $companyId, $associateCompany);
            });

            if ($imageName) {
                $path = public_path($imageName);
                if (File::exists($path)) File::delete($path);
            }

            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            Log::error('ReUseBadges destroy failed: ' . $th->getMessage());
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Get available badge slots for a company in the live event.
     *
     * @param  int  $companyId
     * @return int
     */
    private function CheckBadgeAvailability($companyId, $associateCompany = null): int
    {
        $eventId = session('liveEvent');

        if ($associateCompany) {
            $allocation = BadgesAllocation::where('company_id', $associateCompany)
                ->where('event_id', $eventId)
                ->lockForUpdate()
                ->first();

            if (!$allocation) return 0;

            $used = CompanyMember::where('company_id', $companyId)
                ->where('associateCompany', $associateCompany)
                ->where('event', $eventId)
                ->where('badge_status', '!=', 'ARCHIVED')
                ->count();

            $allocation->update(['badges_used' => $used]);
            return (int) $allocation->badges_allocated + (int) $allocation->free_badges - $used;
        }

        $allocation = BadgesAllocation::where('company_id', $companyId)
            ->where('event_id', $eventId)
            ->lockForUpdate()
            ->first();

        if (!$allocation) return 0;

        $ownMembers = CompanyMember::where('company_id', $companyId)
            ->where('event', $eventId)
            ->where(function ($query) {
                $query->whereNull('associateCompany')->orWhere('associateCompany', '');
            })
            ->where('badge_status', '!=', 'ARCHIVED')
            ->count();

        $childIds = companies::where('associate_company', $companyId)->pluck('companyID');
        $childReserved = BadgesAllocation::whereIn('company_id', $childIds)
            ->where('event_id', $eventId)->sum('free_badges');

        $used = $ownMembers + (int) $childReserved;
        $allocation->update(['badges_used' => $used]);

        return (int) $allocation->badges_allocated + (int) $allocation->free_badges - $used;
    }
}
