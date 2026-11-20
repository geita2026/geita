<?php

namespace App\Http\Controllers\Exhibition;

use App\Http\Controllers\Controller;
use App\Models\exhibition\EventProgram;
use App\Models\settings\events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Intervention\Image\Facades\Image;

class EventProgramController extends Controller
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
            if ($request->user()->cannot('View EventPrograms', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $programs = EventProgram::leftJoin('events', 'event_programs.event', '=', 'events.event_id')
                ->where('event_status', 1)->get();
            $eventName = events::where('event_status', 1)->first()->event_name;
            return view('Exhibition.EventPrograms.Programs', compact('programs', 'eventName'));
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
        if ($request->user()->cannot('Create EventPrograms', Role::class)) {
            return back()->with('fail', config('Messages.401'));
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
        if ($request->user()->cannot('Create EventPrograms', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $request->validate([
            'program_title' => 'required',
            'program_description' => 'required',
            'programPoster' => 'mimes:png,jpeg,jpg',
            'program_date' => 'required',
            'characters' => 'required',
        ]);
        try {
            $posterUrl=Null;
            $event = events::where('event_status', 1)->first()->event_id;
            if ($request->programPoster) {
                $fileName = 'event_program' . Str::uuid() . '.' . $request->programPoster->extension();
                $image = Image::make($request->programPoster)->resize(1200, 600);
                $path = public_path('uploadedFiles/');
                $image->save($path . $fileName);
                $posterUrl=env('APP_URL').'/public/uploadedFiles/'.$fileName;
            }
            EventProgram::create([
                'program_id' => Str::uuid(),
                'program_title' => $request->program_title,
                'program_description' => $request->program_description,
                'program_date' => $request->program_date,
                'characters' => $request->characters,
                'program_file'=>$posterUrl,
                'event' => $event
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500').$th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\exhibition\EventProgram  $eventProgram
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('View Event Programs', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\exhibition\EventProgram  $eventProgram
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('Update Event Programs', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $request->validate([
            'program_title' => 'required',
            'program_description' => 'required',
            'program_file' => 'mimes:png,jpeg,jpg',
            'program_date' => 'required',
            'characters' => 'required',
        ]);
        try {
            $event = events::where('event_status', 1)->first()->event_id;
           $program= EventProgram::find($id);
           $posterUrl=$program->program_file ?? Null;
           if ($request->programPoster) {
                $imageDelete=str_replace(env('APP_URL').'/public/uploadedFiles/','',$posterUrl);
                $imagePath=public_path('uploadedFiles/').$imageDelete;
                File::delete($imagePath);
                $fileName = 'event_program' . Str::uuid() . '.' . $request->programPoster->extension();
                $image = Image::make($request->programPoster)->resize(1000, 500);
                $path = public_path('uploadedFiles/');
                $image->save($path . $fileName);
                $posterUrl=env('APP_URL').'/public/uploadedFiles/'.$fileName;
            }

           $program->update([
                'program_title' => $request->program_title,
                'program_description' => $request->program_description,
                'program_date' => $request->program_date,
                'characters' => $request->characters,
                'event' => $event,
                'share_program' => $request->share_program,
                'program_file'=>$posterUrl,
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500').$th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\exhibition\EventProgram  $eventProgram
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('Delete Event Programs', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        try {
            $program = EventProgram::find($id);
            $program->delete();
            return back()->with('success', 'Program deleted successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong!' . $th->getMessage());
        }
    }
}
