<?php

namespace App\Http\Controllers\sponsorships;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_sponsorship_packages;
use App\Models\companies;
use App\Models\sponsorships\customer_packages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class customerpackagesController extends Controller
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
        if($request->user()->cannot('MANAGE_CUSTOMER_PACKAGE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('MANAGE_CUSTOMER_PACKAGE'));
        }
        //
        $packages=booked_sponsorship_packages::leftJoin('advice_notes','booked_sponsorship_packages.advice_note_id','=','advice_notes.advice_note_id')
        ->leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->leftJoin('sponsorship_packages','booked_sponsorship_packages.package_id','=','sponsorship_packages.package_id')
        ->where('advice_note_status','APPROVED')
        ->orderBy('advice_note_created_at','DESC')
        ->paginate(10);

        return view('sponsorship.customer-sponsorship-packages-list',compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->user()->cannot('CREATE_CUSTOMER_PACKAGE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_CUSTOMER_PACKAGE'));
        }
        //
        $companies=companies::get(['companyID','companyName']);
        return view('sponsorship.create-customer-package',compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('CREATE_CUSTOMER_PACKAGE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_CUSTOMER_PACKAGE'));
        }
        //
        $request->validate([
            'customer_name'=>'required',
            'package_name'=>'required',
            'package_amount'=>'numeric|min:0',
            'exhibition_year'=>'required',
        ]);

        $registered=customer_packages::create([
            'customer_id'=>$request->customer_name,
            'customer_package_id'=>Str::uuid(),
            'package_name'=>$request->package_name,
            'package_description'=>$request->package_description,
            'type_of_currency'=>$request->type_of_currency,
            'created_by'=>Auth::user()->user_uuid,
            'package_amount'=>$request->package_amount,
            'exhibition_year'=>$request->exhibition_year,
        ]);
        if($registered)
        {
            return back()->with('success','Customer Sponsorship package registered successfully!');
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
        if($request->user()->cannot('UPDATE_CUSTOMER_PACKAGE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_CUSTOMER_PACKAGE'));
        }
        //

        // return $request;
        $request->validate([
            'package_name'=>'required',
            'package_amount'=>'numeric|min:0',
            'exhibition_year'=>'required',
        ]);

        $package=customer_packages::find($id);
        $updated=$package->update([
            'package_name'=>$request->package_name,
            'package_description'=>$request->package_description,
            'type_of_currency'=>$request->type_of_currency,
            'created_by'=>Auth::user()->user_uuid,
            'package_amount'=>$request->package_amount,
            'exhibition_year'=>$request->exhibition_year,
        ]);
        if($updated)
        {
            return back()->with('success','Sponsorship package updated successfully!');
        }
        else
        {
            return dd('No data');
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
        if($request->user()->cannot('DELETE_CUSTOMER_PACKAGE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DELETE_CUSTOMER_PACKAGE'));
        }
        //
        $package=customer_packages::find($id);
        $deleted=$package->delete();
        if($deleted)
        {
         return back()->with('success','Customer Sponsorship package deleted successfully!');
        }
    }
}
