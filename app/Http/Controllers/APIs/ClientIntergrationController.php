<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\exhibition\exhibitors;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class ClientIntergrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function ExhibitorList(Request $request)
    {
        // if($request->user()->cannot('VIEW_ARTICLES',Role::class))
        // {
        //     return response()->json([
        //         'status'=>0,
        //         'message'=>config('global.RESTRICTED_ACCESS')
        //     ],401);
        // }
        $exhibitors=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
        ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
        ->leftJoin('sectors','companies.companySector','=','sectors.sectorID')
        ->where('event_status',1)->get([ 'companyID','companyName','judging_status','judging_location','event_name','companySector','sectorName','sectorID']);
        return response()->json([
            'status'=>1,
            'exhibitors'=>$exhibitors,
        ]);
    }
}
