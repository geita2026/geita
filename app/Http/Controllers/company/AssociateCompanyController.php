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
        if ($request->user()->cannot('view-associate-companies', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $associates = companies::where('associate_company', '!=', Null)->get();
        return view('company.associate_companies', compact('associates'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->user()->cannot('view-associate-companies', Role::class)) {
            return back()->with('fail', config('Messages.401'));
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
        if ($request->user()->cannot('view-associate-companies', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $sectors = sectors::orderBy('sectorName', 'ASC')
            ->get(['sectorID', 'sectorName']);

        $usedBadges=CompanyMember::where('event',session('liveEvent'))
        ->where('company_id',$id)->count();

        $associates = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->where('associate_company', $id)->where('status', 1)
            ->get([
                'companies.companyID',
                'companyName',
                'companyEmail',
                'companyPhone',
                'companyCategory',
                'companyLocation',
                'companySector',
                'sectorName',
                'registrationNo',
                'companyTel',
                'companyAddress',
                'originality',
                'registrationID',
                'associate_company'
            ]);
        $associateWithAllocations =  companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->leftJoin('badges_allocations', 'companies.companyID', '=', 'badges_allocations.company_id')
            ->where('associate_company', $id)->where('status', 1)
            ->where('event_id', session('liveEvent'))
            ->get([
                'companies.companyID',
                'event_id',
                'badges_allocated',
                'badges_used',
                'free_badges'
            ]);
        $totalChildBadges = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->leftJoin('badges_allocations', 'companies.companyID', '=', 'badges_allocations.company_id')
            ->where('associate_company', $id)->where('status', 1)
            ->where('event_id', session('liveEvent'))
            ->sum('free_badges');

        $main_company = '';
        $company_id = $id;
        $motherCompany = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->leftJoin('badges_allocations', 'companies.companyID', '=', 'badges_allocations.company_id')
            ->where('event_id', session('liveEvent'))
            ->where('company_id', $id)
            ->get(['companyID', 'companyName', 'badges_allocated', 'badges_used','free_badges']);
        session(['allowedAllocation' => 0]);

        foreach ($motherCompany as $key => $value) {
            $usedBadges = $totalChildBadges + $usedBadges;
            session(['allowedAllocation' => $value->badges_allocated - $usedBadges]);
        }
        $countries = Country::get(['country_name', 'country_short_code']);
        $sectors = sectors::get(['sectorID', 'sectorName']);

        return view('company.associate_companies', compact('associates', 'main_company', 'company_id', 'countries', 'sectors', 'associateWithAllocations'));
    }

    public function update(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('allocate-associates-badges', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $validator = Validator::make($request->all(), [
                'parentCompany' => 'required|exists:companies,companyID',
                'allocatedBadges' => 'required|numeric|min:1|max:100',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }

            $parentBadges = BadgesAllocation::where('company_id', $request->parentCompany)
                ->where('event_id', session('liveEvent'))->first();

            $allocation = BadgesAllocation::where('company_id', $id)
                ->where('event_id', session('liveEvent'))->first(); // Get Company Allocations

            $parentUsedBadges = CompanyMember::where('company_id', $request->parentCompany)
                ->Where('event', session('liveEvent'))->count(); // Total Badges Used

            $childBadges = companies::leftJoin('badges_allocations', 'companies.companyID', '=', 'badges_allocations.company_id')
                ->where('badges_allocations.event_id', session('liveEvent'))
                ->where('associate_company', $request->parentCompany)
                ->sum('free_badges');

            $totalUsedBadges = $parentUsedBadges + $childBadges + $request->allocatedBadges;
            if ($totalUsedBadges > $parentBadges->badges_allocated + $parentBadges->free_badges) {
                return back()->with('fail', 'The parent company has less or zero badge Allocation!');
            }

            if (!$allocation) {
                $allocationId = Str::uuid();
                BadgesAllocation::create([
                    'badge_allocation_id' => $allocationId,
                    'company_id' => $id,
                    'event_id' => session('liveEvent'),
                    'free_badges' => $request->allocatedBadges,
                ]);
                $parentBadges->update([
                    'badges_used' => $request->allocatedBadges + $parentBadges->badges_used,
                ]);
            } else {
                $allocation->update([
                    'free_badges' => ($request->allocatedBadges + $allocation->free_badges),
                ]);
                $parentBadges->update([
                    'badges_used' => $request->allocatedBadges + $parentBadges->badges_used,
                ]);
            }
            return back()->with('success', 'New allocation saved successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
