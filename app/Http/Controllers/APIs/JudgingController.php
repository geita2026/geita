<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\exhibition\exhibitors;
use App\Models\sectors;
use App\Models\settings\events;
use App\Traits\APITraits;
use Illuminate\Http\Request;

class JudgingController extends Controller
{

    use APITraits;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            # ------ API AUTHENTICATION ------#
            $request_name=$request->response['sp_code'];
            $token=$request->response['token'];
            $status=$this->AuthenticateRequest($request_name,$token);
            if($status==0)
            {
                $response=array('code'=>401,'message'=>'Authentication failed');
                return response()->json([ 'response'=>$response],200);
            }

            #------- API AUTHENTICATION ------#
            
            $liveEvent=events::where('event_status',1)->first()->event_id;
            $exhibitors=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
            ->leftJoin('judging_categories', 'exhibitors.judgingCategory', '=', 'judging_categories.categoryId')
            ->where('exhibitor_event',$liveEvent)
            ->where('judging_status',1)
            ->get([ 'companyID','companyName','judging_status','pStatus','judging_location','judgingCategory']);

            $sponsors=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
            ->leftJoin('advice_notes','exhibitors.exhibitor_id','=','advice_notes.company_id')
            ->leftJoin('booked_sponsorship_packages','advice_notes.advice_note_id','=','booked_sponsorship_packages.advice_note_id')
            ->where('advice_note_status','APPROVED')
            ->where('event',$liveEvent)
            ->where('booked_sponsorship_packages.advice_note_id','!=',Null)
            ->get(['companyID','companyName']);
            
            $response=array('code'=>1,'exhibitors'=>$exhibitors,'sponsors'=>$sponsors);

            return response()->json(['response'=>$response]);

        } catch (\Throwable $th) {
            $response=array('code'=>0,'message'=>'Check your request, Some information is missing or submited incorrectly!'.$th->getMessage());
            return response()->json(['response'=>$response],200);
        }
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
        try {
            companies::find($id)->update([
                'companyName'=>$request->companyName,
                'companySector'=>$request->company_sector,
            ]);

            $event=events::where('event_status',1)->first()->event_id;
            $participation_id=exhibitors::where('exhibitor_event',$event)->where('exhibitor_id',$id)->first()->participation_id;
            if($participation_id)
            {
                exhibitors::find($participation_id)->update([
                    'judging_location'=>$request->judging_location,
                    'judging_status'=>$request->confirm_judging
                ]);
            }
            return back()->with('success','Your judging request Received successfully!');
        }
         catch (\Throwable $th)
        {
            return back()->with('fail','Something went wrong!'.$th->getMessage());
        }
    }


    public function CompanySectors(Request $request)
    {
         # ------ API AUTHENTICATION ------#
         $request_name=$request->response['sp_code'];
         $token=$request->response['token'];
         $status=$this->AuthenticateRequest($request_name,$token);
         if($status==0)
         {
            $response=array('code'=>401,'message'=>'Authentication failed');
            return response()->json([ 'response'=>$response],200);
        }
        #------- API AUTHENTICATION ------#

        try
        {
            $sectors=sectors::get(['sectorID','sectorName']);
            $response=array('code'=>1,'data'=>$sectors);
            return response()->json([
                'response'=>$response
            ],200);
        }
        catch (\Throwable $th)
        {
            $response=array('code'=>0,'message'=>'Something went wrong!');
            return response()->json([
                'response'=>$response
            ]);
        }
    }

}
