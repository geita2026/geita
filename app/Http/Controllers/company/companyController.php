<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\companies;
use App\Models\Companies\CompanyAdmin;
use Illuminate\Support\Facades\Auth;
use App\Models\sectors;
use Illuminate\Support\Str;
use App\Models\company_details;
use App\Models\exhibition\exhibitors;
use App\Models\Notifications\BulkSms;
use App\Models\setting\Country;
use App\Models\setting\DefaultSettings;
use App\Models\User;
use App\Traits\UserRegistrationTrait;
use App\Traits\NotificationTrait;
use App\Traits\IntergrationTraits;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Traits\TsrnSupplier;

class companyController extends Controller
{
    use UserRegistrationTrait;
    use NotificationTrait;
    use IntergrationTraits;
    use TsrnSupplier;
    // actions which run in backgrounds
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

        if ($request->user()->cannot('Company Registration', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $request->session()->forget('TSRN');
        $companies = companies::orderBy('registrationDate', 'DESC')->where('status', 1)->paginate(config('global.DATA_LIMIT'), ['companyID', 'companyName', 'companyEmail', 'companyPhone', 'registrationID', 'lastUpdatedOn']);
        if (Auth::user()->account_type == 2) {
            $companies = companies::orderBy('registrationDate', 'DESC')
                ->leftJoin('company_admins', 'companies.companyID', '=', 'company_admins.companyId')
                ->where('adminId', Auth::user()->user_uuid)
                ->where('status', 1)->paginate(config('global.DATA_LIMIT'), ['companies.companyID', 'companyName', 'companyEmail', 'companyPhone', 'registrationID', 'lastUpdatedOn']);
        }

        return view('company.company', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('Company Registration', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $countries = Country::get(['country_name', 'country_short_code']);
        $sectors = sectors::get(['sectorID', 'sectorName']);
        $myCompany = [];
        return view('company.company-registration', compact('sectors', 'countries', 'myCompany'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        if ($request->user()->cannot('Company Registration', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        $request->validate([
            'company_name' => 'required|unique:companies,companyName',
            'company_phone' => 'required|min:10|max:13',
            'company_sector' => 'required',
            'country' => 'required',
            'company_category' => 'required',
        ]);

        try {
            // primary company data
            $company_id = Str::uuid();
            $email = $request->company_email;
            $phone_number = $request->company_phone;
            $firstName = $request->company_name;
            $company_category = $request->company_category;
            $company_location = $request->country;
            $company_sector = $request->company_sector;
            $status = 1;
            $associateCompany = $request->associate_company;
            $reg_id = $this->getTsrn();
            $region = $request->region;
            // company other details
            $registrationNo = $request->registrationNo;
            $tinNo = $request->tinNo;
            $companyTele = $request->companyTele;
            $companyAddress = $request->companyAddress;
            $website = $request->website;
            $originality = $request->originality;
            $personalName = $request->personalName;
            $personalPhone = $request->personalPhone;
            $personalEmail = $request->personalEmail;
            $personalTitle = $request->personalTitle;
            $detailID = Str::uuid();

            //
            if (empty($request->company_email)) {
                $email = strtolower(str_replace(' ', '', $request->company_name . "@gmail.com")); // removing space for email comp
            }
            // company registration methode
            $this->CompanyRegistration($company_id, $reg_id, $firstName, $email, $phone_number, $company_category, $company_location, $company_sector, $status, $region, $associateCompany);
            // company registration
            $admins = CompanyAdmin::where('adminId', Auth::user()->user_uuid)
                ->where('companyId', $company_id)->first();
            if (!$admins and Auth::user()->account_type == 2) {
                CompanyAdmin::create([
                    'referenceId' => Str::uuid(),
                    'adminId' => Auth::user()->user_uuid,
                    'companyId' => $company_id,
                ]);
            }
            # End company details Registration
            // $this->registrationNotification($reg_id, $phone_number, $firstName);
            return back()->with('success', 'Associate Company Account Created successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong, Check company phone number and try Agai ' . $th->getMessage());
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
        if ($request->user()->cannot('Company Registration', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        $sectors = sectors::orderBy('sectorName', 'ASC')
            ->get(['sectorID', 'sectorName']);
        $companies = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
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
                'registrationID'
            ])
            ->where('companyID', $id);
        return view('company.company-details', compact('companies', 'sectors'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->user()->cannot('Company Registration', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //

        $sectors = sectors::orderBy('sectorName', 'ASC')
            ->get(['sectorID', 'sectorName']);

        $companies = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
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
                'isAcceptedTerms',
                'companyLogo'
            ])
            ->where('companyID', $id);
        $countries = Country::get(['country_name']);
        $regStatus = 0;
        $regStatus = User::where('user_uuid', $id)->first();
        if ($regStatus) {
            $regStatus = $regStatus->isAcceptedTerms;
        }
        return view('company.edit-company', compact('companies', 'sectors', 'countries', 'regStatus'));
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
        if ($request->user()->cannot('Company Registration', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        try {
            $validator = Validator::make($request->all(), [
                'company_email' => 'required|email',
                'company_name' => 'required',
                'company_phone' => 'required|digits:12',
                'company_location' => 'required',
                'company_sector' => 'required',
                'company_category' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            $email = $request->company_email;
            $firstName = $request->company_name;
            $phone_number = $request->company_phone;
            $company_category = $request->company_category;
            $company_location = $request->company_location;
            $company_sector = $request->company_sector;
            $region = $request->region;
            $associateCompany = $request->associate_company;

            $company = companies::find($id);
            $imageLogo = $company->companyLogo;
            if ($request->companyLogo) {
                $request->validate([
                    'companyLogo' => 'file|mimes:png,jpg,jpeg'
                ]);
                File::delete(public_path('profile_images/' . $imageLogo));
                $imageLogo = date('Ym') . $id . '.' . $request->companyLogo->extension();
                $request->companyLogo->move(public_path('profile_images'), $imageLogo);
            }

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
                'companyLogo' => $imageLogo,
                'isAcceptedTerms' => 1,
            ]);
            # updating company details table
            $company_id = $id;
            if (empty($request->company_email)) {
                $email = strtolower(str_replace(' ', '', $request->company_name . "@gmail.com")); # removing space for email comp
            }
            #Update Billing Too
            if (config('global.APP_STATUS') == 1) {
                $this->customer_registration_api($company_id, $firstName, $phone_number, $email); # register customer to billing
            }
            #Updating Billing with new information account for this company

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
            } else {
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
            }
            $this->MackAsUpdatedCompany($company_id); //
            if ($request->notifyUser == 1) {
                $reg_id = $company->registrationID;
                $this->registrationNotification($reg_id, $phone_number, $firstName);
            }
            return back()->with('success', 'Company informations updated successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail', config('Messages.500')) . $th->getMessage();
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
        if ($request->user()->cannot('Delete Company', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        try {
            $company = companies::find($id);
            if ($company) {
                $company->update(['status' => 0]);
                return redirect()->route('companies.index')->with('success', config('Messages.200'));
            }
            return back()->with('fail', config('Messages.500'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail', config('Messages.500'));
        }
    }


    // company registration
    public function CompanyRegistration($company_id, $reg_id, $firstName, $email, $phone_number, $company_category, $company_location, $company_sector, $status, $region, $associateCompany)
    {
        companies::create([
            'companyID' => $company_id,
            'registrationID' => $reg_id,
            'companyName' => $firstName,
            'companyEmail' => $email,
            'companyPhone' => $phone_number,
            'companyCategory' => $company_category,
            'companyLocation' => $company_location,
            'companySector' => $company_sector,
            'region' => $region,
            'status' => $status,
            'associate_company' => $associateCompany,
        ]);
        // end primary company data registration
    }

    // end

    // Company Details registrations
    public function CompanyDetailsRegistrations($detailID, $company_id, $registrationNo, $tinNo, $companyTele, $companyAddress, $website, $originality, $personalName, $personalPhone, $personalEmail, $personalTitle)
    {
        company_details::create([
            'companyDetailID' => $detailID,
            'companyID' => $company_id,
            'registrationNo' => $registrationNo,
            'tinNo' => $tinNo,
            'companyTel' => $companyTele,
            'companyAddress' => $companyAddress,
            'website' => $website,
            'originality' => $originality,
            'personalName' => $personalName,
            'personalPhone' => $personalPhone,
            'personalEmail' => $personalEmail,
            'personalTitle' => $personalTitle,
        ]);
    }

    // end company registrations


    // Mack as updated company
    function MackAsUpdatedCompany($company_id)
    {
        $user = User::where('user_uuid', $company_id)->first();

        if ($user) {
            $user->update([
                'profile_updated' => 1,
                'isAcceptedTerms' => 1,
            ]);
        }
    }

    public function registrationNotification($reg_id, $phone_number, $firstName)
    {
        if (DefaultSettings::first()->print_notification == 1) {
            $sms_content = 'We are pleased to confirm that your registration with SAMS was successful.
Your TSRN is:  ' . $reg_id . '
Register your personal Account via, https://tradefair.tantrade.go.tz/registration';
            BulkSms::create([
                'sms_id' => Str::uuid(),
                'phone_number' => $phone_number,
                'sms_body' => $sms_content,
                'sms_sent_on' => date('Y-m-d'),
                'customer_name' => $firstName,
            ]);
        }
    }



    public function NotiFyForGudging(Request $request){
        try {
            $exhibitors=exhibitors::leftJoin('companies','companies.companyID','=','exhibitors.exhibitor_id')
            ->leftJoin('');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
