<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class companyUserControllers extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->companyUsersView($request);
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
    public function show(Request $request, $id)
    {
        return $this->companyUsersView($request, $id);
    }

    private function companyUsersView(Request $request, ?string $companyId = null)
    {
        $viewer = $request->user();
        if ($viewer->cannot('VIEW_COMPANY_USER', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_COMPANY_USER'));
        }

        $memberships = DB::table('company_admins')
            ->select('adminId as user_uuid', 'companyId as company_id')
            ->union(DB::table('users')->whereNotNull('myCompany')
                ->select('user_uuid', 'myCompany as company_id'));

        $visibleCompanies = companies::query();
        if ((int) $viewer->account_type === 2) {
            $visibleCompanies->where(function ($query) use ($viewer) {
                $query->where('companies.companyID', $viewer->myCompany)
                    ->orWhereExists(function ($admins) use ($viewer) {
                        $admins->selectRaw('1')->from('company_admins')
                            ->whereColumn('company_admins.companyId', 'companies.companyID')
                            ->where('company_admins.adminId', $viewer->user_uuid);
                    });
            });
        }

        $users = User::joinSub($memberships, 'company_memberships', function ($join) {
                $join->on('users.user_uuid', '=', 'company_memberships.user_uuid');
            })
            ->joinSub($visibleCompanies->select('companyID', 'companyName'), 'user_company', function ($join) {
                $join->on('company_memberships.company_id', '=', 'user_company.companyID');
            })
            ->when($companyId !== null, fn ($query) => $query->where('user_company.companyID', $companyId))
            ->orderBy('user_company.companyName')
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get([
                'users.user_uuid', 'users.first_name', 'users.middle_name', 'users.last_name',
                'users.name', 'users.email', 'users.phone_number', 'users.account_status',
                'users.myCompany', 'user_company.companyID', 'user_company.companyName',
            ]);

        $companies = $visibleCompanies->orderBy('companyName')->get();

        return view('company.CompanyUsers', compact('users', 'companies'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::leftJoin('companies', 'users.myCompany', '=', 'companies.companyID')
            ->leftJoin('roles', 'users.role', '=', 'roles.id')
            ->where('user_uuid', $id)->get();
        $companies = companies::get(['companyID', 'companyName']);
        return view('company.updateCompanyUsers', compact('users', 'companies'));
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
        try {
            if ($request->user()->cannot('UPDATE_COMPANY_USER', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('UPDATE_COMPANY_USER'));
            }
            $userId = User::where('user_uuid', $id)->first()->id;

            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:users,email,' . $userId,
            ]);

            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            User::find($userId)->update([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
            ]);
            return back()->with('success', 'User Updated Successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
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
        try {
            if ($request->user()->cannot('SUNSPEND_USER_ACCOUNT', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('SUNSPEND_USER_ACCOUNT'));
            }
            $user = User::where('user_uuid', $id)->first();
            if ($user->account_status == 0) {
                $user->update(['account_status' => 1]);
                return back()->with('success', 'User Account Activated Successfully!');
            } elseif ($user->account_status == 1) {
                $user->update(['account_status' => 0]);
                return back()->with('success', 'User Account Sunspended Successfully!');
            }
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
