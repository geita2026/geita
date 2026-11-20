<?php

namespace App\Http\Controllers\off_fair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\companies;
use App\Models\off_fair\booking\space_size_category_costs;
use App\Models\space\pavilions;
use App\Models\off_fair\off_fair_spaces;
use App\Models\off_fair\spaces\off_fair_space_categories;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Intervention\Image\Facades\Image;
use Throwable;

class offFairSpaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->user()->cannot('Manage OffFairSpace', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $spaces = off_fair_spaces::leftJoin('off_fair_space_categories', 'off_fair_spaces.space_category', '=', 'off_fair_space_categories.space_category_id')
            ->leftJoin('space_size_category_costs', 'off_fair_spaces.space_category_cost', '=', 'space_size_category_costs.space_size_category_cost_id')
            ->get([
                'space_size_category_cost_id',
                'space_id',
                'off_fair_spaces.space_category_cost',
                'category_name',
                'space_name',
                'size_range_cost',
                'space_category',
                'space_size',
                'space_cost',
                'space_descriptions',
                'area_size_name','spaceImage','spaceCostWeekend'
            ]);
        $categories = off_fair_space_categories::get(['space_category_id', 'category_name']);
        $cost_categories = space_size_category_costs::get();
        //
        return view('Off-Fair.off_fair_spaces.off_fair_spaces', compact('spaces', 'categories', 'cost_categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('Manage OffFairSpace', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if ($request->user()->cannot('Manage OffFairSpace', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $validator = Validator::make($request->all(), [
                'space_name' => 'required|unique:off_fair_spaces,space_name',
                'space_cost' => 'required|numeric|min:0',
                'space_category' => 'required',
                'spaceCostWeekend'=>'required'
            ]);
            if ($validator->fails()) {
                return back()->with('fails', implode(',', $validator->getMessageBag()->all()));
            }
            $spaceImage = Null;
            if ($request->spaceImage) {
                $validator = Validator::make($request->all(), [
                    'spaceImage' => 'required|mimes:png,jpg,jpeg',
                ]);
                if ($validator->fails()) {
                    return back()->with('fails', implode(',', $validator->getMessageBag()->all()));
                }
                $fileName = 'OffFairSpace_' . date('sihdmY') . '.' . $request->spaceImage->extension();
                $image = Image::make($request->spaceImage)->resize(1000, 500);
                $path = public_path('uploadedFiles/');
                $image->save($path . $fileName);
                $spaceImage = env('APP_URL') . '/public/uploadedFiles/' . $fileName;
            }
            off_fair_spaces::create([
                'space_id' => Str::uuid(),
                'space_cost' => $request->space_cost,
                'space_descriptions' => $request->space_descriptions,
                'average_population' => $request->average_population,
                'space_registered_by' => Auth::user()->user_uuid,
                'space_category' => $request->space_category,
                'space_name' => $request->space_name,
                'space_size' => $request->space_size,
                'spaceImage' => $spaceImage,
                'spaceCostWeekend'=>$request->spaceCostWeekend,
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('Manage OffFairSpace', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        //
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
            if ($request->user()->cannot('Manage OffFairSpace', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $validator = Validator::make($request->all(), [
                'space_cost' => 'required|numeric|min:0',
            ]);
            if ($validator->fails()) {
                return back()->with('fails', implode(',', $validator->getMessageBag()->all()));
            }
             $space = off_fair_spaces::find($id);
            $spaceImage = $space->spaceImage ?? Null;
            if ($request->spaceImage) {
                
                $validator = Validator::make($request->all(), [
                    'spaceImage' => 'required|mimes:png,jpg,jpeg',
                ]);
       
                if ($validator->fails()) {
                    return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
                }

                $deleted = substr($spaceImage, -30);
                File::delete(public_path('uploadedFiles/' . $deleted));
                $fileName = 'OffFairSpace_' . date('sihdmY') . '.' . $request->spaceImage->extension();
                $image = Image::make($request->spaceImage)->resize(1000, 500);
                $path = public_path('uploadedFiles/');
                $image->save($path . $fileName);
                $spaceImage = env('APP_URL') . '/public/uploadedFiles/' . $fileName;
            }
            $space->update([
                'space_cost' => $request->space_cost,
                'space_descriptions' => $request->space_descriptions,
                'space_category' => $request->space_category,
                'space_registered_by' => Auth::user()->user_uuid,
                'space_name' => $request->space_name,
                'space_size' => $request->space_size,
                'spaceImage' => $spaceImage,
                'spaceCostWeekend'=>$request->spaceCostWeekend,
            ]);

            return back()->with('success', config('Messages.200'));
        } catch (Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('Delete OffFairSpace', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            off_fair_spaces::find($id)->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
