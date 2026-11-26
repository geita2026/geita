<?php

namespace App\Http\Controllers\Exhibition;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\exhibition\exhibitors;
use App\Models\exhibition\VirtualFiles;
use App\Models\exhibition\VirtualPavilion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Intervention\Image\Facades\Image;

class VirtualPavilionController extends Controller
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
        if($request->user()->cannot('VIEW_VIRTUAL_PAVILIONS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_VIRTUAL_PAVILIONS'));
        }

        try {
            #------------------------- INTERNAL USERS -------------------#
            if(Auth::user()->account_type==1)
            {
                $virtual_pavilions=exhibitors::leftJoin('companies','exhibitor_id','=','companies.companyID')
                ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
                ->where('event_status',1)->get(['companyName','participation_id','pavilion_descriptions','pavilion_image_name','visibility_status','exhibitor_id']);
            }
            #-------------------------END INTERNAL USERS -------------------#

            #------------------------- EXTERNAL USERS -------------------#
            if(Auth::user()->account_type==2)
            {
             $virtual_pavilions=exhibitors::leftJoin('companies','exhibitor_id','=','companies.companyID')
            ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
            ->where('event_status',1)->where('exhibitor_id',Auth::user()->myCompany)
            ->get(['companyName','participation_id','pavilion_descriptions','pavilion_image_name','visibility_status','exhibitor_id']);
            }
            #-------------------------END EXTERNAL USERS -------------------#

            return view('Exhibition.VirtualPavilions',compact('virtual_pavilions'));
        } catch (\Throwable $th) {
            return back()->with('success','Something went wrong, please try again!');
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
        if($request->user()->cannot('CREATE_VIRTUAL_PAVILIONS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('CREATE_VIRTUAL_PAVILIONS'));
        }
        //
       $id=session('participation_id');

        $request->validate([
            'pavilion_descriptions'=>'required',
            'welcome_word'=>'required',
            'pavilion_image_name'=>'mimes:jpg,png,jpeg|max:2048',
        ]);


        try {
            $existing_images=exhibitors::where('participation_id',$id)->get();
               foreach ($existing_images as  $value) {
               $image_name=$value->pavilion_image_name;
                 }

            if (!empty($request->pavilion_image_name)) {
                $image = Image::make($request->pavilion_image_name)->resize(1280, 820);
                File::delete(public_path('VirtualImages/'.$image_name)); // delete existing file in upload file
                $image_name =Str::uuid().".".$request->pavilion_image_name->extension();
                $request->pavilion_image_name->move(public_path('VirtualImages'), $image_name);
            #---- RESIZE IMAGE -----#
            $path = public_path('VirtualImages/');
            $image->save($path . $image_name);
            #---- RESIZE IMAGE -----#
            }


        exhibitors::find($id)->update([
            'pavilion_descriptions'=>$request->pavilion_descriptions,
            'welcome_word'=>$request->welcome_word,
            'pavilion_image_name'=>$image_name,
            'visibility_status'=>1,
        ]); // updating details in database

        return back()->with('success','Pavilion profile created successfully');
        } catch (\Throwable $th) {
           return back()->with('fail','Something went wrong!');
        }

    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if($request->user()->cannot('UPDATE_VIRTUAL_PAVILIONS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_VIRTUAL_PAVILIONS'));
        }
        //
        session(['participation_id'=>$id]);
        $virtual_pavilions=companies::leftJoin('exhibitors','companies.companyID','exhibitors.exhibitor_id')
        ->where('participation_id',$id)->get();
        $reference=exhibitors::find($id)->exhibitor_id;
        $images=VirtualFiles::where('reference_id',$reference)->get();

        return view('Exhibition.EditVirtualPavilion',compact('virtual_pavilions','images'));
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
        if($request->user()->cannot('UPDATE_VIRTUAL_PAVILIONS',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('UPDATE_VIRTUAL_PAVILIONS'));
        }

        // return $request;
        $request->validate([
            'pavilion_image'=>'required|mimes:png,jpg,gif,jpeg',
            'image_description'=>'required|max:100'
        ]);


        try {
            $reference_id=exhibitors::find(session('participation_id'))->exhibitor_id;
            $image_id=Str::uuid();
            $image_name=$image_id.'.'.$request->pavilion_image->extension();

        VirtualFiles::create([
            'image_id'=>$image_id,
            'image_name'=>$image_name,
            'reference_id'=>$reference_id,
            'destination'=>1,
            'image_description'=>$request->image_description
        ]);
        $request->pavilion_image->move(public_path('VirtualImages'),$image_name); // save image to Folder
        return back()->with('success','Image saved successfully!');
        } catch (\Throwable $th) {
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
        // if($request->user()->cannot('DELETE_VIRTUAL_PAVILIONS',Role::class))
        // {
        //     return back()->with('fail','You do not have permission to perform this Action');
        // }
        //
        try {
            {
                $image=VirtualFiles::find($id);
                if(File::delete(public_path('VirtualImages/'.$image->image_name)))
                {
                    $image->delete();
                    return back()->with('success','Pavilion Image deleted successfully!');
                }
                else
                {
                    return back()->with('fail','Image does not exist!');
                }
            }

        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong!');
        }
    }
}
