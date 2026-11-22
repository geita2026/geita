<?php

namespace App\Http\Controllers\space;

use App\Http\Controllers\Controller;
use App\Models\PriceConfiguration;
use Illuminate\Http\Request;
use App\Models\space\space_categories;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\settings\events;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Intervention\Image\Facades\Image;

class spaceCategoryController extends Controller
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
            //
            $categories = space_categories::leftJoin('events', 'space_categories.category_event', '=', 'events.event_id')
                ->orderByDesc('events.event_start_date')
                ->orderBy('space_categories.category_name')
                ->get(['space_categories.*', 'events.event_name']);
            return view('space.space-category', compact('categories'));
        } catch (\Throwable $th) {
            report($th);

            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            if ($request->user()->cannot('MANAGE_SPACES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPACES'));
            }
            $events = events::orderBy('event_status', 'DESC')->orderBy('event_start_date', 'DESC')->get();
            return view('space.add-space-category', compact('events'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
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
                'category_name' => 'required',
                'cost_one_msq' => 'numeric|min:0',
                'type_of_currency' => 'required',
                'event_name' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->with('fails', implode(',', $validator->getMessageBag()->all()));
            }

            $categoryPoster = Null;
            if ($request->categoryPoster) {
                $validator = Validator::make($request->all(), [
                    'categoryPoster' => 'required|mimes:png,jpg,jpeg'
                ]);
                if ($validator->fails()) {
                    return back()->with('fails', implode(',', $validator->getMessageBag()->all()));
                }
                $fileName =  'category_poster' . date('shdmY') . '.' . $request->categoryPoster->extension();
                $path = public_path('uploadedFiles/');
                $image = Image::make($request->categoryPoster)->resize(1000, 500);
                $image->save($path . $fileName);
                $categoryPoster = env('APP_URL') . '/public/uploadedFiles/' . $fileName;
            }
            space_categories::create([
                'category_id' => Str::uuid(),
                'user_id' => Auth::user()->user_uuid,
                'category_name' => $request->category_name,
                'cost_one_msq' => $request->cost_one_msq,
                'type_of_currency' => $request->type_of_currency,
                'category_event' => $request->event_name,
                'background_color' => 'bg-info',
                'category_descriptions' => $request->category_descriptions,
                'category_icon' => $categoryPoster,
            ]);
            return redirect()->route('spaceCategories.index')->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('MANAGE_SPACES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPACES'));
            }
            $events = events::orderBy('event_status', 'DESC')->orderBy('event_start_date', 'DESC')->get();
            $categories = space_categories::leftJoin('events', 'space_categories.category_event', '=', 'events.event_id')
                ->get(['category_id', 'category_name', 'cost_one_msq', 'type_of_currency', 'event_name', 'category_event', 'category_descriptions', 'category_icon'])
                ->where('category_id', $id);
            return view('space.edit-space-category', compact('categories', 'events'));
        } catch (\Throwable $th) {
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
        try {
            if ($request->user()->cannot('MANAGE_SPACES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SPACES'));
            }
            //
            $validator = Validator::make($request->all(), [
                'category_name' => 'required',
                'cost_one_msq' => 'required|numeric|min:0',
                'type_of_currency' => 'required',
                'event_name' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->with('fails', implode(',', $validator->getMessageBag()->all()));
            }

            $category = space_categories::find($id);
            $categoryPoster = $category->category_icon ?? Null;
            if ($request->categoryPoster) {
                $validator = Validator::make($request->all(), [
                    'categoryPoster' => 'required|mimes:png,jpg,jpeg'
                ]);
                if ($validator->fails()) {
                    return back()->with('fails', implode(',', $validator->getMessageBag()->all()));
                }
                $img = str_replace(env('APP_URL') . '/public/uploadedFiles/','',$categoryPoster);
                $deletePath = public_path('uploadedFiles/' . $img);
                File::delete($deletePath);
                $fileName =  'category_poster_' . date('shdmY') . '.' . $request->categoryPoster->extension();
                $path = public_path('uploadedFiles/');
                $image = Image::make($request->categoryPoster)->resize(1000, 500);
                $image->save($path . $fileName);
                $categoryPoster = env('APP_URL') . '/public/uploadedFiles/' . $fileName;
            }
            $category->update([
                'user_id' => Auth::user()->user_uuid,
                'category_name' => $request->category_name,
                'cost_one_msq' => $request->cost_one_msq,
                'type_of_currency' => $request->type_of_currency,
                'category_event' => $request->event_name,
                'background_color' => 'bg-info',
                'category_icon' => $categoryPoster,
                'category_descriptions' => $request->category_descriptions,
            ]);
            if (!PriceConfiguration::where('eventId', session('liveEvent'))->where('referenceId', $id)->exists()) {
                PriceConfiguration::create([
                    'trackingId' => Str::uuid(),
                    'referenceId' => $id,
                    'configurationName' => $request->category_name,
                    'configDescription' => $request->category_name,
                    'configCost' => $request->cost_one_msq,
                    'currency'=>$request->type_of_currency,
                    'eventId' => session('liveEvent')
                ]);
            }
            return redirect()->route('spaceCategories.index')->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
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
            if ($request->user()->cannot('DELETE_EXHIBITION_SPACE_CATEGORY', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('DELETE_EXHIBITION_SPACE_CATEGORY'));
            }
            $category = space_categories::find($id);
            $img = str_replace(env('APP_URL') . '/public/uploadedFiles/','',$category->category_icon);
            $deletePath = public_path('uploadedFiles/' . $img);
            File::delete($deletePath);
            $category->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
