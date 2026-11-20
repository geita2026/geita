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
use Intervention\Image\Facades\Image;

class ReUseBadgesController extends Controller
{
    use BadgesTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Reuse Badges', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        try {
            CompanyMember::create([
                'member_id' => Str::uuid(),
                'company_id' => $request['company_id'],
                'first_name' => $request['first_name'],
                'middle_name' => $request['middle_name'],
                'last_name' => $request['last_name'],
                'member_group' => $request['member_group'],
                'member_image' => $request['member_image'],
                'badge_status' => 'UNPRINTED',
                'event' => session('liveEvent'),
                'phone_number' => $request['phone_number'],
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('View Member Badges', Role::class)) {
            return back()->with('fail', 'Not allowed to access this resource!');
        }
        $events = events::where('event_status', 1)->first();
        $badges = BadgesAllocation::where('company_id', $id)->where('event_id', $events->event_id)->get();
        $groups = BadgeGroup::orderBy('group_name', 'ASC')->get();

        $members = CompanyMember::orderBy('first_name', 'ASC')->leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
            ->leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
            ->where('company_id', $id)->where('badge_status', '!=', 'ARCHIVED')
            ->where('event', '!=', session('liveEvent'))->paginate(10);

        $existBadges = CompanyMember::where('event', '!=', session('liveEvent'))
            ->where('badge_status', '!=', 'ARCHIVED')
            ->where('company_id', $id)->count();

        $companies = companies::where('companyID', $id)->get();

        // $members=CompanyMember::paginate(2);

        $balanceInvoice = advice_notes::where('company_id', $id)->where('event', $events->event_id)
            ->where('advice_note_status', 3)->where('balance', '>', 0)->get();

        $badge = BadgesAllocation::where('company_id', $id)->where('event_id', $events->event_id)->first();
        if ($badge) {
            $freeBadges = $badge->free_badges;
        } else {
            $freeBadges = 0;
        }

        $allBadge = CompanyMember::orderBy('member_added_at', 'DESC')->leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
            ->leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
            ->where('badge_status','!=','ARCHIVED')
            ->where('company_id', $id)->get();
        session(['companyID' => $id]);
        $associates = companies::where('associate_company', $id)->get(['companyID', 'companyName']);

        return view('Badges.PreviousMembers', compact('groups', 'members', 'companies', 'badges', 'balanceInvoice', 'freeBadges', 'associates', 'allBadge'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $response = Http::get(config('global.BACKUP_BASE_URL') . date('d_m_y') . '/' . $id);
            if ($response['status'] == 1) {
                return back()->with('success', $response['message']);
            }
            return back()->with('fail', 'Something went wrong!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Soory, Backup Failed for this image!');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if ($request->user()->cannot('View Member Badges', Role::class)) {
            return back()->with('fail', 'Not allowed to perform this Activity!');
        }
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        try {
            $member = CompanyMember::find($id);
            $id = $member->company_id;
            if ($id != config('global.COMPANY_EXCEPTION')) {
                $availableBadges = $this->CheckBadgeAvailability($id);
                if ($availableBadges < 1) {
                    return back()->with('fail', $availableBadges . ' Number of slotes available, Please request new badges to proceed!');
                }
            }
            CompanyMember::create([
                'member_id' => Str::uuid(),
                'company_id'=>$member->company_id,
                'first_name' => $member->first_name,
                'middle_name' => $member->middle_name,
                'last_name' => $member->last_name,
                'member_group' => $member->member_group,
                'member_image' => $member->member_image,
                'phone_number' => $member->phone_number,
                'badge_status' => 'UNPRINTED',
                'print_session_id' => Null,
                'search_session_id' => Null,
                'scan_count'=>0,
                'event' => session('liveEvent'),
            ]);
            $this->UpdateUsedBadges(session('liveEvent'), $request->company_id); // update used badges
            $member->update([
                'badge_status'=>'ARCHIVED'
            ]);
            return back()->with('success', $request->first_name . ' ' . $request->last_name . ' Moved To Current Event');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('View Member Badges', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $badge = CompanyMember::find($id);
            if ($badge) {
                $imageName = str_replace(env('APP_URL') . '/public/', '', $badge->member_image);
                $path = public_path($imageName);
                File::delete($path);
                $badge->update([
                    'badge_status' => 'ARCHIVED' #
                ]);
                return back()->with('success', config('Messages.200'));
            } else {
                return back()->with('fail', config('Messages.404'));
            }
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }



    private function CheckBadgeAvailability($id)
    {
        $usedBadges = CompanyMember::where('company_id', $id)
            ->Where('event', session('liveEvent'))->count(); // Total Badges Used

        $allocation = BadgesAllocation::where('company_id', $id)
            ->where('event_id', session('liveEvent'))->first(); // Get Company Allocations

        $allocation->update(['used_badges' => $usedBadges]); // Update Badge Allocations

        $totalAllocatedBadges = ($allocation->badges_allocated + $allocation->free_badges); // Total Badges Allocated


        $availableBadges = $totalAllocatedBadges - $usedBadges;
        return $availableBadges;
    }
}
