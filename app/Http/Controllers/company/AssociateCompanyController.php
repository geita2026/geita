<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\ExhibitionBadges\BadgesAllocation;
use App\Models\ExhibitionBadges\CompanyMember;
use App\Models\sectors;
use App\Models\setting\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AssociateCompanyController extends Controller
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
        if ($request->user()->cannot('VIEW_ASSOCIATE_COMPANY_SUBMENU', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_ASSOCIATE_COMPANY_SUBMENU'));
        }
        $associates = companies::where('associate_company', '!=', Null)->get();
        return view('company.associate_companies', [
            'associates' => $associates,
            'countries' => Country::orderBy('country_name')->get(['country_name', 'country_short_code']),
            'sectors' => sectors::orderBy('sectorName')->get(['sectorID', 'sectorName']),
            'associateWithAllocations' => collect(),
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->user()->cannot('VIEW_ASSOCIATE_COMPANY_SUBMENU', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_ASSOCIATE_COMPANY_SUBMENU'));
        }
        $sectors = sectors::orderBy('sectorName', 'ASC')
            ->get(['sectorID', 'sectorName']);

        $associates = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
            ->where('companies.companyID', $id)
            ->get([
                'companies.companyID',
                'companyName',
                'companyEmail',
                'companyPhone',
                'companyCategory',
                'companyLocation',
                'companySector',
                'sectorName',
                'companyDetailID',
                'registrationNo',
                'tinNo',
                'companyTel',
                'companyAddress',
                'website',
                'originality',
                'personalName',
                'personalPhone',
                'personalEmail',
                'personalTitle',
                'registrationID',
                'region',
                'associate_company'
            ]);

        $main_company = '';
        $company_id = $id;
        $countries = Country::get(['country_name', 'country_short_code']);
        $sectors = sectors::get(['sectorID', 'sectorName']);
        return view('company.EditAssociateCompany', compact('associates', 'main_company', 'company_id', 'countries', 'sectors'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('VIEW_ASSOCIATE_COMPANY_SUBMENU', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_ASSOCIATE_COMPANY_SUBMENU'));
        }

        $sectors = sectors::orderBy('sectorName', 'ASC')->get(['sectorID', 'sectorName']);

        $usedBadges = CompanyMember::where('event', session('liveEvent'))
            ->where('company_id', $id)
            ->where(function ($query) {
                $query->whereNull('associateCompany')->orWhere('associateCompany', '');
            })
            ->where('badge_status', '!=', 'ARCHIVED')
            ->count();

        $associates = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->where('associate_company', $id)->where('status', 1)
            ->get([
                'companies.companyID', 'companyName', 'companyEmail', 'companyPhone',
                'companyCategory', 'companyLocation', 'companySector', 'sectorName',
                'registrationNo', 'companyTel', 'companyAddress', 'originality',
                'registrationID', 'associate_company',
            ]);

        $associateWithAllocations = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->leftJoin('badges_allocations', function ($join) {
                $join->on('companies.companyID', '=', 'badges_allocations.company_id')
                    ->where('badges_allocations.event_id', '=', session('liveEvent'));
            })
            ->where('associate_company', $id)->where('status', 1)
            ->get(['companies.companyID', 'event_id', 'badges_allocated', 'badges_used', 'free_badges']);

        $childIds = companies::where('associate_company', $id)->pluck('companyID');
        $totalChildBadges = BadgesAllocation::whereIn('company_id', $childIds)
            ->where('event_id', session('liveEvent'))->sum('free_badges');

        $parentAllocation = BadgesAllocation::where('company_id', $id)
            ->where('event_id', session('liveEvent'))->first();

        $allowedAllocation = $parentAllocation
            ? max(0, (int) $parentAllocation->badges_allocated + (int) $parentAllocation->free_badges
                - $usedBadges - (int) $totalChildBadges)
            : 0;

        session([
            'allowedAllocation' => $allowedAllocation,
            'companyID' => $id,
        ]);

        $main_company = '';
        $company_id = $id;
        $countries = Country::orderBy('country_name')->get(['country_name', 'country_short_code']);

        return view('company.associate_companies', compact(
            'associates', 'main_company', 'company_id', 'countries', 'sectors', 'associateWithAllocations'
        ));
    }

    public function update(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('ALLOCATE_ASSOCIATE_COMPANY', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('ALLOCATE_ASSOCIATE_COMPANY'));
            }

            $validator = Validator::make($request->all(), [
                'parentCompany' => 'required|exists:companies,companyID',
                'allocatedBadges' => 'required|integer|min:1|max:100',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }

            $eventId = session('liveEvent');
            $amount = (int) $request->allocatedBadges;

            DB::transaction(function () use ($request, $id, $eventId, $amount) {
                $child = companies::where('companyID', $id)
                    ->where('associate_company', $request->parentCompany)
                    ->lockForUpdate()->first();

                if (!$child) {
                    throw new \RuntimeException('Selected company is not an associate of this parent company.');
                }

                $parent = BadgesAllocation::where('company_id', $request->parentCompany)
                    ->where('event_id', $eventId)
                    ->lockForUpdate()->first();

                if (!$parent) {
                    throw new \RuntimeException('The parent company has no badge allocation for this event.');
                }

                $childAllocation = BadgesAllocation::where('company_id', $id)
                    ->where('event_id', $eventId)
                    ->lockForUpdate()->first();

                $parentOwnMembers = CompanyMember::where('company_id', $request->parentCompany)
                    ->where('event', $eventId)
                    ->where(function ($query) {
                        $query->whereNull('associateCompany')->orWhere('associateCompany', '');
                    })
                    ->where('badge_status', '!=', 'ARCHIVED')
                    ->count();

                $childIds = companies::where('associate_company', $request->parentCompany)->pluck('companyID');
                $alreadyReserved = BadgesAllocation::whereIn('company_id', $childIds)
                    ->where('event_id', $eventId)->sum('free_badges');

                $parentCapacity = (int) $parent->badges_allocated + (int) $parent->free_badges;
                if ($parentOwnMembers + (int) $alreadyReserved + $amount > $parentCapacity) {
                    throw new \RuntimeException('The parent company does not have enough unallocated badges.');
                }

                if (!$childAllocation) {
                    $childAllocation = BadgesAllocation::create([
                        'badge_allocation_id' => (string) Str::uuid(),
                        'company_id' => $id,
                        'event_id' => $eventId,
                        'badges_allocated' => 0,
                        'badges_used' => 0,
                        'free_badges' => $amount,
                    ]);
                } else {
                    $childAllocation->increment('free_badges', $amount);
                }

                // Parent badges_used means direct usage + badges reserved for all children.
                $parent->update([
                    'badges_used' => $parentOwnMembers + (int) $alreadyReserved + $amount,
                ]);
            });

            return back()->with('success', 'New allocation saved successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', $th instanceof \RuntimeException
                ? $th->getMessage()
                : config('Messages.500'));
        }
    }
}
