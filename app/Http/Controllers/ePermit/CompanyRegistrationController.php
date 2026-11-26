<?php

namespace App\Http\Controllers\ePermit;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\Companies\CompanyAdmin;
use App\Models\sectors;
use App\Models\setting\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Traits\ThirdPartIntergrationTraits;
use App\Traits\IntergrationTraits;
use App\Traits\TsrnSupplier;
use Illuminate\Support\Facades\Http;

class CompanyRegistrationController extends Controller
{
    use ThirdPartIntergrationTraits;
    use IntergrationTraits;
    use TsrnSupplier;


    public function myCompanyList(Request $request)
    {
        try {
            if ($request->user()->cannot('CREATE_COMPANY', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('CREATE_COMPANY'));
            }
            $myCompanies = CompanyAdmin::leftJoin('companies', 'company_admins.companyId', '=', 'companies.companyID')
                ->where('adminId', Auth::user()->user_uuid)
                ->get([
                    'companyName',
                    'companies.companyId',
                    'companyEmail',
                    'companyPhone',
                    'status',
                    'registrationID'
                ]);

            return view('ePermit.MyCompanyList', compact('myCompanies'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function CompanyProfile(Request $request, $companyId)
    {
        try {

            if ($request->user()->cannot('CREATE_COMPANY', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('CREATE_COMPANY'));
            }
            if ($request->TSRN) {
                $localCompany = companies::where('registrationID', $request->TSRN)->first();
                if (!$localCompany) {
                    return back()->with('fail', 'TSRN not found. If your TSRN was recently issued, please register using your TIN number instead.');
                }
                session(['tinStatus' => $localCompany->tinStatus]);
                $exists = CompanyAdmin::where('companyId', $localCompany->companyID)
                    ->where('adminId', Auth::user()->user_uuid)->first();
                if (!$exists && Auth::user()->account_type == 2) {
                    CompanyAdmin::create([
                        'referenceId' => Str::uuid(),
                        'adminId' => Auth::user()->user_uuid,
                        'companyId' => $localCompany->companyID,
                    ]);
                }
                $myCompany = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
                    ->where('registrationID', $request->TSRN)->get();
            } elseif ($request->TIN) {
                $myCompany = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
                    ->where('tinNo', $request->TIN)->get();
                $exists = companies::where('tinNo', $request->TIN)->first();
                if ($exists) {
                    session(['tinStatus' => $exists->tinStatus]);
                    $exist = CompanyAdmin::where('companyId', $exists->companyID)
                        ->where('adminId', Auth::user()->user_uuid)->first();
                    if (!$exist && Auth::user()->account_type == 2) {
                        CompanyAdmin::create([
                            'referenceId' => Str::uuid(),
                            'adminId' => Auth::user()->user_uuid,
                            'companyId' => $exists->companyID,
                        ]);
                    }
                } else {
                    $response = Http::withToken(config('global.MARKET_INFO_TOKEN'))->post(config('global.MARKET_INFO_BASE_URL') . 'tra_info', [
                        'tinNo' => $request->TIN,
                    ]);
                    foreach ($response['data'] as  $value) {
                        $companyID = Str::uuid();
                        companies::create([
                            'companyID' => $companyID,
                            'registrationID' => $value['TSRN'],
                            'companyName' => $value['traderName'],
                            'companyEmail' => $value['traderEmail'],
                            'companyPhone' => $value['traderPhone'],
                            'companyAddress' => $value['traderAddress'] . ', ' . $value['traderStreet'],
                            'tinNo' => $value['TIN'],
                            'tinStatus' => 'VERIFIED',
                            'status' => 1,
                            'registrationNo' => $value['BRN'],
                            'companyCategory' => $value['businessType'],
                            'brnStatus' => 'VERIFIED'
                        ]);

                        $myCompany = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
                            ->where('registrationID', $value['TSRN'])->get();
                        session(['tinStatus' => 'VERIFIED']);
                        $exist = CompanyAdmin::where('companyId', $companyID)
                            ->where('adminId', Auth::user()->user_uuid)->first();
                        if (!$exist && Auth::user()->account_type == 2) {
                            CompanyAdmin::create([
                                'referenceId' => Str::uuid(),
                                'adminId' => Auth::user()->user_uuid,
                                'companyId' => $companyID,
                            ]);
                        }
                    }
                }
            } elseif ($companyId == date('dmd')) {
                return back()->with('fail', 'Please, Enter the required number to proceed!');
            } else {
                session(['tinStatus' => companies::where('registrationID', $companyId)->first()->tinStatus]);
                $myCompany = companies::leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
                    ->where('registrationID', $companyId)->get();
            }
            $sectors = sectors::get(['sectorID', 'sectorName']);
            $countries = Country::orderByRaw("CASE WHEN country_name = 'Tanzania' THEN 0 ELSE 1 END")->get(['country_name', 'country_short_code']);
            return view('ePermit.CompanyProfile', compact('myCompany', 'countries', 'sectors'));
        } catch (\Throwable $th) {
            return back()->with('fail', 'submited Number is incorect, please provide correct Number to proceed!');
        }
    }

    public function registerCompany(Request $request)
    {

        try {
            if ($request->user()->cannot('CREATE_COMPANY', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('CREATE_COMPANY'));
            }
            $validator = Validator::make($request->all(), [
                'TIN' => 'nullable',
                'company_email' => 'required|email',
                'company_name' => 'required',
                'company_phone' => 'required|min:12|numeric',
                'company_address' => 'required',
                'originality' => 'required',
                'hasOtherCompanies' => 'required|in:YES,NO',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            $company = companies::where('registrationID', $request->TSRN)->first();

            if ($company) {
                # ------ Register as My Company ------ #
                $admin = CompanyAdmin::where('companyId', $company->companyID)->where('adminId', Auth::user()->user_uuid)->first();
                if (!$admin && Auth::user()->account_type == 2) {
                    CompanyAdmin::create([
                        'referenceId' => Str::uuid(),
                        'adminId' => Auth::user()->user_uuid,
                        'companyId' => $company->companyID
                    ]);
                }
                # ----- End Registration -------- #

                $logoName = $company->companyLogo;
                if ($request->companyLogo) {
                    $validator = Validator::make($request->all(), [
                        'companyLogo' => 'file|mimes:png,jpg,pdf,jpeg'
                    ]);
                    if ($validator->fails()) {
                        return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
                    }
                    $logoName = str_replace(' ', '', $request->company_name) . date('sihdmY') . '.' . $request->companyLogo->extension();
                    File::delete(public_path('profile_images/') . $company->companyLogo);
                    $request->companyLogo->move(public_path('profile_images'), $logoName); // Delete Existing File
                }
                $company->update([
                    'companyName' => $request->company_name,
                    'companyEmail' => $request->company_email,
                    'companyPhone' => $request->company_phone,
                    'companySector' => $request->company_sector,
                    'companyCategory' => $request->company_category,
                    'companyLocation' => $request->companyLocation,
                    'isAcceptedTerms' => 1,
                    'companyLogo' => $logoName,
                    'website' => $request->website,
                    'personalName' => $request->personalName,
                    'personalEmail' => $request->personalEmail,
                    'personalPhone' => $request->personalPhone,
                    'personalTitle' => $request->personalTitle,
                    'companyAddress' => $request->company_address,
                    'registrationNo' => $request->registrationNo,
                    'tinNo' => $request->TIN,
                    'status' => 1,
                    'referenceModule' => 3,
                    'originality' => $request->originality,
                    'hasOtherCompanies' => $request->hasOtherCompanies == 'YES' ? true : false,
                    'lastUpdatedOn' => date('Y')
                ]);
                session(['companyId' => $company->companyID]);

                return redirect()->route('companies.index')->with('success', config('Messages.200'));
            }
            # ---- If company Exist -----#
            // Checking by Name or Email to avoid Duplicates
            $companyName = companies::where('companyName', $request->company_name)->orWhere('companyEmail', $request->company_email)->first();
            if ($request->TIN && !$companyName) {
                $companyName = companies::where('tinNo', $request->TIN)->first();
            }

            if ($companyName) {
                $admin = CompanyAdmin::where('companyId', $companyName->companyID)->where('adminId', Auth::user()->user_uuid)->first();
                if (!$admin && Auth::user()->account_type == 2) {
                    CompanyAdmin::create([
                        'referenceId' => Str::uuid(),
                        'adminId' => Auth::user()->user_uuid,
                        'companyId' => $companyName->companyID
                    ]);
                }
                // Update existing company with new details
                $logoName = $companyName->companyLogo;
                if ($request->companyLogo) {
                    $validator = Validator::make($request->all(), [
                        'companyLogo' => 'file|mimes:png,jpg,pdf,jpeg'
                    ]);
                    if ($validator->fails()) {
                        return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
                    }
                    $logoName = str_replace(' ', '', $request->company_name) . date('sihdmY') . '.' . $request->companyLogo->extension();
                    File::delete(public_path('profile_images/') . $companyName->companyLogo);
                    $request->companyLogo->move(public_path('profile_images'), $logoName); // Delete Existing File
                }
                $companyName->update([
                    'companyName' => $request->company_name,
                    'companyEmail' => $request->company_email,
                    'companyPhone' => $request->company_phone,
                    'companySector' => $request->company_sector,
                    'companyCategory' => $request->company_category,
                    'companyLocation' => $request->companyLocation,
                    'isAcceptedTerms' => 1,
                    'companyLogo' => $logoName,
                    'website' => $request->website,
                    'personalName' => $request->personalName,
                    'personalEmail' => $request->personalEmail,
                    'personalPhone' => $request->personalPhone,
                    'personalTitle' => $request->personalTitle,
                    'companyAddress' => $request->company_address,
                    'registrationNo' => $request->registrationNo,
                    'tinNo' => $request->TIN,
                    'status' => 1,
                    'referenceModule' => 3,
                    'originality' => $request->originality,
                    'hasOtherCompanies' => $request->hasOtherCompanies == 'YES' ? true : false,
                    'lastUpdatedOn' => date('Y')
                ]);
                session(['companyId' => $companyName->companyID]);
                return redirect()->route('companies.index')->with('success', config('Messages.200'));
            }

            #------- END ------#
            $TSRN = $this->getTsrn();
            $companyId = Str::uuid();
            $logoName = Null;
            if ($request->companyLogo) {
                $logoName = str_replace(' ', '', $request->company_name) . date('sihdmY') . '.' . $request->companyLogo->extension();
                $request->companyLogo->move(public_path('profile_images'), $logoName);
            }
            companies::create([
                'companyID' => $companyId,
                'registrationID' => $TSRN,
                'companyName' => $request->company_name,
                'companyEmail' => $request->company_email,
                'companyPhone' => $request->company_phone,
                'companySector' => $request->company_sector,
                'companyCategory' => $request->company_category,
                'companyLocation' => $request->companyLocation,
                'isAcceptedTerms' => 1,
                'companyLogo' => $logoName,
                'status' => 1,
                'website' => $request->website,
                'personalName' => $request->personalName,
                'personalEmail' => $request->personalEmail,
                'personalPhone' => $request->personalPhone,
                'personalTitle' => $request->personalTitle,
                'companyAddress' => $request->company_address,
                'registrationNo' => $request->registrationNo,
                'tinNo' => $request->TIN,
                'lastUpdatedOn' => date('Y'),
                'referenceModule' => 3,
                'originality' => $request->originality,
                'hasOtherCompanies' => $request->hasOtherCompanies == 'YES' ? true : false,
            ]);
            session(['TSRN' => $TSRN]);
            $admin = CompanyAdmin::where('companyId', $companyId)->where('adminId', Auth::user()->user_uuid)->first();
            if (!$admin && Auth::user()->account_type == 2) {
                CompanyAdmin::create([
                    'referenceId' => Str::uuid(),
                    'adminId' => Auth::user()->user_uuid,
                    'companyId' => $companyId
                ]);
            }
            return redirect()->route('companies.index')->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }



    public function UpdateCompanyFromTRA(Request $request)
    {
        try {
            $company = companies::where('tinNo', $request->TIN)->where('status', 1)->first();
            $response = Http::withToken(config('global.MARKET_INFO_TOKEN'))->post(config('global.MARKET_INFO_BASE_URL') . 'tra_info', [
                'tinNo' => $request->TIN,
                'TSRN' => $company->registrationID,
            ]);
            foreach ($response['data'] as  $value) {
                $company = companies::where('tinNo', $request->TIN)
                    ->where('status', 1)->first();
                $company->update([
                    'companyName' => $value['traderName'],
                    'companyAddress' => $value['traderAddress'] . ', ' . $value['traderStreet'],
                    'tinNo' => $value['TIN'],
                    'tinStatus' => 'VERIFIED',
                    'registrationNo' => $value['BRN'],
                    'companyCategory' => $value['businessType'],
                    'brnStatus' => 'VERIFIED'
                ]);
            }
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
