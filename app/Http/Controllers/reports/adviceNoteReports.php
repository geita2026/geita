<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use Illuminate\Http\Request;

class adviceNoteReports extends Controller
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
        if($request->user()->cannot('View Invoice Reports',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        if($request->exhibition_year)
        {
            $data=advice_notes::get()->where('exhibition_year',$request->exhibition_year);
            $exhibition_year=$request->exhibition_year;
        }
        else
        {
            $exhibition_year=date('Y')-1976;
            $data=advice_notes::get()->where('exhibition_year',$exhibition_year);
        }
            $totalAdviceNote=$data->count();
            $expired=0; $unexpired=0; $submitted=0; $pending=0;
            foreach ($data as $item) {
                if ($item->advice_note_deadline<date('Y-m-d')) {
                    $expired=$expired+1;
                }
                if ($item->advice_note_deadline>=date('Y-m-d')) {
                    $unexpired=$unexpired+1;
                }
                if ($item->advice_note_status=='COMPLETE') {
                    $submitted=$submitted+1;
                }
                if ($item->advice_note_status=='PENDING') {
                    $pending=$pending+1;
                }
            }


        $adviceNotes=advice_notes::leftJoin('companies','advice_notes.company_id','=','companies.companyID')
            ->leftJoin('users','advice_notes.user_id','=','users.user_uuid')
            ->get(['advice_note_id','user_id','companyName','advice_note_created_at','companyEmail','companyPhone','exhibition_year']);

        return view('reports.advice-note-report',compact('adviceNotes','expired','unexpired','totalAdviceNote','submitted','pending','exhibition_year'));


    }

}
