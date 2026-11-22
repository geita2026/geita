<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\company_details;
use Illuminate\Http\Request;
use App\Models\sectors;
use App\Models\setting\Country;
use App\Models\settings\events;
use App\Traits\ExhibitorTrait;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class companyListController extends Controller
{
    use ExhibitorTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if($request->user()->cannot('VIEW_COMPANY_PROFILE_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_COMPANY_PROFILE_SUBMENU'));
        }
        $company_list = $this->companyProfileQuery()
            ->addSelect('companies.companyID', 'companies.companyName', 'companies.companyPhone', 'companies.companyEmail')
            ->orderBy('companies.companyName')
            ->limit(300)
            ->get();

        return view('company.company_list',compact('company_list'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {

        if($request->user()->cannot('VIEW_COMPANY_PROFILE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_COMPANY_PROFILE'));
        }

        return $this->companyProfileView($id);
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
        if($request->user()->cannot('UPDATE_COMPANY_PROFILE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_COMPANY_PROFILE'));
        }
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_phone' => ['required', 'string', 'max:30'],
            'person_name' => ['required', 'string', 'max:255'],
            'contact_person_email' => ['nullable', 'email', 'max:255'],
            'contact_person_phone' => ['required', 'string', 'max:30'],
        ]);

        $contact = [
            'personalName' => $data['person_name'],
            'personalEmail' => $data['contact_person_email'] ?? null,
            'personalPhone' => $data['contact_person_phone'],
        ];

        DB::transaction(function () use ($id, $data, $contact) {
            $company = companies::whereKey($id)->lockForUpdate()->firstOrFail();
            $company->update([
                'companyName' => $data['company_name'],
                'companyEmail' => $data['company_email'] ?? null,
                'companyPhone' => $data['company_phone'],
            ] + $contact);

            // Older profiles can also have contact fields in the details table.
            company_details::where('companyID', $company->companyID)->update($contact);
        });

        return back()->with('success', 'Company details updated successfully!');
    }


    private function companyProfileQuery()
    {
        return companies::leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
            ->select('company_details.companyDetailID')
            ->selectRaw('COALESCE(company_details.personalName, companies.personalName) AS personalName')
            ->selectRaw('COALESCE(company_details.personalEmail, companies.personalEmail) AS personalEmail')
            ->selectRaw('COALESCE(company_details.personalPhone, companies.personalPhone) AS personalPhone');
    }

    private function companyProfileView($id)
    {
        $companies = $this->companyProfileQuery()
            ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->where('companies.companyID', $id)
            ->addSelect([
                'companies.companyID', 'companies.companyName', 'companies.companyEmail',
                'companies.companyPhone', 'companies.companyCategory', 'companies.companyLocation',
                'companies.companySector', 'sectors.sectorName', 'company_details.companyTel',
                'companies.registrationID', 'companies.updated_at',
            ])
            ->selectRaw('COALESCE(company_details.registrationNo, companies.registrationNo) AS registrationNo')
            ->selectRaw('COALESCE(company_details.tinNo, companies.tinNo) AS tinNo')
            ->selectRaw('COALESCE(company_details.companyAddress, companies.companyAddress) AS companyAddress')
            ->selectRaw('COALESCE(company_details.website, companies.website) AS website')
            ->selectRaw('COALESCE(company_details.originality, companies.originality) AS originality')
            ->selectRaw('COALESCE(company_details.personalTitle, companies.personalTitle) AS personalTitle')
            ->get();

        $countries = Country::get(['country_name']);
        $sectors = sectors::orderBy('sectorName')->get(['sectorID', 'sectorName']);

        return view('company.company-profile', compact('companies', 'countries', 'sectors'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {

        if($request->user()->cannot('VIEW_COMPANY_PROFILE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_COMPANY_PROFILE'));
        }
        try
        {
        $event_id=events::where('event_status',1)->first()->event_id;
        $this->RegisterCompanyAsExhibitor($id,$event_id); // register this company as exhibitor
        $companies=companies::where('associate_company',$id)->get();
        foreach ($companies as $value)
        {
            $id=$value->companyID;
            $this->RegisterCompanyAsExhibitor($id,$event_id); // register this company as exhibitor
        }
        return back()->with('success','Company made an exhibitor, successfully!');
        }
        catch (\Throwable $th)
        {
        return back()->with('fail','Something went wrong!'.$th->getMessage());
        }

    }


}
