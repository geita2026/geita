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
use Illuminate\Support\Facades\DB;

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
        if ($request->user()->cannot('VIEW_BADGES_LIST', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_BADGES_LIST'));
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
        if ($request->user()->cannot('CREATE_EXHIBITORS_BADGES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('CREATE_EXHIBITORS_BADGES'));
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
            if ($request->user()->cannot('CREATE_MEMBER_BADGES', Role::class)) {
                return response()->json(['status' => 2, 'fail' => $this->permissionDeniedMessage('CREATE_MEMBER_BADGES')]);
            }

            $validator = Validator::make($request->all(), [
                'company_id' => 'required|exists:companies,companyID',
                'associateCompany' => 'nullable|exists:companies,companyID',
                'first_name' => 'required',
                'last_name' => 'required',
                'phone_number' => 'required',
                'member_image' => 'required|mimes:png,jpg,jpeg',
            ]);

            if ($validator->fails()) {
                return back()->with('fail', implode(', ', $validator->getMessageBag()->all()));
            }

            $companyId = $request->company_id;
            $associateCompany = $request->associateCompany ?: null;

            if ($associateCompany) {
                $child = companies::where('companyID', $associateCompany)
                    ->where('associate_company', $companyId)->first();
                if (!$child) {
                    return back()->with('fail', 'Selected associate company does not belong to this parent company.');
                }
            }

            $badgeId = (string) Str::uuid();
            $imageName = $badgeId . '_' . date('Y') . '.' . $request->member_image->extension();
            $directory = public_path('Members/');
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            @ini_set('memory_limit', '512M');
            Image::make($request->member_image)->resize(200, 200)->save($directory . $imageName);
            $imageLink = rtrim(config('app.url'), '/') . '/public/Members/' . $imageName;

            try {
                DB::transaction(function () use ($request, $companyId, $associateCompany, $badgeId, $imageLink) {
                    $available = $this->BadgeChecking($companyId, $associateCompany);
                    if ($companyId != config('global.COMPANY_EXCEPTION') && $available <= 0) {
                        throw new \RuntimeException('All badges have been used up!!');
                    }

                    CompanyMember::create([
                        'member_id' => $badgeId,
                        'company_id' => $companyId,
                        'first_name' => $request->first_name,
                        'middle_name' => $request->middle_name,
                        'last_name' => $request->last_name,
                        'member_group' => $request->member_group,
                        'member_image' => $imageLink,
                        'badge_status' => 'UNPRINTED',
                        'phone_number' => $request->phone_number,
                        'event' => session('liveEvent'),
                        'scan_count' => 0,
                        'associateCompany' => $associateCompany,
                    ]);

                    $this->UpdateUsedBadges(session('liveEvent'), $companyId, $associateCompany);
                });
            } catch (\Throwable $e) {
                File::delete($directory . $imageName);
                throw $e;
            }

            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            Log::error('Badge image upload (store) failed: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
            return back()->with('fail', $th instanceof \RuntimeException ? $th->getMessage() : config('Messages.500'));
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
        if ($request->user()->cannot('VIEW_MEMBER_BADGES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_MEMBER_BADGES'));
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
        if ($request->user()->cannot('VIEW_MEMBER_BADGES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_MEMBER_BADGES'));
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
        if ($request->source == 1) {
            if ($request->user()->cannot('BLOCK_EXHIBITOR_BADGE', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('BLOCK_EXHIBITOR_BADGE'));
            }
            $member = CompanyMember::find($id);
            if (!$member) return back()->with('fail', config('Messages.404'));
            $member->update(['badge_status' => 'BLOCKED', 'blocked_by' => Auth::user()->user_uuid]);
            return back()->with('success', 'Member Badge blocked successfully!');
        }

        if ($request->source == 2) {
            if ($request->user()->cannot('UN_BLOCK_MEMBER_BADGES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('UN_BLOCK_MEMBER_BADGES'));
            }
            $member = CompanyMember::find($id);
            if (!$member) return back()->with('fail', config('Messages.404'));
            $member->update(['badge_status' => 'UNBLOCKED', 'unblocked_by' => Auth::user()->user_uuid]);
            return back()->with('success', 'Member badges unblocked successfully!');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'member_image' => 'nullable|mimes:png,jpg,jpeg',
            'company_id' => 'required|exists:companies,companyID',
            'associateCompany' => 'nullable|exists:companies,companyID',
        ]);
        if ($validator->fails()) {
            return back()->with('fail', implode(', ', $validator->getMessageBag()->all()));
        }

        $member = CompanyMember::find($id);
        if (!$member) return back()->with('fail', config('Messages.404'));

        $newCompanyId = $request->company_id;
        $newAssociate = $request->associateCompany ?: null;
        if ($newAssociate) {
            $child = companies::where('companyID', $newAssociate)
                ->where('associate_company', $newCompanyId)->first();
            if (!$child) return back()->with('fail', 'Selected associate company does not belong to this parent company.');
        }

        $newImagePath = null;
        $oldImagePath = $member->member_image
            ? public_path(str_replace(rtrim(config('app.url'), '/') . '/public/', '', $member->member_image))
            : null;
        $imageLink = $member->member_image;
        try {
            if ($request->hasFile('member_image')) {
                $imageName = $id . '_' . (string) Str::uuid() . '.' . $request->member_image->extension();
                $directory = public_path('Members/');
                if (!File::isDirectory($directory)) File::makeDirectory($directory, 0755, true);
                @ini_set('memory_limit', '512M');
                Image::make($request->member_image)->resize(200, 200)->save($directory . $imageName);
                $newImagePath = $directory . $imageName;
                $imageLink = rtrim(config('app.url'), '/') . '/public/Members/' . $imageName;
            }

            DB::transaction(function () use ($member, $request, $newCompanyId, $newAssociate, $imageLink) {
                $oldCompanyId = $member->company_id;
                $oldAssociate = $member->associateCompany ?: null;
                $oldEvent = $member->event;

                if ($oldCompanyId != $newCompanyId || $oldAssociate != $newAssociate) {
                    $available = $this->BadgeChecking($newCompanyId, $newAssociate);
                    if ($newCompanyId != config('global.COMPANY_EXCEPTION') && $available < 1) {
                        throw new \RuntimeException('The selected company has no available badge allocation.');
                    }
                }

                $member->update([
                    'company_id' => $newCompanyId,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'member_group' => $request->member_group,
                    'member_image' => $imageLink,
                    'phone_number' => $request->phone_number,
                    'badge_status' => 'UNPRINTED',
                    'associateCompany' => $newAssociate,
                    'backupStatus' => 0,
                ]);

                $this->UpdateUsedBadges($oldEvent, $oldCompanyId, $oldAssociate);
                $this->UpdateUsedBadges($oldEvent, $newCompanyId, $newAssociate);
            });

            if ($newImagePath && $oldImagePath && $oldImagePath !== $newImagePath && File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
            return back()->with('success', $request->first_name . ' ' . $request->last_name . ' Details updated successfully!');
        } catch (\Throwable $th) {
            if ($newImagePath && File::exists($newImagePath)) File::delete($newImagePath);
            Log::error('Badge update failed: ' . $th->getMessage());
            return back()->with('fail', 'Something went wrong!');
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
        if ($request->user()->cannot('DELETE_EXHIBITOR_BADGE', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('DELETE_EXHIBITOR_BADGE'));
        }

        try {
            $badge = CompanyMember::find($id);
            if (!$badge) return back()->with('fail', config('Messages.404'));

            $companyId = $badge->company_id;
            $associateCompany = $badge->associateCompany ?: null;
            $eventId = $badge->event;
            $imageName = $badge->member_image
                ? str_replace(rtrim(config('app.url'), '/') . '/public/', '', $badge->member_image)
                : null;

            DB::transaction(function () use ($badge, $companyId, $associateCompany, $eventId) {
                $badge->delete();
                $this->UpdateUsedBadges($eventId, $companyId, $associateCompany);
            });

            if ($imageName) {
                $path = public_path($imageName);
                if (File::exists($path)) File::delete($path);
            }

            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            Log::error('Badge delete failed: ' . $th->getMessage());
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function BadgeChecking($id, $associateCompany = null)
    {
        $eventId = session('liveEvent');

        // Child allocation belongs to the child. Parent allocation is not charged again.
        if ($associateCompany) {
            $allocation = BadgesAllocation::where('company_id', $associateCompany)
                ->where('event_id', $eventId)->first();

            if (!$allocation) {
                return 0;
            }

            $used = CompanyMember::where('company_id', $id)
                ->where('associateCompany', $associateCompany)
                ->where('event', $eventId)
                ->where('badge_status', '!=', 'ARCHIVED')
                ->count();

            $allocation->update(['badges_used' => $used]);
            return (int) $allocation->badges_allocated + (int) $allocation->free_badges - $used;
        }

        $allocation = BadgesAllocation::where('company_id', $id)
            ->where('event_id', $eventId)->first();

        $totalBookedBadges = particulars::leftJoin('advice_notes', 'particulars.advice_note_id', '=', 'advice_notes.advice_note_id')
            ->where('advice_notes.event', $eventId)
            ->where('particular_name', 'LIKE', '%badges%')
            ->where('advice_note_status', 'APPROVED')
            ->where('advice_notes.company_id', $id)
            ->sum('particular_quantity');

        $companyUsedBadges = CompanyMember::where('company_id', $id)
            ->where('event', $eventId)
            ->where(function ($query) {
                $query->whereNull('associateCompany')->orWhere('associateCompany', '');
            })
            ->where('badge_status', '!=', 'ARCHIVED')
            ->count();

        $childIds = companies::where('associate_company', $id)->pluck('companyID');
        $childAllocatedBadges = BadgesAllocation::whereIn('company_id', $childIds)
            ->where('event_id', $eventId)->sum('free_badges');

        $totalUsedBadges = $companyUsedBadges + (int) $childAllocatedBadges;
        $totalAllocatedBadges = (int) $totalBookedBadges + (int) ($allocation->free_badges ?? 0);

        if ($allocation) {
            $allocation->update([
                'badges_allocated' => (int) $totalBookedBadges,
                'badges_used' => $totalUsedBadges,
            ]);
        } else {
            $allocation = BadgesAllocation::create([
                'badge_allocation_id' => (string) Str::uuid(),
                'company_id' => $id,
                'event_id' => $eventId,
                'badges_allocated' => (int) $totalBookedBadges,
                'badges_used' => $totalUsedBadges,
                'free_badges' => 0,
            ]);
        }

        $balance = advice_notes::where('company_id', $id)
            ->where('event', $eventId)
            ->where('advice_note_status', 'APPROVED')
            ->sum('balance');

        session([
            'balance' => $balance,
            'companyID' => $id,
            'usedBadges' => $totalUsedBadges,
            'totalAllocatedBadges' => $totalAllocatedBadges,
            'availableBadges' => $totalAllocatedBadges - $totalUsedBadges,
        ]);

        return session('availableBadges');
    }


    private function CheckBadgeAvailability($id, $associateCompany = null)
    {
        return $this->BadgeChecking($id, $associateCompany);
    }
}
