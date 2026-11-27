<?php

namespace App\Http\Controllers\space;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\sectors;
use App\Models\settings\events;
use App\Models\space\AreaDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\space\pavilions;
use App\Models\space\space_categories;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Intervention\Image\Facades\Image;

class pavilionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->user()->cannot('MANAGE_SPACES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPACES'));
        }

        $pavilions = pavilions::orderBy('pavilions.pavilionName')
            ->orderBy('pavilions.pavilionID')
            ->leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
            ->leftJoin('streets', 'pavilions.spaceStreet', '=', 'streets.streetId')
            ->where('space_status', '!=', 'SUNSPENDED')
            ->paginate(10, [
                'pavilionID',
                'pavilionName',
                'category_name',
                'pavilionRate',
                'pavilionDescription',
                'cost_one_msq',
                'pavilion_size',
                'space_status',
                'type_of_currency',
                'streetName',
                'numberOfPartitions'
            ]);

        $events = events::get();

        return view('space.pavilions', compact('pavilions', 'events'));
    }

    public function create(Request $request)
    {
        $search = $request->get('q');

        if ($request->user()->cannot('MANAGE_SPACES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPACES'));
        }

        $categories = space_categories::orderBy('category_name', 'ASC')
            ->get(['category_id', 'category_name', 'category_event']);

        if ($search != null) {
            $data = pavilions::orderBy('pavilionName', 'ASC')
                ->leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
                ->leftJoin('streets', 'pavilions.spaceStreet', '=', 'streets.streetId')
                ->where('space_status', '!=', 'SUNSPENDED')
                ->where(function ($query) use ($search) {
                    $query->orWhere('pavilionName', 'LIKE', '%' . $search . '%');
                    $query->orWhere('category_name', 'LIKE', '%' . $search . '%');
                })
                ->limit(10)
                ->get([
                    'pavilionID',
                    'pavilionName',
                    'category_name',
                    'pavilionRate',
                    'pavilionDescription',
                    'cost_one_msq',
                    'pavilion_size',
                    'space_status',
                    'type_of_currency',
                    'streetName',
                    'numberOfPartitions'
                ]);

            return response()->json($data);
        }

        $sectors = sectors::get();

        return view('space.add-pavilion', compact('categories', 'sectors'));
    }

    public function store(Request $request)
    {
        try {
            if ($request->user()->cannot('MANAGE_SPACES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPACES'));
            }

            $this->normalizePavilionNumbers($request);

            $validator = Validator::make($request->all(), [
                'pavilion_name' => 'required',
                'pavilion_category' => 'required',
                'pavilion_size' => 'numeric|min:1',
                'numberOfPartitions' => 'nullable|integer|min:0|max:2147483647',
                'pavilion_description' => 'max:2000',
                'floor_plan' => 'mimes:pdf,jpg,png,jpeg,gif',
                'space_status' => 'required',
                'participants' => 'nullable|array',
                'participants.*' => 'exists:sectors,sectorID',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $floorPlan = null;

            if ($request->floorPlan) {
                $validator = Validator::make($request->all(), [
                    'floorPlan' => 'required|mimes:png,jpg,jpeg'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $fileName = $request->pavilionName . Str::uuid() . '.' . $request->floorPlan->extension();
                $path = public_path('uploadedFiles/');

                $image = Image::make($request->floorPlan)->resize(1000, 500);
                $image->save($path . $fileName);

                $floorPlan = env('APP_URL') . '/public/uploadedFiles/' . $fileName;
            }

            $pavilionId = Str::uuid();

            pavilions::create([
                'pavilionID' => $pavilionId,
                'userID' => Auth::user()->user_uuid,
                'pavilionName' => $request->pavilion_name,
                'pavilionCategory' => $request->pavilion_category,
                'pavilionDescription' => $request->pavilion_description,
                'pavilion_size' => $request->pavilion_size,
                'pavilionRate' => $request->space_rate,
                'space_status' => $request->space_status,
                'floor_plan' => $floorPlan,
                'spaceStreet' => $request->streetName,
                'numberOfPartitions' => $request->numberOfPartitions,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            $spaceSectors = [];

            if ($request->participants) {
                foreach ($request->participants as $data) {
                    AreaDistribution::create([
                        'distributionId' => Str::uuid(),
                        'spaceId' => $pavilionId,
                        'participants' => $data
                    ]);

                    $sectorName = sectors::where('sectorID', $data)->first()->sectorName;
                    array_push($spaceSectors, $sectorName);
                }

                pavilions::where('pavilionID', $pavilionId)->first()->update([
                    'allowedForBooking' => implode(', ', $spaceSectors)
                ]);
            }

            return redirect()->route('pavilions.index')->with('success', config('Messages.200'));

        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('MANAGE_SPACES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPACES'));
        }

        session(['event_id' => $id]);

        $pavilions = pavilions::leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
            ->leftJoin('events', 'pavilions.event', '=', 'events.event_id')
            ->where('event', $id)
            ->get([
                'pavilionID',
                'pavilionName',
                'category_name',
                'pavilionRate',
                'pavilionDescription',
                'cost_one_msq',
                'pavilion_size',
                'space_status',
                'type_of_currency',
                'event',
                'event_name',
                'whoCanBook',
                'companyName',
                'numberOfPartitions',
                'pavilions.latitude',
                'pavilions.longitude',
            ]);

        $events = events::get()->where('event_id', $id);

        return view('space.pavilions', compact('pavilions', 'events'));
    }

    public function edit(Request $request, $id)
    {
        if ($request->user()->cannot('MANAGE_SPACES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPACES'));
        }

        $pavilions = pavilions::leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
            ->leftJoin('companies', 'pavilions.whoCanBook', '=', 'companies.companyID')
            ->where('pavilionID', $id)
            ->get([
                'pavilionID',
                'pavilionName',
                'TotalBooths',
                'pavilionDescription',
                'category_name',
                'category_id',
                'pavilion_size',
                'pavilionCategory',
                'pavilionRate',
                'space_status',
                'floor_plan',
                'whoCanBook',
                'companyName',
                'numberOfPartitions',
            ]);

        abort_if($pavilions->isEmpty(), 404);

        $categories = space_categories::orderBy('category_name', 'ASC')
            ->get(['category_id', 'category_name', 'category_event']);

        $sectors = sectors::get();

        $selectedSectors = AreaDistribution::where('spaceId', $id)
            ->get(['participants']);

        return view('space.edit-pavilion', compact('pavilions', 'categories', 'sectors', 'selectedSectors'));
    }

    public function update(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('MANAGE_SPACES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPACES'));
            }

            $this->normalizePavilionNumbers($request);

            $validator = Validator::make($request->all(), [
                'pavilion_name' => 'required',
                'pavilion_category' => 'required',
                'pavilionDescription' => 'max:2000',
                'pavilion_size' => 'required|numeric|min:1',
                'numberOfPartitions' => 'nullable|integer|min:0|max:2147483647',
                'participants' => 'nullable|array',
                'participants.*' => 'exists:sectors,sectorID',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            AreaDistribution::where('spaceId', $id)->delete();

            $spaceSectors = [];

            if ($request->participants) {
                foreach ($request->participants as $data) {
                    AreaDistribution::create([
                        'distributionId' => Str::uuid(),
                        'spaceId' => $id,
                        'participants' => $data
                    ]);

                    $sectorName = sectors::where('sectorID', $data)->first()->sectorName;
                    array_push($spaceSectors, $sectorName);
                }
            }

            $pavilion = pavilions::find($id);
            $floorPlan = $pavilion->floor_plan;

            if ($request->floorPlan) {
                $validator = Validator::make($request->all(), [
                    'floorPlan' => 'required|mimes:png,jpg,jpeg'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $img = str_replace(env('APP_URL') . '/public/uploadedFiles/', '', $floorPlan);
                $deletePath = public_path('uploadedFiles/' . $img);

                File::delete($deletePath);

                $fileName = str_replace(' ', '', $request->pavilion_name) . date('dmy') . '.' . $request->floorPlan->extension();

                $request->floorPlan->move(public_path('uploadedFiles'), $fileName);

                $floorPlan = env('APP_URL') . '/public/uploadedFiles/' . $fileName;
            }

            $pavilion->update([
                'userID' => Auth::user()->user_uuid,
                'pavilionName' => $request->pavilion_name,
                'pavilionCategory' => $request->pavilion_category,
                'pavilionDescription' => $request->pavilionDescription,
                'pavilion_size' => $request->pavilion_size,
                'space_status' => $request->space_status,
                'spaceStreet' => $request->input('streetName', $pavilion->spaceStreet),
                'floor_plan' => $floorPlan,
                'whoCanBook' => $request->whoCanBook,
                'allowedForBooking' => implode(', ', $spaceSectors),
                'numberOfPartitions' => $request->numberOfPartitions,
                'latitude' => $request->input('latitude', $pavilion->latitude),
                'longitude' => $request->input('longitude', $pavilion->longitude),
            ]);

            return redirect()->route('pavilions.index')->with('success', config('Messages.200'));

        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('DELETE_PAVILION', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('DELETE_PAVILION'));
            }

            $pavilion = pavilions::find($id);

            $img = str_replace(env('APP_URL') . '/public/uploadedFiles/', '', $pavilion->floor_plan);
            $deletePath = public_path('uploadedFiles/' . $img);

            File::delete($deletePath);

            $pavilion->update([
                'space_status' => 'SUNSPENDED',
            ]);

            return back()->with('success', config('Messages.200'));

        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    private function normalizePavilionNumbers(Request $request): void
    {
        foreach (['pavilion_size', 'numberOfPartitions'] as $field) {
            $value = $request->input($field);

            if (
                is_string($value) &&
                preg_match('/^\d{1,3}(?:,\d{3})+(?:\.\d+)?$/D', $value)
            ) {
                $request->merge([
                    $field => str_replace(',', '', $value)
                ]);
            }
        }
    }
}
