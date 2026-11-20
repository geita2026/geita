<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\exhibition\exhibition_products;
use Illuminate\Http\Request;
use App\Models\exhibition\exhibitors;
use App\Models\sectors;
use App\Models\settings\events;
use App\Models\Tickets\VipTicketBenefit;
use App\Traits\VistorsTrait;

class PublicPagesController extends Controller
{
    use VistorsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->Vistors($ip = $request->ip()); // save new vistor

        return view('FrontEnd.Public_Pages.expirience');
    }

    public function About_ditf(Request $request)
    {
        // $this->Vistors($ip = $request->ip()); // save new vistor

        $exhibitors = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
            ->leftJoin('events', 'exhibitors.exhibitor_event', '=', 'events.event_id')
            ->where('event_status', 1)->get();
        // ->leftJoin('company_details','companies.companyID','=','company_details.companyID')
        $event = events::where('event_status', 1)->first();
        $sectors = sectors::orderBy('sectorName','ASC')->get();
        return view('FrontEnd.Public_Pages.AboutDITF', compact('exhibitors', 'sectors','event'));

        return view('FrontEnd.Public_Pages.about_ditf', compact('exhibitors', 'sectors'));
    }

    public function ditf_map(Request $request)
    {
        // $this->Vistors($ip = $request->ip()); // save new vistor
        $exhibitors = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
            ->leftJoin('events', 'exhibitors.exhibitor_event', '=', 'events.event_id')
            // ->leftJoin('company_details','companies.companyID','=','company_details.companyID')
            ->where('event_status', 1)->get();
            $event=events::where('event_status',1)->first();

        $categories = sectors::get();
        return view('FrontEnd.Public_Pages.DitfMap', compact('exhibitors', 'categories','event'));

        return view('FrontEnd.Public_Pages.ditf_map', compact('exhibitors', 'categories'));
    }


    public function AboutSabaSabaTicket(Request $request)
    {
        $value = Null;
        $exhibitors = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
            ->where('companySector', 1)->get();

        $vipBenefits=VipTicketBenefit::get();
        return view('FrontEnd.Public_Pages.VipTickets.VipTicketBooking', compact('exhibitors','vipBenefits'));
    }


    public function carParking(Request $request){
        $event=Null;
        $exists=events::where('event_status',1)->first();
        if ($exists) {
            $event=$exists->event_id;
        }
        $sectors=sectors::count();
        $exhibitedProducts=exhibition_products::count();
        $exhibitors=exhibitors::where('exhibitor_event',$event)->count();

        return view('FrontEnd.Public_Pages.VipTickets.carparking',compact('sectors','exhibitedProducts','exhibitors'));
    }


    public function NationalBrandCompetition(Request $request){
        if ($request->submit) {
           $request->validate([
            'fullName'=>'required|min:5|max:100',
            'emailAddress'=>'required|email|max:100',
            'brandLogo'=>'required|file|mimes:zip|size:10240'
           ]);
           
        }

    }
}
