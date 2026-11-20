<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\exhibition\EventProgram;
use App\Traits\VistorsTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image as Image;

class PreviewProgramController extends Controller
{
    use VistorsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    { 
        $programs=EventProgram::leftJoin('events','event_programs.event','=','events.event_id')
        ->orderBy('program_date','ASC')->where('share_program',1)
        ->where('event_status',1)->get();

        $event=EventProgram::leftJoin('events','event_programs.event','=','events.event_id')
        ->where('event_status',1)->first();
        return view('FrontEnd.Public_Pages.SabasabaPrograms',compact('programs','event'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        // $this->Vistors($ip = $request->ip()); # save new vistor
        $imagePath = public_path('images/nembo.png');
        $image = Image::make($imagePath);
        $base64_nembo = 'data:image/png;base64,' . base64_encode($image->encode('png'));

        $imagePath = public_path('images/logo.jpg');
        $image = Image::make($imagePath);
        $base64_logo = 'data:image/png;base64,' . base64_encode($image->encode('png'));


        $programs=EventProgram::leftJoin('events','event_programs.event','=','events.event_id')
        ->orderBy('program_date','ASC')->where('share_program',1)
        ->where('event_status',1)->get();

        $eventName=EventProgram::leftJoin('events','event_programs.event','=','events.event_id')
        ->where('event_status',1)->first()->event_name;

        $pdf=Pdf::setOptions(['isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]);
        $pdf->loadView('Exhibition.EventPrograms.DownloadPrograms',compact('programs','eventName','base64_nembo','base64_logo'))
        ->setPaper('A4', 'portrait');

        return $pdf->stream('Ditf_Programs_'.date('d-m-Y').'.pdf');
    }


}
