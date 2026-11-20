<?php

namespace App\Http\Controllers\ePermit;

use App\Http\Controllers\Controller;
use App\Models\ePermit\ePermitApplication;
use App\Models\ePermit\PromotionEventType;
use Illuminate\Http\Request;

class ePermitController extends Controller
{

    public function AboutPermit(Request $request){
        $eventTypes=PromotionEventType::get();
        return view('ePermit.AboutPermit',compact('eventTypes'));
    }

    public function ApprovedEvents(Request $request){
        $search='';
        if (!empty($request->search)) {
            $search=$request->search;
        }
        
        $approvedEvents=ePermitApplication::leftJoin('companies','e_permit_applications.companyId','=','companies.companyID')
        ->leftJoin('trade_promotion_events','e_permit_applications.applicationId','=','trade_promotion_events.eventApplicationId')
        ->leftJoin('promotion_event_types','trade_promotion_events.tradeEventType','=','promotion_event_types.eventTypeId')
        ->where('permitNo','!=',Null)
        ->where('applicationStatus',6)
        ->where('eventTitle','LIKE','%'.$search.'%')
        ->orderBy('startFrom','DESC')
        ->paginate(6);
        return view('ePermit.ApprovedEvents',compact('approvedEvents'));
    }

}
