<?php

namespace App\Http\Controllers\off_fair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\companies;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
Use App\Models\off_fair\customers\off_fair_customers;
use App\Traits\IntergrationTraits;
use Spatie\Permission\Models\Role;

class offFaircustomerController extends Controller
{

    use IntergrationTraits;
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
        if($request->user()->cannot('View Off Fair Customers',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        //
        $id=$request->companyID;
        $customers=companies::get(['companyID','companyName','companyPhone','companyEmail','companyLocation'])->where('companyID',$id);
        $off_fair_customers=off_fair_customers::orderBy('customer_registered_at','DESC')->get(['customer_registered_at','customer_id','customer_email','customer_phone','customer_name','customer_home_location']);
        $exhibitors=companies::get(['companyID','companyName']);
        return view('Off-Fair.off_fair_customers.new_off_fair_customer',compact('exhibitors','customers','off_fair_customers'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->user()->cannot('View Off Fair Customers',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $id=$request->companyID;
        $customers=companies::get(['companyID','companyName','companyPhone','companyEmail','companyLocation'])->where('companyID',$id);
        $off_fair_customers=off_fair_customers::orderBy('customer_registered_at','DESC')->get(['customer_registered_at','customer_id','customer_email','customer_phone','customer_name','customer_home_location']);
        $exhibitors=companies::get(['companyID','companyName']);
        return view('Off-Fair.off_fair_customers.new_off_fair_customer',compact('exhibitors','customers','off_fair_customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Off Fair Customers',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $request->validate([
            'customer_name'=>'required|unique:off_fair_customers,customer_name',
            'customer_phone'=>'required|starts_with:255|digits:12',
            'customer_home_location'=>'required'
        ]);

        try {

            $company_id=Str::uuid(); $firstName=$request->customer_name; $phone_number=$request->customer_phone;
            $email=$request->customer_email;

            if(empty($email))
            {
                $email=strtolower(str_replace(' ','',$request->customer_name.'.dammy'."@gmail.com")); // removing space for email comp
            }

            off_fair_customers::create([
                'customer_id'=>$company_id,
                'customer_name'=>$firstName,
                'customer_phone'=>$phone_number,
                'customer_email'=>$email,
                'customer_home_location'=>$request->customer_home_location,
                'customer_registered_by'=>Auth::user()->user_uuid,
            ]);
            if (config('global.APP_STATUS')==1) {
                $this->customer_registration_api($company_id,$firstName,$phone_number,$email); // register customer to billing system
            }
            return back()->with('success','Customer registered successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong!, please check your Inputs');
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
        if($request->user()->cannot('View Off-Fair Customer',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
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
        if($request->user()->cannot('Update Off Fair Customer',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //validating inputs
        $request->validate([
            'customer_phone'=>'required|starts_with:255|digits:12',
            'customer_home_location'=>'required'
        ]);
        //updating inputs



        try {
            $firstName=$request->customer_name; $phone_number=$request->customer_phone;$email=$request->customer_email;
        $company_id=$id;
            if(empty($request->customer_phone))
            {
                return back()->with('fail','Company lacks phone  number, please update it to proceed!');
            } // checking mandatory values

            if(empty($email))
            {
                $email=strtolower(str_replace(' ','',$firstName.'.dammy'."@gmail.com")); // removing space for email comp
            }

            $this->customer_registration_api($company_id,$firstName,$phone_number,$email);

    # ------- CHECKING CUSTOMER REGISTRATION -----#


        $customers=off_fair_customers::find($id);
        $updated=$customers->update([
            'customer_name'=>$request->customer_name,
            'customer_phone'=>$request->customer_phone,
            'customer_email'=>$request->customer_email,
            'customer_home_location'=>$request->customer_home_location,
            'customer_registered_by'=>Auth::user()->user_uuid,
        ]);

            return back()->with('success','Customer Updated successefully');

        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong, it might be due to internet connection problems'.$th->getMessage());
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
        if($request->user()->cannot('Delete Off Fair Customer',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $customer=off_fair_customers::find($id);
        $deleted=$customer->delete();
        if($deleted)
        {
            return back()->with('success','Customer Deleted Successflly');
        }
        else
        {
            return back()->with('fail','Something went wrong, it might be due to internet connection problems');
        }
    }
}
