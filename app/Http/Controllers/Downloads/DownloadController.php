<?php

namespace App\Http\Controllers\Downloads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\companyExport;
use App\Models\advice_note\advice_notes;
use App\Models\exhibition\exhibition_products;
use App\Models\off_fair\advice_notes\off_fair_advice_notes;
use App\Models\off_fair\booking\off_fair_space_bookings;
use App\Models\Permits\Permit;
use App\Models\setting\BankAccount;
use App\Models\setting\DefaultSettings;
use App\Models\settings\events;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class DownloadController extends Controller
{
    //

    public function __construct(){

        $this->middleware('auth');
    }

    function ExportCompanies(Request $request){
        if($request->user()->cannot('Export All Companies',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
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
        if($request->user()->cannot('Download Invoice',Role::class))
        {
            return back()->with('fail',config('Messages.401'));
        }

        $id=$request->id;
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
            'advice_notes.updated_at','event_name','event_descriptions','vat','total_amount','currency','control_number',
            'balance','amount_paid','account_type','registrationID','sp_invoice_no'
            ]);
        //end

        foreach ($adviceNotes as $key) {
           if($key->advice_note_deadline==null)
           {
            return back()->with('fail','Sor your required to set deadline to open advice note');
           }
        }

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
        $pdf->render();
        if(Auth::user()->account_type==2)
        {
            return $pdf->download($id.'.pdf');
        }
        else
        {
            return $pdf->stream($id);
        }
    }


    // download collection reports
    function ExportCollectionReport(Request $request){
        if($request->user()->cannot('Download Collection Report',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
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
        if($request->user()->cannot('Export Space Report',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
    }
    #Space Booking

    #Exhibitors EXport
    private function ExportExhibitors()
    {

    }
    #End Export Exhibitors

    #Download Invoice
    public function DownloadOffFairInvoice(Request $request)
    {
        $id=$request->id;

        $adviceNotes = off_fair_advice_notes::leftJoin('off_fair_customers', 'off_fair_advice_notes.advice_note_owned_by', '=', 'off_fair_customers.customer_id')
        ->leftJoin('companies','off_fair_advice_notes.advice_note_owned_by','=','companies.companyID')
        ->leftJoin('company_details','companies.companyID','=','company_details.companyID')
        ->where('off_fair_advice_note_id', $id)->get();



         $booking_dates=off_fair_space_bookings::get(['start_date','end_date','advice_note_id'])->where('advice_note_id',$id);

         $particulars=off_fair_advice_notes::leftJoin('off_fair_booking_particulars','off_fair_advice_notes.off_fair_advice_note_id','=','off_fair_booking_particulars.advice_note_id')
         ->where('off_fair_advice_note_id',$id)->get();

         $defaultSettings=DefaultSettings::get();
         $sac_officer=off_fair_advice_notes::leftJoin('users','off_fair_advice_notes.advice_note_generated_by','=','users.user_uuid')->where('off_fair_advice_note_id',$id)->first(); // sac office
         $sac_officer_name=$sac_officer->first_name." ".$sac_officer->last_name;
         $url='SAMS|'.$id;


         $inv=off_fair_advice_notes::leftJoin('users','off_fair_advice_notes.advice_note_attended_by','=','users.user_uuid')
         ->leftJoin('roles','users.role','=','roles.id')->where('off_fair_advice_note_id',$id)->first();
         $approved_by=$inv->first_name.' '.$inv->middle_name.' '.$inv->last_name; // approved by
         $title=$inv->name; $approved_at=$inv->advice_note_updated_at;

         $transfer=BankAccount::get(['account_number','account_name','swift_code','account_currency','bank_name']);
        $pdf = PDF::loadView('Off-Fair.Advice-Notes.printing-pages.DownloadableInvoice',compact('adviceNotes','particulars','booking_dates',
        'defaultSettings','sac_officer_name','url','approved_by','title','approved_at','transfer'));
                 $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed'=> true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ])
        );
        $pdf->render();
        if(Auth::user()->account_type==2)
        {
            return $pdf->download($id.'.pdf');
        }
        else
        {
            return $pdf->stream($id);
        }
        // return view('Off-Fair.Advice-Notes.printing-pages.printable-advice-note',compact('adviceNotes','particulars','booking_dates','defaultSettings'));
    }
    #Download Invoice

}
