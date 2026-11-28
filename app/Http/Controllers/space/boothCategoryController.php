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
            if($request->user()->cannot('MANAGE_SPACES',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('MANAGE_SPACES'));
        }
        //
        $boothCategories = booth_categories::orderByDesc('created_at')->get();
        return view('space.booth-category',compact('boothCategories'));
        } catch (\Throwable $th) {
            report($th);
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
        if($request->user()->cannot('CREATE_BOOTH_CATEGORY',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_BOOTH_CATEGORY'));
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
        if($request->user()->cannot('UPDATE_BOOTH_CATEGORY',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_BOOTH_CATEGORY'));
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
        if($request->user()->cannot('DELETE_BOOTH_CATEGORY',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DELETE_BOOTH_CATEGORY'));
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
