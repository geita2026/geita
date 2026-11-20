<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\companies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\company_details;
use App\Traits\APITraits;

class companiesAPIController extends Controller
{
    use APITraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
       try {
        $request_name=$request->response['code'];
        $token=$request->response['token'];
        $client_email=$request->response['email_address'];
        $status=$this->AuthenticateRequest($request_name,$token,$client_email);
        if($status==0)
        {
            return response()->json([
                'code'=>'401',
                'message'=>'Authentication Failed, please contact support@tanzaniafair.com or 255753427137 for help'
            ]);
        }

        $companies=companies::leftJoin('company_details','companies.companyID','=','company_details.companyID')
            ->leftJoin('sectors','companies.companySector','=','sectors.sectorID')
           ->get(['companies.companyID','companyName','companyEmail','personalName','personalPhone','sectorName','companyCategory','registrationID']);
           return response()->json(['status'=>1, 'companies'=>$companies],400);

       } catch (\Throwable $th) {
        //throw $th;
        return response()->json([
            'status'=>false,
            'message'=>$th->getMessage(),
        ],403);
       }
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

        try {
            $validateCompany=Validator::make($request->all(),
            [
                'company_name'=>'required|unique:companies,companyName',
                'company_phone'=>'required|starts_with:255|digits:12',
                'company_location'=>'required',
                'company_sector'=>'required',
                'company_category'=>'required',
                'company_email'=>'email',
            ]);


            if($validateCompany->fails()) // if validation does not meet failed
            {
                return response()->json([
                    'status'=>false,
                    'message'=>'Validation error',
                    'errors'=>$validateCompany->errors()
                ],400); // return response with the above details
            }

            $cid=Str::uuid(); // generating uuid
            $reg_id='T-'.substr(date('Y'),-3).'-'.random_int(100,999).'-'.random_int(100,999);
            $registered=companies::create([
                'companyID'=>$cid,
                'registrationID'=>$reg_id,
                'companyName'=>$request->company_name,
                'companyEmail'=>$request->company_email,
                'companyPhone'=>$request->company_phone,
                'companyCategory'=>$request->company_category,
                'companyLocation'=>$request->company_location,
                'companySector'=>$request->company_sector,
                'status'=>'1',
                'password'=>hash::make(12345678),
            ]);
            // end primary company data registration


            // company details registration
            $detailID=Str::uuid();
            $registered=company_details::create([
                'companyDetailID'=>$detailID,
                'companyID'=>$cid,
                'registrationNo'=>$request->registrationNo,
                'tinNo'=>$request->tinNo,
                'companyTel'=>$request->companyTele,
                'companyAddress'=>$request->companyAddress,
                'website'=>$request->website,
                'originality'=>$request->originality,
                'personalName'=>$request->personalName,
                'personalPhone'=>$request->personalPhone,
                'personalEmail'=>$request->personalEmail,
                'personalTitle'=>$request->personalTitle,
            ]);
            // end company details registration


        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage()
            ],400);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        //
        try {
            $companies=companies::find($id);
        //    checking if company exist
        $i=0;
        foreach ($companies as $value) {
            $i++;
        }

        if($i==0)
        {
            return response()->json([
                'status'=>false,
                'Message'=>'Company Not Found!',
            ],404);
        }
        // end checking

            return response()->json([
                'status'=>true,
                'companies'=>$companies,
            ],200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage(),
            ],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(companies $companies)
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
    public function update(Request $request,$id)
    {
        //
        $companies=companies::get('companyID',$id);
        $i=0;
        foreach ($companies as $key) {
            $i++;
        }

        if($i==0)
        {
            return response()->json([
                'status'=>false,
                'message'=>'Company Not Found',
            ],404);
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
        $companies=companies::find($id);
        $i=0;
        foreach ($companies as $key) {
            $i++;
        }
        if($i==0)
        {
            return response()->json([
                'status'=>false,
                'message'=>'Company Not Found',
            ],404);
        }
    }
}
