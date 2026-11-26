<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\space\pavilions;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class SpacesApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('VIEW_ALL_COMPANIES',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_ALL_COMPANIES'));
        }
        try {

            $validateUser=Validator::make($request->all(),
            [
                'event_name'=>'required',
            ]);

            if($validateUser->fails())
            {
                return response()->json([
                    'status'=>false,
                    'errors'=>$validateUser->errors()
                ],401);
            }
            $spaces=pavilions::leftJoin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')
            ->leftJoin('events','pavilions.event','=','events.event_id')
            ->get(['pavilionID','pavilionName','pavilion_size','category_name','bookingStatus','cost_one_msq','space_Status','event_name']);
            return response()->json([
                'status'=>true,
                'spaces'=>$spaces,
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>'something went wrong!'.$th->getMessage()
            ],500);
        }

    }

}
