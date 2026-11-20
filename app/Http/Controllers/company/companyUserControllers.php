<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\Companies\CompanyAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class companyUserControllers extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {}

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
        if ($request->user()->cannot('view-company-users', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $users = User::leftJoin('companies', 'users.myCompany', '=', 'companies.companyID')
            ->leftJoin('roles', 'users.role', '=', 'roles.id')
            ->where('myCompany', $id)->get();
        $users = CompanyAdmin::leftJoin('users', 'company_admins.adminId', '=', 'users.user_uuid')
            ->leftJoin('companies', 'company_admins.companyId', '=', 'companies.companyID')
            ->where('company_admins.companyId', $id)->get();

        $companies = companies::get(['companyID', 'companyName']);
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
            if ($request->user()->cannot('Update Company Users', Role::class)) {
                return back()->with('fail', config('Messages.401'));
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
            if ($request->user()->cannot('Sunspend User Account', Role::class)) {
                return back()->with('fail', config('Messages.401'));
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
