<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use App\Models\setting\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Contracts\Role;

class BankAccountController extends Controller
{
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
        if($request->user()->cannot('VIEW_BANK_ACCOUNT_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_BANK_ACCOUNT_SUBMENU'));
        }
        $banks=BankAccount::get();
        return view('settings.BankAccounts',compact('banks'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('CREATE_BANK_ACCOUNT',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_BANK_ACCOUNT'));
        }

        // return $request;
        $request->validate([
            'account_number'=>'required|unique:bank_accounts,account_number',
            'account_name'=>'required:unique:bank_accounts,account_name',
            'swift_code'=>'required',
            'account_currency'=>'required',
            'bank_name'=>'required'
        ]);

        try
        {
            BankAccount::create([
                'account_id'=>Str::uuid(),
                'account_number'=>$request->account_number,
                'account_name'=>$request->account_name,
                'bank_name'=>$request->bank_name,
                'swift_code'=>$request->swift_code,
                'account_currency'=>$request->account_currency,
                'account_descriptions'=>$request->account_descriptions,
                'bankAccountStatus'=>$request->bankAccountStatus
            ]);
            return back()->with('success',);
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500'));
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if($request->user()->cannot('CREATE_BANK_ACCOUNT',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_BANK_ACCOUNT'));
        }
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
        if($request->user()->cannot('UPDATE_BANK_ACCOUNT',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_BANK_ACCOUNT'));
        }
        // return $request;
        $request->validate([
            'account_number'=>'required',
            'account_name'=>'required',
            'swift_code'=>'required',
            'account_currency'=>'required'
        ]);

        try
        {


            BankAccount::find($id)->update([
                'account_number'=>$request->account_number,
                'account_name'=>$request->account_name,
                'bank_name'=>$request->bank_name,
                'swift_code'=>$request->swift_code,
                'account_currency'=>$request->account_currency,
                'account_descriptions'=>$request->account_descriptions,
                'bankAccountStatus'=>$request->bankAccountStatus
            ]);
            return back()->with('success',config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('success',config('Messages.500'));
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->user()->cannot('DELETE_BANK_ACCOUNT',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DELETE_BANK_ACCOUNT'));
        }
    }
}
