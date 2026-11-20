<?php

namespace App\Http\Controllers\Badges;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\advice_note\particulars;
use App\Models\exhibition\exhibitors;
use App\Models\settings\events;
use Illuminate\Http\Request;
use App\Models\companies;
use App\Models\ExhibitionBadges\BadgeGroup;
use App\Models\ExhibitionBadges\BadgesAllocation;
use App\Models\ExhibitionBadges\CompanyMember;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Traits\BadgesTraits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;

class BadgesController extends Controller
{
    use BadgesTraits;
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
        // $badges=CompanyMember::get(['member_image','member_id']);
        // foreach ($badges as $value) {
        //     $imageName=str_replace(env('APP_URL').'/public/Members/','',$value->member_image);
        //     $path=env('APP_URL').'/public/Members/'.$imageName;
        //     CompanyMember::find($value->member_id)->update([
        //     'member_image'=>$path,
        //    ]);
        // }
        if ($request->user()->cannot('View Badges List', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $companies = companies::orderBy('registrationDate', 'DESC')->where('status', 1)->paginate(config('global.DATA_LIMIT'), ['companyID', 'companyName', 'companyEmail', 'companyPhone', 'registrationID']);
        if (Auth::user()->account_type == 2) {
            $companies = companies::orderBy('registrationDate', 'DESC')
                ->leftJoin('company_admins', 'companies.companyID', '=', 'company_admins.companyId')
                ->where('adminId', Auth::user()->user_uuid)
                ->where('status', 1)->paginate(config('global.DATA_LIMIT'), ['companies.companyID', 'companyName', 'companyEmail', 'companyPhone', 'registrationID']);
        }
        return view('Badges.exhibitor_companies', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('Create Exhibitors Badges', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $exhibitors = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', 'companies.companyID')
            ->where('exhibitor_event', $request->event_id)
            ->get(); // return all exhibitors at specific event.
        return view('Badges.generate_badges', compact('exhibitors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if ($request->user()->cannot('Create Member Badges', Role::class)) {
                return response()->json(['status' => 2, 'fail' => 'You dont have permission to perform this Action']);
            }

            $validator = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'phone_number' => 'required',
                    'member_image' => 'required|mimes:png,jpg,jpeg',
                ]
            );
            if ($validator->fails()) {
                return back()->with('fail', implode(', ', $validator->getMessageBag()->all()));
            }
            $badgeId = Str::uuid();
            $company_id = $request->company_id;
            $associateCompany = Null;
            if ($request->associateCompany != null) {
                $associateCompany = $request->associateCompany;
                $company_id = companies::where('companyID', $associateCompany)->first()->associate_company;
            }
            ############### CHECK BADGE AVAILABILITY ###############
            $availabeBadges = $this->BadgeChecking($company_id, session('liveEvent')); // Checking Company badge Availability.
            session(['availabeBadges' => $availabeBadges]);
            if ($company_id != config('global.COMPANY_EXCEPTION')) {
                if ($availabeBadges <= 0) {
                    return back()->with('fail', 'All badges have been used up!!');
                }
            }
            $image_name = $badgeId . "_" . date('Y') . "." . $request->member_image->extension(); # if available creates new sign name
            $imageLink = "https://tradefair.tantrade.go.tz". '/public/Members/' . $image_name;
            CompanyMember::create([
                'member_id' => $badgeId,
                'company_id' => $company_id,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'member_group' => $request->member_group,
                'member_image' => $imageLink,
                'badge_status' => 'UNPRINTED',
                'phone_number' => $request->phone_number,
                'event' => session('liveEvent'), // event ID
                'scan_count' => 0,
                'associateCompany' => $associateCompany,
            ]);

            #---- RESIZE IMAGE -----#
            @ini_set('memory_limit', '512M'); // large @2x/@4x PNGs need headroom for GD to decode before resize
            $path = public_path('Members/');
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0755, true);
            }
            $image = Image::make($request->member_image)->resize(200, 200);
            $image->save($path . $image_name);
            #---- RESIZE IMAGE -----#
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            Log::error('Badge image upload (store) failed: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
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
            return back()->with('fail', config('Messages.401'));
        }

        $events = events::get('event_id', $id); // return all active events
        $companies = companies::limit(25)->get();
        $total_companies = companies::count();

        return view('Badges.exhibitor_companies', compact('events', 'companies', 'total_companies'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->user()->cannot('View Member Badges', Role::class)) {
            return back()->with('fail', config('global.RESTRICTED_ACCESS'));
        }
        session(['companyID' => $id]);
        $status = $this->BadgeChecking($id); // Check Badge Availability
        $badges = BadgesAllocation::where('company_id', $id)->where('event_id', session('liveEvent'))->get();
        $invoice = advice_notes::where('company_id', $id)->where('event', session('liveEvent'))
            ->where('advice_note_status', 3)->where('balance', '>', 1)->get();

        if (Auth::user()->account_type == 1) {
            $groups = BadgeGroup::orderBy('group_name', 'ASC')->get();
        } elseif (Auth::user()->account_type == 2) {
            $groups = BadgeGroup::where('group_name', 'EXHIBITOR')->get();
        }
        $members = CompanyMember::orderBy('first_name', 'ASC')->leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
            ->leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
            ->where('event', session('liveEvent'))->where('company_id', $id)->paginate(10);
        if ($request->search) {
            $search=$request->search;
            $members = CompanyMember::orderBy('first_name', 'ASC')->leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
                ->leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
                ->where(function ($query) use ($search) {
                    $query->orWhere('companyName', 'LIKE', '%' . $search . '%');
                    $query->orWhere('first_name', 'LIKE', '%' . $search . '%');
                    $query->orWhere('last_name', 'LIKE', '%' . $search . '%');
                    $query->orWhere('phone_number', 'LIKE', '%' . $search . '%');
                })
                ->where('event', session('liveEvent'))
                ->where('company_id', $id)
                ->paginate(50);
        }

        $companies = companies::where('companyID', $id)->get();

        $balanceInvoice = advice_notes::where('company_id', $id)
            ->where('advice_note_status', 'APPROVED')
            ->where('balance', '>', 0)->get();
        $previousBadges = CompanyMember::where('company_id', $id)
            ->where('event', '!=', session('liveEvent'))
            ->where('badge_status', '!=', 'ARCHIVED')->count();

        $associates = companies::where('associate_company', $id)->get(['companyID', 'companyName']);
        return view('Badges.create_new_badge', compact('groups', 'members', 'companies', 'badges', 'balanceInvoice', 'associates', 'previousBadges'));
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
        # ----- CONTROL BADGE OWNERS
        if ($request->source == 1) // block Member
        {
            if ($request->user()->cannot('Block Member Badges', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            CompanyMember::find($id)->update([
                'badge_status' => 'BLOCKED',
                'blocked_by' => Auth::user()->user_uuid,
            ]);
            return back()->with('success', 'Member Badge blocked successfully!');
        } elseif ($request->source == 2) // unblock Member
        {
            if ($request->user()->cannot('UnBlock Member Badges', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            CompanyMember::find($id)->update([
                'badge_status' => 'UNBLOCKED',
                'unblocked_by' => Auth::user()->user_uuid,
            ]);
            return back()->with('success', 'Member badges unblocked sucessfully!');
        }
        # ------ END CONTROL BADGE ----- #

        # --- UPDATE MEMBER BADGE ---- #
        else {
            try {
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'member_image' => 'mimes:png,jpg,jpeg'
                ]);
                if ($validator->fails()) {
                    return back()->with('fail', implode(', ', $validator->getMessageBag()->all()));
                }
                $member = CompanyMember::find($id);
                $imageLink = $member->member_image;
                if (!empty($request->member_image)) {
                    $badge = CompanyMember::find($id);
                    $imageName = str_replace(config('app.url') . '/public/', '', $badge->member_image);
                    $path = public_path($imageName);
                    File::delete($path);
                    $image_name = $id . "." . $request->member_image->extension(); # if available creates new sign name
                    $imageLink = "https://tradefair.tantrade.go.tz". '/public/Members/' . $image_name;

                    #---- RESIZE IMAGE -----#
                    @ini_set('memory_limit', '512M'); // large @2x/@4x PNGs need headroom for GD to decode before resize
                    $path = public_path('Members/');
                    if (!File::isDirectory($path)) {
                        File::makeDirectory($path, 0755, true);
                    }
                    $image = Image::make($request->member_image)->resize(200, 200);
                    $image->save($path . $image_name);
                    #---- RESIZE IMAGE -----#
                }
                $associateCompany = Null;
                $company_id = $request->company_id;
                if ($request->associateCompany != null) {
                    $associateCompany = $request->associateCompany;
                    $company_id = $company_id = companies::where('companyID', $associateCompany)->first()->associate_company;
                }
                $member->update([
                    'company_id' => $company_id,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'member_group' => $request->member_group,
                    'member_image' => $imageLink,
                    'phone_number' => $request->phone_number,
                    'badge_status' => 'UNPRINTED',
                    'associateCompany' => $associateCompany,
                    'backupStatus' => 0,
                ]);
                return back()->with('success', $request->first_name . ' ' . $request->last_name . ' Details updated successfully!');
            } catch (\Throwable $th) {
                Log::error('Badge image upload (update) failed: ' . $th->getMessage(), [
                    'file' => $th->getFile(),
                    'line' => $th->getLine(),
                ]);
                return back()->with('fail', 'Something went wrong!');
            }
        }
        # ----- END BADGE -------- #

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('Delete Member Badges', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        try {
            $this->DeleteBadge($id); // delete badge from Allocation
            $badge = CompanyMember::find($id);
            $imageName = str_replace(env('APP_URL') . '/public/', '', $badge->member_image);
            $path = public_path($imageName);
            File::delete($path);
            $badge->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function BadgeChecking($id)
    {
        $allocation = BadgesAllocation::where('company_id', $id)->where('event_id', session('liveEvent'))->first();
        $totalBookedBadges = particulars::leftJoin('advice_notes', 'particulars.advice_note_id', '=', 'advice_notes.advice_note_id')
            ->where('event', session('liveEvent'))
            ->where('particular_name', 'LIKE', '%badges%')->where('advice_note_status', 'APPROVED')
            ->where('advice_notes.company_id', $id)->sum('particular_quantity');

        $companyUsedBadges = CompanyMember::where('company_id', $id)
            ->where('event', session('liveEvent'))->count(); # consumed by Company
        $childBadges = BadgesAllocation::whereIn('company_id', companies::where('associate_company', $id)->get(['companyID']))
            ->where('event_id', session('liveEvent'))->sum('free_badges'); # Given to child Company
        $totalUsedBadges = $companyUsedBadges + $childBadges; # Total Used Badges
        if ($allocation) {
            $totalAllocatedBadges = $totalBookedBadges + $allocation->free_badges; # Calculates total Allocated badges
            $allocation->update([
                'badges_allocated' => $totalBookedBadges,
                'badges_used' => $totalUsedBadges,
            ]);
        }
        // If allocation does not exist
        else {
            $totalAllocatedBadges = $totalBookedBadges;
            $allocationId = Str::uuid();
            BadgesAllocation::create([
                'badge_allocation_id' => $allocationId,
                'company_id' => $id,
                'event_id' => session('liveEvent'),
                'badges_allocated' => $totalAllocatedBadges,
            ]);
            $allocation = BadgesAllocation::find($allocationId);
        }
        #*******************END BADGES*********************#

        $balance = advice_notes::where('company_id', $id)->where('event', session('liveEvent'))
            ->where('advice_note_status', 'APPROVED')->sum('balance');

        session(['balance' => $balance]);
        session(['companyID' => $id]);
        session(['usedBadges' => $totalUsedBadges]); // Used Badges
        session(['totalAllocatedBadges' => $totalAllocatedBadges]); // Total Allocated Badges.
        session(['availableBadges' => session('totalAllocatedBadges') - session('usedBadges')]);
        return session('availableBadges');
    }


    private function CheckBadgeAvailability($id)
    {
        $availableBadges = 0;
        $usedBadges = CompanyMember::where('company_id', $id)
            ->Where('event', session('liveEvent'))->count(); // Total Badges Used

        $allocation = BadgesAllocation::where('company_id', $id)
            ->where('event_id', session('liveEvent'))->first(); // Get Company Allocations

        if ($allocation) {
            $totalAllocatedBadges = ($allocation->badges_allocated); // Total Badges Allocated
            $allocation->update(['used_badges' => $usedBadges]); // Update Badge Allocations
            $availableBadges = $totalAllocatedBadges - $usedBadges;
        }

        return $availableBadges;
    }
}
