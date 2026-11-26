<?php

namespace App\Http\Controllers\Downloads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\companyExport;
use App\Models\advice_note\advice_notes;
use App\Models\setting\BankAccount;
use App\Models\setting\DefaultSettings;
use App\Models\settings\events;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Permission\Models\Role;

class DownloadController extends Controller
{
    //

    public function __construct(){

        $this->middleware('auth');
    }

    function ExportCompanies(Request $request){
        if($request->user()->cannot('EXPORT_ALL_COMPANIES',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('EXPORT_ALL_COMPANIES'));
        }
        return Excel::download(new companyExport, 'companies.xlsx');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function DownloadInvoice(Request $request){
        $id = $request->validate(['id' => 'required|string'])['id'];
        $invoice = advice_notes::findOrFail($id, ['advice_note_id', 'company_id', 'advice_note_status']);
        abort_unless($invoice->canDownload($request->user()), 403);

        //advice note details
        $adviceNotes=advice_notes::leftJoin('companies','companies.companyID','=','advice_notes.company_id')
        ->leftJoin('users','advice_notes.user_id','=','users.user_uuid')
        ->leftJoin('roles','users.role','=','roles.id')
        ->leftJoin('events','advice_notes.event','=','events.event_id')
        ->where('advice_notes.advice_note_id',$id)
        ->get([
            'advice_notes.advice_note_id','companyName','advice_note_created_at','companyAddress','companyLocation',
            'companyPhone','personalPhone','first_name','last_name','companyEmail','roles.name','advice_note_deadline'
            ,'advice_note_description','tinNo','users.user_signature','registrationNo','advice_note_status',
            'event_name','event_descriptions','vat','total_amount','currency','control_number',
            'balance','amount_paid','account_type','registrationID','sp_invoice_no'
            ]);
        //end

        // Item  booked
        $particulars=advice_notes::leftJoin('particulars','advice_notes.advice_note_id','=','particulars.advice_note_id')
        ->leftJoin('companies','advice_notes.company_id','=','companies.companyID')
        ->where('advice_notes.advice_note_id',$id)
        ->get( ['particulars.advice_note_id','companyName','particular_name','cost_for_one_particular',
             'particular_quantity','type_of_currency','particular_category','particular_description','particular_type','contain_vat']);// end

           $url='SAMS|'.$id;
           $settings=DefaultSettings::get(['invoice_other_information']);


           $inv=advice_notes::leftJoin('companies','companies.companyID','=','advice_notes.company_id')
        ->leftJoin('company_details','advice_notes.company_id','=','company_details.companyID')
        ->leftJoin('users','advice_notes.accountant_id','=','users.user_uuid')
        ->leftJoin('roles','users.role','=','roles.id')
        ->where('advice_notes.advice_note_id',$id)->first();
        $sac_officer=advice_notes::leftJoin('users','advice_notes.user_id','=','users.user_uuid')->where('advice_note_id',$id)->first(); // sac office
        $sac_officer_name=$sac_officer->first_name." ".$sac_officer->last_name;

        $approved_by=$inv->first_name.' '.$inv->middle_name.' '.$inv->last_name; // approved by
        $title=$inv->name; $approved_at=$inv->approved_at;



        #*******************
        $transfer=BankAccount::where('bankAccountStatus','ACTIVE')->inRandomOrder()->get();
        
        
        $pdf = PDF::loadView('advice_note.downloadableinvoice',compact('adviceNotes','particulars','url','approved_by','title',
        'approved_at','settings','sac_officer_name','transfer'));
                 $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed'=> true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ])
        );
        return $pdf->download($id.'.pdf');
    }


    // download collection reports
    function ExportCollectionReport(Request $request){
        if($request->user()->cannot('DOWNLOAD_COLLECTION_REPORT',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DOWNLOAD_COLLECTION_REPORT'));
        }
            $from_date=session('from_date');
            $to_date=session('to_date');
            $event_id=session('event_id');

        $events=events::orderBy('event_start_date','DESC')->get(); // get all events

        $eventName=events::where('event_id',$event_id)->get();
        $event_name='';
        foreach($eventName as $value){ $event_name=$value->event_name; }

        $bills=advice_notes::where('event',$event_id)->where('advice_note_status','!=','CANCELED')
        ->whereBetween('advice_note_created_at',[$from_date." 00:00:00",$to_date." 23:59:59"])
        ->get(['total_amount','amount_paid','currency','total_vat_amount']);

        $nvoiceCount=advice_notes::where('event',$event_id)->where('advice_note_status','!=','CENCELED')
        ->whereBetween('advice_note_created_at',[$from_date." 00:00:00",$to_date." 23:59:59"])->count();

        $report=PDF::loadView('reports.Exports.collection_excel',compact('events','event_name','from_date','to_date','bills','event_id','nvoiceCount'))->setPaper('A4','potrait');
        $report->render();
        // return $report->stream();
        return $report->download("collection_report_".date('M_d_Y_h_i').'.xlsx');
    }


    #Space Booking Reprot
    function ExportSpaceReport(Request $request){
        if($request->user()->cannot('EXPORT_SPACE_REPORT',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('EXPORT_SPACE_REPORT'));
        }
    }
    #Space Booking

    #Exhibitors EXport
    private function ExportExhibitors()
    {

    }
    #End Export Exhibitors
}
