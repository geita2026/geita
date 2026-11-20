<?php

namespace App\Http\Controllers\Catalogy;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_pavilions;
use App\Models\exhibition\CatalogueSetting;
use App\Models\exhibition\exhibition_products;
use App\Models\exhibition\exhibitors;
use App\Models\exhibition\VirtualFiles;
use App\Models\exhibition\VirtualService;
use App\Models\sectors;
use App\Models\settings\events;
use App\Models\sponsorships\EventSponsor;
use App\Models\Vistors\SiteVistor;
use Illuminate\Http\Request;
use App\Traits\VistorsTrait;

class CatalogyController extends Controller
{
    use VistorsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function __construct(){

    //     $this->middleware('auth');
    // }
    public function index(Request $request)
    {
        $sectorId = sectors::inRandomOrder()->first()->sectorID;
        session(['sectorId'=>$sectorId]);
        if ($request->sectorId) {
            session(['sectorId'=>$request->sectorId]);
        }
        $exhibitors = exhibitors::inRandomOrder()->leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
            ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
            ->where('event_status', 1)->where('companySector',$sectorId)->paginate();

        $totalExhibitorCount =exhibitors::inRandomOrder()->leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
        ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
        ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
        ->where('event_status', 1)->where('companySector',$sectorId)->count();

        $event = events::where('event_status', 1)->first();
        $exhibitedProducts = exhibition_products::where('event', $event->event_id)->get();
        $sectors = sectors::orderBy('sectorName','ASC')->get();
        $totalSectors = sectors::count();
        $totalProducts=exhibition_products::count();
        $sector=sectors::find($sectorId);
        return view('FrontEnd.Public_Pages.ExhibitorCategories', compact('exhibitors','totalExhibitorCount', 'exhibitedProducts', 'sectors','event','totalSectors','totalProducts','sector'));
    }

   

   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // $this->Vistors($ip = $request->ip()); # save new vistor

        $events = events::get();
         $exhibitor=exhibitors::find($id);
         $id=$exhibitor->exhibitor_id;
        session(['companyID'=>$id]);
        
        $exhibitors = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
            ->leftJoin('company_details', 'exhibitors.exhibitor_id', '=', 'company_details.companyID')
            ->leftJoin('events', 'exhibitors.exhibitor_event', '=', 'events.event_id')
            ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->where('event_status',1)->where('exhibitor_id',$id)->first();

        $totalVisitors=exhibitors::where('exhibitor_id',$id)->count();
        $exhibitorCount=SiteVistor::where('page_visted','LIKE','%'.$id.'%')->count();

        $sponsors = EventSponsor::inRandomOrder()->where('sponsorship_category', 1)->get();

        $catalogueSettings = CatalogueSetting::orderBy('order_number', 'ASC')
            ->leftJoin('events', 'catalogue_settings.event', '=', 'events.event_id')
            ->where('event_status', 1)->get();

        $event = events::where('event_status', 1)->first();
        $exhibitedProducts = exhibition_products::where('event', $event->event_id)->get();
        
        $totalProducts=exhibition_products::where('event', $event->event_id)->count();

         $myStand=booked_pavilions::leftJoin('booths','booked_pavilions.pavilionID','=','booths.boothID')
        ->leftJoin('pavilions','booths.pavilionID','=','pavilions.pavilionID')
        ->leftJoin('advice_notes','booked_pavilions.advice_note_id','=','advice_notes.advice_note_id')
        ->where('booked_pavilions.companyID',$id)->where('advice_note_status','!=','CANCELED')
        ->where('booths.boothID','!=',Null)
        ->where('exhibition_year',$event->event_id)->get();

         $myPavilion=booked_pavilions::leftJoin('pavilions','booked_pavilions.pavilionID','=','pavilions.pavilionID')
         ->leftJoin('advice_notes','booked_pavilions.advice_note_id','=','advice_notes.advice_note_id')
         ->where('booked_pavilions.companyID',$id)->where('advice_note_status','!=','CANCELED')
         ->where('pavilions.pavilionName','!=',Null)
        ->where('exhibition_year',$event->event_id)->get();

        $myProducts=VirtualService::leftJoin('exhibitors','virtual_services.participation_id','=','exhibitors.participation_id')

        ->where('exhibitor_id',$id)->get();
        $pavilionImages=VirtualFiles::where('reference_id',$id)->get(['image_id','reference_id','destination','image_name','image_description']);
        return view('FrontEnd.Public_Pages.ExhibitorPavilion', compact('exhibitors','myPavilion','myStand', 'sponsors', 'exhibitedProducts','myProducts','totalProducts','pavilionImages','exhibitorCount','totalVisitors','event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $sectorId = $id;
        
        $exhibitors = exhibitors::inRandomOrder()->leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
            ->leftJoin('company_details', 'exhibitors.exhibitor_id', '=', 'company_details.companyID')
            ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
            ->where('event_status', 1)->where('companySector',$sectorId)->get();

            $totalExhibitorCount = exhibitors::inRandomOrder()->leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
            ->leftJoin('company_details', 'exhibitors.exhibitor_id', '=', 'company_details.companyID')
            ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
            ->where('event_status', 1)->where('companySector',$sectorId)->count();

        $sectors = sectors::orderBy('sectorName','ASC')->get();
        $sector=sectors::find($sectorId);
        return view('FrontEnd.Public_Pages.ExhibitorsList', compact('exhibitors','totalExhibitorCount', 'sectors','sector'));
 
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
        //
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
    }

    public function sabaSabaVisitors(){
        return view('sabasabaVisitors');
    }
}
