<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractsManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-contracts')->except(['myContract', 'contractSigning', 'contractSigningFullpage', 'serveFlutterApp', 'serveFlutterAsset']);
    }

    public function index(Request $request)
    {
        try {
            $contracts = DB::table('signed_contracts')
                ->leftJoin('companies', 'signed_contracts.registration_id', '=', 'companies.registrationID')
                ->select(
                    'signed_contracts.registration_id',
                    'companies.companyName',
                    'companies.companyEmail',
                    'companies.companyPhone',
                    DB::raw("MAX(CASE WHEN signer_type = 'exhibitor' THEN CONCAT(signed_contracts.first_name, ' ', signed_contracts.middle_name, ' ', signed_contracts.last_name) END) as exhibitor_name"),
                    DB::raw("MAX(CASE WHEN signer_type = 'witness' THEN CONCAT(signed_contracts.first_name, ' ', signed_contracts.middle_name, ' ', signed_contracts.last_name) END) as witness_name"),
                    DB::raw("MAX(signed_contracts.updated_at) as last_updated"),
                    DB::raw("COUNT(DISTINCT signed_contracts.signer_type) as signature_count")
                )
                ->groupBy(
                    'signed_contracts.registration_id',
                    'companies.companyName',
                    'companies.companyEmail',
                    'companies.companyPhone'
                )
                ->orderBy('last_updated', 'desc')
                ->paginate(15);

            return view('Contracts.index', compact('contracts'));
        } catch (\Throwable $th) {
            return back()->with('fail', 'Error loading contracts: ' . $th->getMessage());
        }
    }

    public function contractSigning(Request $request)
    {
        return view('Contracts.signing');
    }

    public function contractSigningFullpage(Request $request)
    {
        $registrationID = $request->query('registrationID');

        if (!$registrationID) {
            abort(404);
        }

        $user = auth()->user();

        // Roles 20, 21, 34 (Super Admin, Admin, SAC Officer) can view any contract
        if (!in_array($user->role, [20, 21, 34])) {
            // Regular users can only view their own company's contract
            $company = DB::table('companies')
                ->where('companyID', $user->myCompany)
                ->first();

            if (!$company || $company->registrationID !== $registrationID) {
                abort(403, 'Unauthorized access to this contract');
            }
        }

        return view('Contracts.signing-fullpage', compact('registrationID'));
    }

    public function myContract(Request $request)
    {
        try {
            $user = auth()->user();

            // Get user's company registration ID (TSRN)
            if (!$user->myCompany) {
                return back()->with('fail', 'No company associated with your account. Please contact support.');
            }

            $company = DB::table('companies')
                ->where('companyID', $user->myCompany)
                ->first();

            if (!$company || !$company->registrationID) {
                return back()->with('fail', 'Company registration ID not found. Please contact support.');
            }

            // Redirect to the protected contract signing route with registration ID
            $registrationID = $company->registrationID;
            return redirect()->route('contract.signing', ['registrationID' => $registrationID]);
        } catch (\Throwable $th) {
            return back()->with('fail', 'Error loading your contract: ' . $th->getMessage());
        }
    }

}
