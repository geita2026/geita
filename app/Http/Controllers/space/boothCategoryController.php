<?php
namespace App\Http\Controllers\space;
use App\Http\Controllers\Controller;
use App\Models\PriceConfiguration;
use Illuminate\Http\Request;
use App\Models\space\booth_categories;
use Illuminate\Support\Str;
use  Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class boothCategoryController extends Controller
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
        try {
            if($request->user()->cannot('Manage Spaces',Role::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        //
        $boothCategories=booth_categories::orderBy('created_at','DESC')->get(['booth_category_id','category_name','type_of_currency','category_cost','created_at','updated_at','categoryCode']);
        return view('space.booth-category',compact('boothCategories'));
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500'));
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Booth Categories',Role::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        //
        $request->validate([
            'category_name'=>'required',
            'booth_cost'=>'required|numeric|min:0',
            'type_of_currency'=>'required',
        ]);

        try {
            booth_categories::create([
                'booth_category_id'=>Str::uuid(),
                'user_id'=>Auth::user()->user_uuid,
                'category_name'=>$request->category_name,
                'categoryCode'=>$request->categoryCode,
                'category_cost'=>$request->booth_cost,
                'type_of_currency'=>$request->type_of_currency,
            ]);
            return back()->with('success',config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500'));
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
        if($request->user()->cannot('Update Booth Categories',Role::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        //
        $request->validate([
            'category_name'=>'required',
            'booth_cost'=>'required',
            'type_of_currency'=>'required',
        ]);

        try {
            booth_categories::find($id)->update([
                'user_id'=>Auth::user()->user_uuid,
                'category_name'=>$request->category_name,
                'category_cost'=>$request->booth_cost,
                'type_of_currency'=>$request->type_of_currency,
                'categoryCode'=>$request->categoryCode,
            ]);

            if (!PriceConfiguration::where('eventId', session('liveEvent'))->where('referenceId', $id)->exists()) {
                PriceConfiguration::create([
                    'trackingId' => Str::uuid(),
                    'referenceId' => $id,
                    'configurationName' => $request->category_name,
                    'configDescription' => $request->category_name,
                    'configCost' => $request->booth_cost,
                    'currency'=>$request->type_of_currency,
                    'eventId' => session('liveEvent')
                ]);
            }
            return back()->with('success',config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500'));
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
        if($request->user()->cannot('Delete Booth Categories',Role::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        //
           try {
            booth_categories::find($id)->delete();
            return back()->with('success','Booth category deleted Successfully!');
           } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500'));
           }

    }
}
