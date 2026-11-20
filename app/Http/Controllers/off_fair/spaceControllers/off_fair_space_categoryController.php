<?php

namespace App\Http\Controllers\off_fair\spaceControllers;

use App\Http\Controllers\Controller;
use App\Models\off_fair\spaces\off_fair_space_categories;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Intervention\Image\Facades\Image;

class off_fair_space_categoryController extends Controller
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
        if ($request->user()->cannot('Manage Spaces', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        $categories = off_fair_space_categories::get();
        return view('Off-Fair.off_fair_spaces.off_fair_space_categories', compact('categories'));
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
            if ($request->user()->cannot('Manage OffFairSpaces', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $validator = Validator::make($request->all(), [
                'space_category_name' => 'required|unique:off_fair_space_categories,category_name',
                'category_cost' => 'numeric',
                'category_descriptions' => 'max:100',
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
            off_fair_space_categories::create([
                'space_category_id' => Str::uuid(),
                'category_name' => $request->space_category_name,
                'category_created_by' => Auth::user()->user_uuid,
                'space_category_cost' => $request->category_cost,
                'categoryPoster' => $categoryPoster,
                'category_descriptions' => $request->category_descriptions,
                'populationLimit' => $request->populationLimit,
            ]);
            return back()->with('success', 'Space Category registered successfully!');
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
            if ($request->user()->cannot('Update Off Fair Space Categories', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }

            $validator = Validator::make($request->all(), [
                'space_category_name' => 'required',
                'category_cost' => 'numeric',
                'category_descriptions' => 'max:100',
            ]);

            if ($validator->fails()) {
                return back()->with('fails', implode(',', $validator->getMessageBag()->all()));
            }
            $category = off_fair_space_categories::find($id);
            $categoryPoster = $category->categoryPoster ?? Null;
            if ($request->categoryPoster) {
                $validator = Validator::make($request->all(), [
                    'categoryPoster' => 'required|mimes:png,jpg,jpeg'
                ]);
                if ($validator->fails()) {
                    return back()->with('fails', implode(',', $validator->getMessageBag()->all()));
                }
                $img = substr($categoryPoster, -32);
                $deletePath = public_path('uploadedFiles/' . $img);
                File::delete($deletePath);
                $fileName =  'category_poster_' . date('shdmY') . '.' . $request->categoryPoster->extension();
                $path = public_path('uploadedFiles/');
                $image = Image::make($request->categoryPoster)->resize(1000, 500);
                $image->save($path . $fileName);
                $categoryPoster = env('APP_URL') . '/public/uploadedFiles/' . $fileName;
            }
            $category->update([
                'category_name' => $request->space_category_name,
                'category_created_by' => Auth::user()->user_uuid,
                'space_category_cost' => $request->category_cost,
                'category_descriptions' => $request->category_descriptions,
                'categoryPoster' => $categoryPoster,
                'populationLimit' => $request->populationLimit,
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
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
        if ($request->user()->cannot('Delete Space', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        $category = off_fair_space_categories::find($id);
        $img = substr($category->categoryPoster, -32);
        $deletePath = public_path('uploadedFiles/' . $img);
        File::delete($deletePath);
        $deleted = $category->delete();
        if ($deleted) {
            return back()->with('success', config('Messages.200'));
        } else {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
