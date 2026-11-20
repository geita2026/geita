<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use App\Models\Collections\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class IncomeController extends Controller
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
        if($request->user()->cannot('View Income',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        # ----- UPDATE FOR NEW GFS CODE
        // $url='https://billing.tantrade.go.tz/index.php/v1/api_gfs';
        // $auth=array('token'=>'MH20230501NAVNORMILELIZURqm$dynUpe&@Y4YzgRpgnJ#y&XB%VQA95HBgq@guXSw68*fPV8!',"code"=>"SAMS");
        // $response=Http::withOptions(["verify"=>false])->post($url,[ 'auth'=>$auth]);

        // foreach ($response['data'] as $value)
        // {

        //     if(Income::where('gfs_code',$value['item_code'])->exists())
        //     {
        //         continue;
        //     }
        //     Income::create([
        //         'gfs_code'=>$value['item_code'],
        //         'income_name'=>$value['name'],
        //     ]);
        // }
        #CREATING GFS CODES

        $incomes=Income::get();
        return view('settings.income',compact('incomes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Income',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $validator=Validator::make($request->all(),
        [
            'gfs_code'=>'required|unique:incomes,gfs_code',
            'income_name'=>'required|unique:incomes,income_name',
            'description'=>'max:255'
        ]);

    if($validator->fails()){
        return response()->json(['status'=>0,'error'=>$validator->errors()->toArray()]);
    }

    else
    {
        Income::create([
            'gfs_code'=>$request->gfs_code,
            'income_name'=>$request->income_name,
            'description'=>$request->description,
            'is_space_fee'=>$request->is_space_fee,
            'is_joint_payment'=>$request->is_joint_payment,
        ]); // adding income to database
    return response()->json(['status'=>1,'success'=>'Income Registered Successfully!']);
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
        if($request->user()->cannot('Update Income',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        try {
        $income=Income::find($id);
        $income->update($request->all());
        return back()->with('success','Income updated successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->user()->cannot('Delete Income',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
       try {
        $income=Income::find($id);
        $income->delete();
        return back()->with('success','Income deleted Successfully!');
       } catch (\Throwable $th) {
        return back()->with('fail','Something went wrong!');
       }
    }

}
