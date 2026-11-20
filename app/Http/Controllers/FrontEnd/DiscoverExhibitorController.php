<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_booths;
use App\Models\booking\booked_pavilions;
use App\Models\companies;
use App\Models\exhibition\exhibitors;
use App\Models\exhibition\VirtualFiles;
use App\Models\exhibition\VirtualService;
use App\Models\sectors;
use App\Models\User;
use App\Models\Vistors\SiteVistor;
use Illuminate\Http\Request;
use App\Traits\VistorsTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class DiscoverExhibitorController extends Controller
{
    use VistorsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id='';
        $exhibitors=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
        ->leftJoin('company_details','companies.companyID','=','company_details.companyID')
        ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')->where('event_status',1)->get();

        $company=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
        ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
        ->leftJoin('company_details','companies.companyID','=','company_details.companyID')
        ->where('event_status',1)->orderBy('companyName','ASC')->limit(4)->get();

        $services=VirtualService::get();
        $categories=sectors::get();
        $pavilionImages=VirtualFiles::orderBy('image_added_at','ASC')->get();

        $booths=booked_booths::leftJoin('booths','booked_booths.boothID','=','booths.boothID')
        ->leftJoin('pavilions','booths.pavilionID','=','pavilions.pavilionID')
        ->where('companyID',$id)->orderBy('boothNo','ASC')->get();

        $pavilions=booked_pavilions::leftJoin('pavilions','booked_pavilions.pavilionID','=','pavilions.pavilionID')
        ->leftJoin('advice_notes','booked_pavilions.advice_note_id','=','advice_notes.advice_note_id')
        ->leftJoin('events','advice_notes.event','=','events.event_id')
        ->where('event_status',1)->where('advice_note_status','!=','CANCELED')->where('companyID',$id)->get();
        $sectors=sectors::orderBy('sectorName','ASC')->get();

        $users=User::where('user_uuid',$id)->first();
        if($users){$logo=$users->profile_image;}
        else{$logo='notfound';}

        return view('FrontEnd.Public_Pages.discover_exhibitor',compact('exhibitors','sectors','categories','company','services','pavilionImages','booths'
    ,'pavilions','logo'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // save new vistor

        $vistors=SiteVistor::where('page_visted','LIKE','%'.$id.'%')->count();

        $exhibitors=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
        ->leftJoin('company_details','companies.companyID','=','company_details.companyID')
        ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
        ->where('event_status',1)->get();

        $company=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
        ->leftJoin('company_details','companies.companyID','=','company_details.companyID')
        ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
        ->where('event_status',1)->where('exhibitor_id',$id)
        ->get(['companies.companyID','companyName','exhibitor_id','welcome_word','companyEmail','companyPhone','website','participation_id']);

        $categories=sectors::get();

        $services=VirtualService::get();
        $pavilionImages=VirtualFiles::orderBy('image_added_at','ASC')->where('reference_id',$id)->get();

        $booths=booked_booths::leftJoin('booths','booked_booths.boothID','=','booths.boothID')
        ->leftJoin('pavilions','booths.pavilionID','=','pavilions.pavilionID')
        ->leftJoin('advice_notes','booked_booths.advice_note_id','=','advice_notes.advice_note_id')
        ->leftJoin('events','advice_notes.event','=','events.event_id')->where('event_status',1)
        ->where('advice_note_status','!=','CENCELED')->where('companyID',$id)->orderBy('boothNo','ASC')->get();

        $pavilions=booked_pavilions::leftJoin('pavilions','booked_pavilions.pavilionID','=','pavilions.pavilionID')
        ->leftJoin('advice_notes','booked_pavilions.advice_note_id','=','advice_notes.advice_note_id')
        ->leftJoin('events','advice_notes.event','=','events.event_id')
        ->where('event_status',1)->where('advice_note_status','APPROVED')->where('companyID',$id)->get();

        $users=User::where('user_uuid',$id)->first();
        if($users){$logo=$users->profile_image;}
        else{$logo=null;}

        return view('FrontEnd.Public_Pages.CompanyPavilion',compact('exhibitors','categories','company',
        'vistors','services','pavilionImages','booths','pavilions','logo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $exhibitors=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
        ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
        ->leftJoin('company_details','companies.companyID','=','company_details.companyID')->where('event_status',1)->get();

        $company=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
        ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
        ->leftJoin('company_details','companies.companyID','=','company_details.companyID')
        ->leftJoin('sectors','companies.companySector','=','sectors.sectorID')
        ->where('event_status',1)->where('companySector',$id)->get(['companies.companyID','companyName','sectorName']);

        $sectors=sectors::orderBy('sectorName','ASC')->get();

        $categories=sectors::get();


        return view('FrontEnd.Public_Pages.discover_exhibitor',compact('company','sectors','exhibitors'));
    }

}
