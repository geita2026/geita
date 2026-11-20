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
        if($request->user()->cannot('View All Company',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $company_list=companies::leftJoin('company_details','companies.companyID','=','company_details.companyID')->limit(300)
        ->orderBy('companyName','ASC')
        ->get(['companies.companyID','companyName','companyPhone','companyEmail','companyDetailID','personalName','personalEmail','personalPhone']);
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

        if($request->user()->cannot('View Company',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $sectors=sectors::orderBy('sectorName','ASC')->get(['sectorID','sectorName']);
        $companies=companies::leftJoin('sectors','companies.companySector','=','sectors.sectorID')
        ->leftJoin('company_details','companies.companyID','=','company_details.companyID')
        ->get(['companies.companyID','companyName','companyEmail','companyPhone','companyCategory'
        ,'companyLocation','companySector','sectorName','companyDetailID','registrationNo','tinNo',
        'companyTel','companyAddress','website','originality','personalName','personalPhone',
        'personalEmail','personalTitle','registrationID','companies.updated_at'])
        ->where('companyID',$id);

        $countries=Country::get(['country_name']);
        return view('company.company-profile',compact('companies','countries','sectors'));
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
        if($request->user()->cannot('Update Company',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $sectors=sectors::orderBy('sectorName','ASC')->get(['sectorID','sectorName']);
        $companies=companies::leftJoin('sectors','companies.companySector','=','sectors.sectorID')
        ->leftJoin('company_details','companies.companyID','=','company_details.companyID')
        ->get(['companies.companyID','companyName','companyEmail','companyPhone','companyCategory'
        ,'companyLocation','companySector','sectorName','companyDetailID','registrationNo','tinNo',
        'companyTel','companyAddress','website','originality','personalName','personalPhone',
        'personalEmail','personalTitle','registrationID','companies.updated_at'])
        ->where('companyID',$id);

        $countries=Country::get(['country_name']);
        return view('company.company-profile',compact('companies','countries','sectors'));

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {

        if($request->user()->cannot('View Company',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
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
