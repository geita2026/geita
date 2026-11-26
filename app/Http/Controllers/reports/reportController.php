<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use Illuminate\Http\Request;
use App\Models\companies;
use App\Models\exhibition\exhibitors;
use App\Models\sectors;
use App\Models\space\booths;
use App\Models\space\pavilions;
use phpDocumentor\Reflection\Types\Null_;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;
use Spatie\Permission\Models\Role;

class reportController extends Controller
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
        if($request->user()->cannot('VIEW_ALL_REPORT',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_ALL_REPORT'));
        }
        //
        $companies=companies::get('companyID')->count();//count number of companies
        $exhibitors=exhibitors::get('exhibitor_id')->count();
        // sum the size of ground space
        $spaces=pavilions::get('pavilion_size');
        $size=0;
        foreach ($spaces as $value) {
            $size=$size+$value->pavilion_size;
        }
        //end of summing
        $booked_size1=0;
        $b1=pavilions::get(['bookingStatus','pavilion_size'])->where('bookingStatus',1);
        foreach($b1 as $i)
        {
            $booked_size1=$booked_size1+$i->pavilion_size;
        }

        $b2=booths::get(['bookingStatus','boothSize'])->where('bookingStatus','partialBooked');
        $booked_size2=0;
        foreach($b2 as $j)
        {
            $booked_size2=$booked_size2+$j->boothSize;
        }
        $booked=$booked_size1+$booked_size2;
        $shared=$booked_size2;
        $stand_alone=$booked_size1;
        $adviceNoteCount=advice_notes::get()->count();//counting number of advice notes
        $attended=advice_notes::get()->where('advice_note_status','ATTENDED')->count(); // attended advice note
        $pending=advice_notes::get()->where('advice_note_status','PENDING')->count();// PENDING advice note
        $canceled=advice_notes::get()->where('advice_note_status','CANCELED')->count(); //canceled advice note

        return view('reports.report',compact('companies','size','adviceNoteCount','booked',
        'exhibitors','shared','stand_alone','pending','canceled','attended'));
    }

}
