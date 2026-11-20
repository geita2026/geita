<?php

namespace App\Http\Controllers\Exhibition;

use App\Http\Controllers\Controller;
use App\Models\exhibition\exhibitors;
use App\Models\exhibition\VirtualService;
use App\Models\settings\events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Intervention\Image\Facades\Image;

class VirtualServiceController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        try {
            if ($request->user()->cannot('View Virtual Services', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $virtual_pavilions = VirtualService::get();
            return view('Exhibition.VirtualPavilions', compact('virtual_pavilions'));
        } catch (\Throwable $th) {
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
            if ($request->user()->cannot('Create Virtual Services', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            //

            $id = session('participation_id');
            $virtual_service = VirtualService::where('participation_id', $id)->get();
            $pavilion = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
                ->where('participation_id', $id)->get();

            return view('Exhibition.AddVirtualService', compact('virtual_service', 'pavilion'));
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
        if ($request->user()->cannot('Create Virtual Services', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $id = session('participation_id');
        $request->validate([
            'service_descriptions' => 'required',
            'service_name' => 'required',
            'service_image_name' => 'mimes:jpg,png,jpeg|max:2048',
        ]);
        try {
            $image = Image::make($request->service_image_name)->resize(1600, 830);
            $image_name = 'virtualService_' . Str::uuid() . "." . $request->service_image_name->extension();
            $request->service_image_name->move(public_path('VirtualImages'), $image_name);
            #---- RESIZE IMAGE -----#
            $path = public_path('VirtualImages/');
            $image->save($path . $image_name);
            #---- RESIZE IMAGE -----#
            VirtualService::create([
                'virtual_service_id' => Str::uuid(),
                'participation_id' => $id,
                'service_descriptions' => $request->service_descriptions,
                'service_name' => $request->service_name,
                'service_image_name' => $image_name,
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
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
            if ($request->user()->cannot('View Virtual Services', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            //
            session(['participation_id' => $id]); // save participation id in session.
            $pavilion = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
                ->where('participation_id', $id)->get();
            $virtual_service = VirtualService::where('participation_id', $id)->get();
            return view('Exhibition.VirtualService', compact('virtual_service', 'pavilion'));
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
        if ($request->user()->cannot('Update Virtual Services', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        $request->validate([
            'service_name' => 'required',
            'service_descriptions' => 'required',
            '' => 'mimes:png,jpg,jpeg|max:2048' // max 2 MB file
        ]);

        try {
            $image_name = VirtualService::find($id)->service_image_name;
            if ($request->service_image_name) {
                $image = Image::make($request->service_image_name)->resize(1280, 720);
                File::delete(public_path('VirtualImages/' . $image_name));
                $image_name = Str::uuid() . '.' . $request->service_image_name->extension(); // file extension
                $request->service_image_name->move(public_path('VirtualImages'), $image_name); // move file to upload folder.
                #---- RESIZE IMAGE -----#
                $path = public_path('VirtualImages/');
                $image->save($path . $image_name);
                #---- RESIZE IMAGE -----#
            }
            VirtualService::find($id)->update([
                'service_descriptions' => $request->service_descriptions,
                'service_name' => $request->service_name,
                'service_image_name' => $image_name,
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
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('Delete Virtual Service', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        try {
            $image_name = VirtualService::find($id)->service_image_name;
            File::delete(public_path('VirtualImages/') . $image_name);
            VirtualService::find($id)->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
