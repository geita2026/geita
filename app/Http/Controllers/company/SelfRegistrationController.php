<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\company_details;
use App\Models\sectors;
use App\Models\setting\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\UserRegistrationTrait;

class SelfRegistrationController extends Controller
{
    use UserRegistrationTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sectors = sectors::orderBy('sectorName', 'ASC')
        ->get(['sectorID', 'sectorName']);

    $companies = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
        ->leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
        ->get([
            'companies.companyID', 'companyName', 'companyEmail', 'companyPhone', 'companyCategory', 'companyLocation', 'companySector', 'sectorName', 'companyDetailID', 'registrationNo', 'tinNo',
            'companyTel', 'companyAddress', 'website', 'originality', 'personalName', 'personalPhone',
            'personalEmail', 'personalTitle', 'registrationID', 'region'
        ])
        ->where('companyID', $id);
    $countries = Country::get(['country_name']);
    $regStatus=User::where('user_uuid',$id)->first()->isAcceptedTerms;
    return view('company.SelfRegistrationUpdateInfo', compact('companies', 'sectors', 'countries','regStatus'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $request->validate([
            'company_phone' => 'required|digits:12',
            'company_location' => 'required',
            'company_sector' => 'required',
            'company_category' => 'required',
            'company_name'=>'required',
            'companyAddress'=>'required'
        ]);
        try {
            $email = $request->company_email;
            $firstName = $request->company_name;
            $phone_number = $request->company_phone;
            $company_category = $request->company_category;
            $company_location = $request->company_location;
            $company_sector = $request->company_sector;
            $region = $request->region;
            $associateCompany = $request->associate_company;
            $company = companies::find($id);

            $company->update([
                'companyName' => $firstName,
                'companyEmail' => $email,
                'companyPhone' => $phone_number,
                'companyCategory' => $company_category,
                'companyLocation' => $company_location,
                'companySector' => $company_sector,
                'region' => $region,
                'status' => 1,
                'associate_company' => $associateCompany,
            ]);
            // updating company details table
            $company_id = $id;
            if (empty($request->company_email)) {
                $email = strtolower(str_replace(' ', '', $request->company_name . "@gmail.com")); // removing space for email comp
            }

            #Update Billing Too
               if (config('global.APP_STATUS')==1) {
                $this->customer_registration_api($company_id,$firstName,$phone_number,$email); // register customer to billing
               }
            #Updating Billing with new information account for this company
            $this->UpdateUser($id, $firstName, $phone_number, $email); // update user also when company is updated.

            if (company_details::where('companyID', $id)->exists()) {
                $details = company_details::get()->where('companyID', $id);
                foreach ($details as $value) {

                    company_details::find($value->companyDetailID)->update([
                        'registrationNo' => $request->registrationNo,
                        'tinNo' => $request->tinNo,
                        'companyTel' => $request->companyTele,
                        'companyAddress' => $request->companyAddress,
                        'website' => $request->website,
                        'originality' => $request->originality,
                        'personalName' => $request->personalName,
                        'personalPhone' => $request->personalPhone,
                        'personalEmail' => $request->personalEmail,
                        'personalTitle' => $request->personalTitle,
                    ]);
                }
                //    end of updating details table
            } else {
                // company details registration
                $detailID = Str::uuid();
                company_details::create([
                    'companyDetailID' => $detailID,
                    'companyID' => $id,
                    'registrationNo' => $request->registrationNo,
                    'tinNo' => $request->tinNo,
                    'companyTel' => $request->companyTele,
                    'companyAddress' => $request->companyAddress,
                    'website' => $request->website,
                    'originality' => $request->originality,
                    'personalName' => $request->personalName,
                    'personalPhone' => $request->personalPhone,
                    'personalEmail' => $request->personalEmail,
                    'personalTitle' => $request->personalTitle,
                ]);
                // end company details registration

            }

            $this->MackAsUpdatedCompany($id); //

            return back()->with('success', 'Company informations updated successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail', 'something went wrong!').$th->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    function MackAsUpdatedCompany($id)
    {
            User::where('user_uuid',$id)->first()->update([
                'profile_updated' => 1,
                'isAcceptedTerms'=>1,
            ]);
       
    }
}
