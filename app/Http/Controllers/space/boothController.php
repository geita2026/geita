<?php

namespace App\Http\Controllers\space;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_pavilions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\space\booths;
use App\Models\space\pavilions;
use App\Models\space\booth_categories;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class boothController extends Controller
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
        try {
            if ($request->user()->cannot('MANAGE_SPACES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPACES'));
            }
            return view('space.add-booth');
        } catch (\Throwable $th) {
            return back()->with('success', config('Messages.500'));
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
        try {
            if ($request->user()->cannot('MANAGE_SPACES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPACES'));
            }
            //
            $validator = Validator::make($request->all(), [
                'booth_name' => 'required',
                'booth_size' => 'required|numeric|gt:0',
                'booth_category' => 'required',
                'pavilionID' => 'required|exists:pavilions,pavilionID',
                'booth_description' => 'max:30'
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            ########### UPDATE SPACE SIZE ################ 
            $partitionsSize = booths::where('bookingStatus', 1)
                ->where('pavilionID', $request->pavilionID)
                ->sum('boothSize');
            $space = pavilions::find($request->pavilionID);
            $SpaceSize = $space->pavilion_size;
            $spaceRemained = $SpaceSize - $partitionsSize;

            if (($partitionsSize + $request->booth_size) > $SpaceSize) {
                return back()->with('fail', 'Space Exceeds Maximum Space Size of ' . number_format($SpaceSize, 2) . ' SQM , Remained only ' . $spaceRemained . ' SQM');
            }


            ############## END CHECKING SPACE SIZE #######
            booths::create([
                'boothID' => Str::uuid(),
                'pavilionID' => $request->pavilionID,
                'boothNo' => $request->booth_name,
                'boothSize' => $request->booth_size,
                'booth_category' => $request->booth_category,
                'boothCost' => $request->booth_cost,
                'boothDescription' => $request->booth_description,
                'bookingStatus' => $request->partitionStatus,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            report($th);
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
        try {
            if ($request->user()->cannot('MANAGE_SPACES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPACES'));
            }
            $booth_categories = booth_categories::get(['booth_category_id', 'category_name']);
            $pav = pavilions::get(['pavilionName', 'pavilionID'])->where('pavilionID', $id);
            $pavilions = booths::leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
                ->leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')
                ->orderBy('boothNo', 'ASC')
                ->where('pavilions.pavilionID', $id)
                ->paginate(10, [
                    'pavilions.pavilionID',
                    'pavilionName',
                    'TotalBooths',
                    'pavilionCategory',
                    'pavilionRate',
                    'pavilionDescription',
                    'boothNo',
                    'boothSize',
                    'booth_category',
                    'boothDescription',
                    'category_cost',
                    'boothID',
                    'category_name',
                    'booths.bookingStatus',
                    'type_of_currency',
                    'booths.latitude','booths.longitude'
                ]);
            return view('space.booths', compact('pavilions', 'id', 'pav', 'booth_categories'));
        } catch (\Throwable $th) {
            report($th);
            return back()->with('fail', config('Messages.500'));
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
        if ($request->user()->cannot('UPDATE_BOOTH', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('UPDATE_BOOTH'));
        }
        //
        $request->validate([
            'booth_number' => 'required',
            'booth_size' => 'required',
            'booth_category' => 'required',
        ]);

        ########### UPDATE SPACE SIZE ################ 
        $booth = booths::find($id);
        $partitionsSize = booths::where('bookingStatus', 1)
            ->where('pavilionID', $booth->pavilionID)
            ->sum('boothSize');
        $acuratePartition = $partitionsSize - $request->booth_size;

        $space = pavilions::find($booth->pavilionID);
        $SpaceSize = $space->pavilion_size;
        $spaceRemained = $SpaceSize - $acuratePartition;

        if (($acuratePartition + $request->booth_size) > $SpaceSize) {
            return back()->with('fail', 'Space Exceeds Maximum Space Size of ' . number_format($SpaceSize, 2) . ' SQM , Remained only ' . $spaceRemained . ' SQM');
        }
        ############## END CHECKING SPACE SIZE #######
        $booth->update([
            'boothNo' => $request->booth_number,
            'boothSize' => $request->booth_size,
            'booth_category' => $request->booth_category,
            'boothCost' => $request->booth_cost,
            'bookingStatus' => $request->partitionStatus,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
        return back()->with('success', config('Messages.200'));
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
            if ($request->user()->cannot('DELETE_BOOTH', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('DELETE_BOOTH'));
            }
            $booth = booths::find($id);
            if (booked_pavilions::where('pavilionID', $id)->exists()) {
                $booth->update([
                    'bookingStatus' => 'SUNSPENDED'
                ]);
            } else {
                $booth->delete();
            }
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
