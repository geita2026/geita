<?php

namespace App\Http\Controllers\Advertisements;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\Advertisements\TextAdvertisement;
use Illuminate\Support\Facades\Validator;
use App\Traits\APITraits;

class TextAdvertisementController extends Controller
{
     use APITraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('MANAGE_ADVATISMENT', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_ADVATISMENT'));
        }
        $advertisements = TextAdvertisement::get();
        return view('Advertisements.TextAdvertisements', compact('advertisements'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('MANAGE_ADVATISMENT', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_ADVATISMENT'));
        }
        $request->validate([
            'advertisement_title' => 'required',
            'advertising_days' => 'required',
            'status' => 'required',
            'imageDescription' => 'max:400'
        ]);
        try {

            $image_name = "slidingImage_" . Str::uuid() . "_." . $request->slidingImage->extension(); # if available creates new sign name 
            $request->slidingImage->move(public_path('uploadedFiles'), $image_name);
            $image_name = env('APP_URL') . '/public/uploadedFiles/' . $image_name;
            #---- RESIZE IMAGE -----#

            TextAdvertisement::create([
                'advertisement_id' => Str::uuid(),
                'advertisement_title' => $request->advertisement_title,
                'advertisement_link' => $request->advertisement_link,
                'advertising_days' => $request->advertising_days,
                'status' => $request->status,
                'slidingImage' => $image_name,
                'imageDescription' => $request->imageDescription,
            ]);
            return back()->with('success', config('Messages.200'));
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
        if ($request->user()->cannot('MANAGE_ADVATISMENTS', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_ADVATISMENTS'));
        }

        try {
            $request->validate([
                'advertisement_title' => 'required',
                'advertising_days' => 'required',
                'status' => 'required',
            ]);

            $old = TextAdvertisement::find($id);
            $image_name = $old->slidingImage ?? Null; # if available creates new sign name 
            if ($request->slidingImage) {
                if ($old->slidingImage != Null) {
                    $oldImage = str_replace(env('APP_URL'), '', $image_name);
                    File::delete(public_path('uploadedFiles/' . $oldImage));
                }
                $image_name = "slidingImage_" . Str::uuid() . "." . $request->slidingImage->extension(); # if available creates new sign name 
                $request->slidingImage->move(public_path('uploadedFiles'), $image_name);
                $image_name = env('APP_URL') . '/public/uploadedFiles/' . $image_name;
                #---- RESIZE IMAGE -----#
            }

            TextAdvertisement::find($id)->update([
                'advertisement_title' => $request->advertisement_title,
                'advertisement_link' => $request->advertisement_link,
                'advertising_days' => $request->advertising_days,
                'status' => $request->status,
                'slidingImage' => $image_name,
                'imageDescription' => $request->imageDescription,
            ]);
            return back()->with('success', config('Messages.200'));
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
    public function destroy(Request $Request, $id)
    {
        try {
            $old = TextAdvertisement::find($id);
            if ($old->slidingImage != Null) {
                File::delete(public_path('VirtualImages/' . $old->slidingImage));
            }
            $old->delete();
            return back()->with('success', 'Advertisements deleted successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong!');
        }
    }

    public function getAppAdvert(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'spCode' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $status = $this->AuthenticateRequest($request->spCode, $request->token);
            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 404);
            }
            $data = TextAdvertisement::where('status', 2)->get([
                'advertisement_id',
                'advertisement_title',
                'advertisement_link',
                'slidingImage',
                'imageDescription',
            ]);
            return response()->json([
                'status' => 1,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('Messages.500')
            ]);
        }
    }
}
