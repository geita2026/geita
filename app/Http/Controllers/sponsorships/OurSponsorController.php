<?php

namespace App\Http\Controllers\sponsorships;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\advice_note\advice_notes;
use App\Models\space\additional_services;
use App\Models\space\booths;
use App\Models\advice_note\service_charges;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\booking\booked_sponsorship_packages;
use Spatie\Permission\Models\Role;

class OurSponsorController extends Controller
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
        if($request->user()->cannot('View Sponsors',Role::class))
        {
            return back()->with('fail',config('Messages.500'));
        }
        $sponsors=booked_sponsorship_packages::leftJoin('sponsorship_packages','booked_sponsorship_packages.package_id','=','sponsorship_packages.package_id')
        ->leftJoin('advice_notes','booked_sponsorship_packages.advice_note_id','=','advice_notes.advice_note_id')
        ->leftJoin('events','advice_notes.event','=','events.event_id')
        ->leftJoin('companies','booked_sponsorship_packages.customer_id','=','companies.companyID')
        ->where('advice_note_status','!=','CANCELED')
        ->where('event_status',1)
        ->get(); // get all packages

        // return $packages;

        // get all advice note generated in the system.
        $all_advice_notes=advice_notes::leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->orderBy('advice_note_created_at','DESC')
        ->where('advice_note_status','!=','CANCELED')
        ->where('invoice_type','Sponsorship') // return only sposorship customers
        ->get(); // get all advice note generated in the system;

        $service=additional_services::leftJoin('events','additional_services.service_event','=','events.event_id')
        ->where('service_type','Tantrade')
        ->where('event_status',1)->get(); // return services provided by only Tantrade

        $advice_note=advice_notes::get();

        $benefits=service_charges::leftJoin('advice_notes','service_charges.advice_note_id','=','advice_notes.advice_note_id')
        ->leftJoin('additional_services','service_charges.service_id','=','additional_services.serviceID')
        ->where('invoice_type','Sponsorship')->get();

        // return $advice_note;

        return view('sponsorship.our_sponsors',compact('all_advice_notes','service','sponsors','benefits','advice_note'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Sponsors',Role::class))
        {
            return back()->with('fail',config('Messages.500'));
        }
        try {
           foreach ($request->service as $value) {
            service_charges::create([
                'service_charge_id'=>Str::uuid(),
                'company_id'=>$request->customer_name,
                'user_id'=>Auth::user()->user_uuid,
                'service_id'=>$value,
                'exhibition_year'=>$request->exhibition_year,
                'service_quantity'=>$request->service_quantity,
                'advice_note_id'=>$request->advice_note_id,
            ]);
           }
           return back()->with('success','Benefits set to '.$request->advice_note_id.' Successfully!' );
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong!'.$th->getMessage());
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
        if($request->user()->cannot('View Sponsors',Role::class))
        {
            return back()->with('fail',config('Messages.500'));
        }
        //
        $advice_note=advice_notes::leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->leftJoin('booked_sponsorship_packages','advice_notes.advice_note_id','=','booked_sponsorship_packages.advice_note_id')
        ->leftJoin('sponsorship_packages','booked_sponsorship_packages.package_id','=','sponsorship_packages.package_id')
        ->orderBy('advice_note_created_at','DESC')
        ->where('invoice_type','Sponsorship') // return only sposorship customers
        ->where('advice_note_status','!=','CENCELED')
        ->leftJoin('events','advice_notes.event','=','events.event_id')
        ->where('event_status',1)->limit(50) // ensure event is open
        ->get();

        // get all advice note generated in the system.
        $all_advice_notes=advice_notes::leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->orderBy('advice_note_created_at','DESC')
        ->where('advice_note_status','!=','CANCELED')
        ->where('invoice_type','Sponsorship') // return only sposorship customers
        ->get(); // get all advice note generated in the system;

        $service=additional_services::leftJoin('events','additional_services.service_event','=','events.event_id')
        ->where('service_type','Tantrade')
        ->where('event_status',1)->get(); // return services provided by only Tantrade

        $booths=booths::orderBy('boothNo','ASC')->get();

        return view('sponsorship.our_sponsors',compact('advice_note','all_advice_notes','service','booths'));
    }

}
