<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collections\Income;
use App\Traits\CollectionSettingTraits;
use App\Traits\APITraits;
use Illuminate\Support\Facades\Validator;

class GfsCodeApiController extends Controller
{
    use CollectionSettingTraits;
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
        $request_name=$request->response['sp_code'];
        $token=$request->response['token'];
        $status=$this->AuthenticateRequest($request_name,$token);
        if($status==0)
        {
            return response()->json([
                'code'=>'401',
                'message'=>'You are not Authenticated to Access this Resource!'
            ]);
        }

        foreach ($request->data as $key)
        {
          $gfs_code= $key['item_code'];
          $income_name=$key['name'];
        $gfs_codes=Income::get();
        foreach ($gfs_codes as $value)
        {
            if ($gfs_code==$value->gfs_code)
            {
                continue;
            }
        }

        $this->SaveNewGfsCode($gfs_code,$income_name); // method to register new Iterm

      }

      return response()->json([
        'code'=>200,
        'message'=>'Item added successfully!',
      ]);

      } catch (\Throwable $th) {
        return response()->json([
            'code'=>500,
            'message'=>'Something went wrong!'
        ]);
      }

    }

}
