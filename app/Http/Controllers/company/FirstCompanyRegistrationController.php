<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\Companies\CompanyAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FirstCompanyRegistrationController extends Controller
{
    public function store(Request $request)
    {
        abort_unless((int) $request->user()->account_type === 2, 403);

        return $this->register($request, true);
    }

    public function storeFromCompanyList(Request $request)
    {
        if ($request->user()->cannot('CREATE_COMPANY')) {
            return back()->with('fail', $this->permissionDeniedMessage('CREATE_COMPANY'));
        }

        return $this->register($request, false);
    }

    private function register(Request $request, bool $firstCompanyOnly)
    {
        $data = $request->validateWithBag('firstCompany', [
            'companyName' => ['required', 'string', 'max:255'],
            'tinNo' => ['required', 'digits:9'],
            'companyEmail' => ['required', 'email', 'max:255'],
            'companyPhone' => ['required', 'string', 'max:30'],
            'companyAddress' => ['required', 'string', 'max:1000'],
            'companySector' => ['required', Rule::exists('sectors', 'sectorID')],
            'companyLocation' => ['required', Rule::exists('countries', 'country_name')],
            'companyCategory' => ['required', Rule::in([
                'Individuals', 'Small Traders', 'Medium-Sized Enterprises',
                'Large Corporates', 'Government Insititution',
            ])],
            'registrationNo' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'isAcceptedTerms' => ['accepted'],
        ]);

        $company = DB::transaction(function () use ($request, $data, $firstCompanyOnly) {
            $year = now()->year;
            $sequence = DB::table('company_registration_sequences')->where('year', $year);
            DB::table('company_registration_sequences')->insertOrIgnore([
                'year' => $year,
                'last_number' => 0,
            ]);
            $lastNumber = $sequence->lockForUpdate()->value('last_number');

            $user = User::whereKey($request->user()->getKey())->lockForUpdate()->firstOrFail();
            $alreadyRegistered = $user->myCompany
                || CompanyAdmin::where('adminId', $user->user_uuid)->exists();
            if ($firstCompanyOnly && $alreadyRegistered) {
                return null;
            }

            if (companies::where('tinNo', $data['tinNo'])->exists()) {
                throw ValidationException::withMessages([
                    'tinNo' => 'A company with this TIN is already registered. Contact support for access.',
                ])->errorBag('firstCompany');
            }

            $prefix = sprintf('GMMS%02d', $year % 100);
            $currentIds = companies::where('registrationID', 'like', $prefix . '____')
                ->orderByDesc('registrationID')
                ->pluck('registrationID');
            $latestId = $currentIds->first(fn ($id) => preg_match('/^' . $prefix . '[0-9]{4}$/D', $id));
            $nextNumber = max($lastNumber, $latestId ? (int) substr($latestId, -4) : 0) + 1;
            if ($nextNumber > 9999) {
                throw ValidationException::withMessages([
                    'registration' => 'All company registration numbers for this year have been issued.',
                ])->errorBag('firstCompany');
            }

            $company = companies::create(array_merge($data, [
                'companyID' => (string) Str::uuid(),
                'registrationID' => sprintf('%s%04d', $prefix, $nextNumber),
                'isAcceptedTerms' => true,
                'status' => 1,
                'lastUpdatedOn' => $year,
            ]));
            if ((int) $user->account_type === 2) {
                CompanyAdmin::create([
                    'referenceId' => (string) Str::uuid(),
                    'adminId' => $user->user_uuid,
                    'companyId' => $company->companyID,
                ]);
                if (!$user->myCompany) {
                    $user->update(['myCompany' => $company->companyID]);
                }
            }
            $sequence->update(['last_number' => $nextNumber]);

            return $company;
        }, 3);

        if (!$company) {
            return redirect()->route('dashboard');
        }

        return redirect()->route($firstCompanyOnly ? 'dashboard' : 'companies.index')->with(
            'success',
            'Company registered successfully. Your registration ID is ' . $company->registrationID . '.'
        );
    }
}
