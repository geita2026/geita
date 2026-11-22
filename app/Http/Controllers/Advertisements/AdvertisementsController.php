<?php

namespace App\Http\Controllers\Advertisements;

use App\Http\Controllers\Controller;
use App\Models\Advertisements\ClipsAdvertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class AdvertisementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('VIEW_ADVERTISEMENTS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_ADVERTISEMENTS'));
        }
        $advertisements=ClipsAdvertisement::orderBy('advertisement_created_at','DESC')->get();
        return view('Advertisements.advertisements',compact('advertisements'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('CREATE_ADVERTISEMENTS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_ADVERTISEMENTS'));
        }
        //
        $request->validate([
            'advertisement_title'=>'required|max:100',
            'advertisement_descriptions'=>'max:200',
            'clip'=>'required|mimes:mp4'
        ]);

        try {
            if($request->clip){
                $clip_name=Str::uuid().".".$request->clip->extension(); // composing clip name
                $request->clip->move(public_path('Advertisements'), $clip_name); // moving file to folder
            }
            else{
                $clip_name=Null;
            }

            ClipsAdvertisement::create([
                'advertisement_id'=>Str::uuid(),
                'advertisement_title'=>$request->advertisement_title,
                'advertisement_descriptions'=>$request->advertisement_descriptions,
                'clip'=>$clip_name,
                'advertising_days'=>$request->advertising_days,
                'status'=>$request->status,
            ]);
            return back()->with('success','Advert created successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong!'.$th->getMessage());
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
        if($request->user()->cannot('UPDATE_ADVERTISEMENTS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_ADVERTISEMENTS'));
        }

      $request->validate([
            'advertisement_title'=>'required|max:100',
            'advertisement_descriptions'=>'max:200',
            'file'=>'max:20480'
        ]);

        try {
            if($request->file){
                $clip_name=date('d-m-Y H:i:s').".".$request->file->extension(); // composing clip name
                File::delete(public_path('Advertisements/').$request->existing); // delete existing file in folder
                $request->file->move(public_path('Advertisements'), $clip_name); // moving file to folder
            }
            else{
                $clip_name=$request->existing;
            }

            $advert=ClipsAdvertisement::find($id);

            $advert->update([
                'advertisement_title'=>$request->advertisement_title,
                'advertisement_descriptions'=>$request->advertisement_descriptions,
                'clip'=>$clip_name,
                'advertising_days'=>$request->advertising_days,
                'status'=>$request->status,
            ]);
            return back()->with('success','Advert deleted successfully');
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
        if($request->user()->cannot('DELETE_ADVERTISEMENTS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DELETE_ADVERTISEMENTS'));
        }

        try {

            $advertisements=ClipsAdvertisement::where('advertisement_id',$id)->get();
            foreach ($advertisements as $value) {
                File::delete(public_path('Advertisements/').$value->clip); // delete file in folder
                $advert=ClipsAdvertisement::find($id);
                $advert->delete(); // delete file from database
                return back()->with('success','Advert deleted successfully');
            }

        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','somthing went wrong!');
        }
    }
}
