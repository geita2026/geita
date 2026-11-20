<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use App\Models\setting\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CountriesController extends Controller
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

        if($request->user()->cannot('Company Registration',Role::class))
        {
            return back()->with('fail',config('Messages.401'));
        }

        $countries=Country::orderBy('country_name','ASC')->get(['country_id','country_name','country_short_code','phone_code']);
        return view('settings.countries',compact('countries'));

    }

    public function create(Request $request){
        $search = $request->get('q');
        $data=Country::where(function ($query) use ($search) {
            $query->orWhere('country_name', 'LIKE', '%' . $search . '%');
        })->get(['country_name','phone_code']);
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Company Registration',Role::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        //

        $request->validate([
            'country_name'=>'required|unique:countries,country_name',
            'country_short_code'=>'unique:countries,country_short_code'
        ]);
        try {
            Country::create([
                'country_id'=>Str::uuid(),
                'country_name'=>$request->country_name,
                'country_short_code'=>$request->country_short_code,
                'phone_code'=>$request->phone_code,
            ]);
            return back()->with('success','Country registered successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong!');
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
        if($request->user()->cannot('Update Countries',Role::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        //
        $request->validate([
            'country_name'=>'required'
        ]);
        try {
            $country=Country::find($id);
            $country->update([
                'country_name'=>$request->country_name,
                'country_short_code'=>$request->country_short_code,
                'phone_code'=>$request->phone_code,
            ]);
            return back()->with('success','Country registered successfully!');
        } catch (\Throwable $th) {
            //throw $th;
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
        if($request->user()->cannot('Delete Countries',Role::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        //
        try {
            Country::find($id)->delete();
            return back()->with('success','Country Deleted successfull');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong!');
        }
    }
}
