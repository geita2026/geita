<?php

namespace App\Http\Controllers\FrontEnd\VirtualExhibition;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_booths;
use App\Models\booking\booked_pavilions;
use App\Models\exhibition\VirtualService;
use App\Models\sectors;
use App\Models\space\pavilions;
use App\Models\space\space_categories;
use Illuminate\Http\Request;
use App\Traits\VistorsTrait;
use Intervention\Image\Facades\Image;

class VirtualExhibitionController extends Controller
{
  use VistorsTrait;
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->Vistors($ip = $request->ip()); // save new vistor

    $myProducts = VirtualService::InrandomOrder()->leftJoin('exhibitors', 'virtual_services.participation_id', '=', 'exhibitors.participation_id')
      ->leftJoin('events', 'exhibitors.exhibitor_event', '=', 'events.event_id')
      ->leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
      ->leftJoin('company_details', 'exhibitors.exhibitor_id', '=', 'company_details.companyID')
      ->get();

    return view('FrontEnd.Public_Pages.VirtualExhibition.welcomeVirtualExhibition', compact('myProducts'));
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $myProducts = VirtualService::InrandomOrder()->leftJoin('exhibitors', 'virtual_services.participation_id', '=', 'exhibitors.participation_id')
      ->leftJoin('events', 'exhibitors.exhibitor_event', '=', 'events.event_id')
      ->leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
      ->leftJoin('company_details', 'exhibitors.exhibitor_id', '=', 'company_details.companyID')
      ->where('virtual_service_id', $id)->get();
    return view('FrontEnd.Public_Pages.VirtualExhibition.ExhibitedProductDescription', compact('myProducts'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $pavilion = pavilions::where('pavilionID', $id)->first();

    $pav_members = booked_pavilions::leftJoin('companies', 'booked_pavilions.companyID', '=', 'companies.companyID')
      ->leftJoin('pavilions', 'booked_pavilions.pavilionID', '=', 'pavilions.pavilionID')
      ->where('booked_pavilions.pavilionID', $id)->get();

    $both_members = booked_booths::leftJoin('companies', 'booked_booths.companyID', '=', 'companies.companyID')
      ->leftJoin('booths', 'booked_booths.boothID', '=', 'booths.boothID')
      ->orderBy('boothNo', 'ASC')->where('pavilionID', $id)->get();

    return view('FrontEnd.Public_Pages.VirtualExhibition.PavilionMembers', compact('pavilion', 'pav_members', 'both_members'));
  }
}
