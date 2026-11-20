<?php

namespace App\Http\Controllers\sponsorships;

use App\Http\Controllers\Controller;
use App\Models\sponsorships\EventSponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EventSponsorController extends Controller
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
        if($request->user()->cannot('View Event Sponsors',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $sponsors=EventSponsor::get();
        return view('sponsorship.RecoginizedSponsors',compact('sponsors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('View Event Sponsors',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $request->validate([
            'sponsor_name'=>'required',
            'sponsor_logo'=>'required|mimes:png,jpg,jpeg,svg',
            'sponsor_website'=>'required|url'
        ]);

        try {
            $logo="sponsor_logo_".Str::uuid().".".$request->sponsor_logo->extension();
            $request->sponsor_logo->move(public_path('VirtualImages'),$logo);
        EventSponsor::create([
            'sponsor_id'=>Str::uuid(),
            'sponsor_name'=>$request->sponsor_name,
            'sponsor_logo'=>$logo,
            'sponsor_website'=>$request->sponsor_website,
            'sponsorship_category'=>$request->sponsorship_category,
        ]);

        return back()->with('success','Event sponsor saved successfully');
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong!, please try again');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\sponsorships\EventSponsor  $eventSponsor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        if($request->user()->cannot('View Event Sponsors',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $request->validate([
            'sponsor_name'=>'required',
            'sponsor_logo'=>'required|mimes:png,jpg,jpeg,svg',
            'sponsor_website'=>'required|url'
        ]);

        $logo="sponsor_logo_".Str::uuid().".".$request->sponsor_logo->extension();
        $request->sponsor_logo->move(public_path('VirtualImages'),$logo);
        EventSponsor::find($id)->update([
            'sponsor_name'=>$request->sponsor_name,
            'sponsor_logo'=>$logo,
            'sponsor_website'=>$request->sponsor_website,
            'sponsorship_category'=>$request->sponsorship_category,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\sponsorships\EventSponsor  $eventSponsor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->user()->cannot('Delete Event Sponsors',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        try
        {
        $sponsor=EventSponsor::find($id);
        File::delete(public_path('VirtualImages/'.$sponsor->sponsor_logo));
        $sponsor->delete();
        return back()->with('success','Event sponsor deleted successfully!');
        } catch (\Throwable $th)
        {
          return back()->with('fail','Something went wrong!');
        }

    }
}
