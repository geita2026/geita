<?php

namespace App\Http\Controllers\advice_note;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\Companies\CompanyAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class CanceledInvoiceController extends Controller
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
        if($request->user()->cannot('View Canceled Invoice',Role::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        //
         #---------------------------------- INTERNAL USERS--------------------------#
         if (Auth::user()->account_type == 1) {
            if ($request->user()->cannot('View Canceled Invoice', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }

            $advice_note = advice_notes::leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
                ->leftJoin('canceled_advice_notes','advice_notes.advice_note_id','=','canceled_advice_notes.advice_note_id')
                ->leftJoin('users','canceled_advice_notes.canceled_by','=','users.user_uuid')
                ->where('advice_note_status', '=', 'CANCELED')
                ->orderBy('canceled_at', 'DESC')
                ->paginate(config('global.DATA_LIMIT'),[
                    'company_id', 'advice_notes.advice_note_id', 'companyName', 'advice_note_deadline', 'invoice_type',
                    'advice_note_status', 'advice_note_description', 'advice_note_created_at', 'registrationID', 'companyLocation',
                    'companyPhone', 'control_number', 'balance', 'total_amount', 'currency', 'amount_paid','canceled_at',
                    'first_name','last_name','reasons'
                ]);

            //END CURRENT EVENT INVOICES
        
        }
        #----------------------------END INTERNAL USERS-----------------------------------#



        #------------------------- EXTERNAL USERS----------------------------#
        elseif (Auth::user()->account_type == 2) {
            if ($request->user()->cannot('View Canceled Invoice', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            //CURRENT EVENT INVOICES
           return $advice_note = advice_notes::leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')->where('company_id', Auth::user()->user_uuid)
                ->where('advice_note_status','CANCELED')
                ->whereIn('advice_notes.company_id',CompanyAdmin::where('adminId',Auth::user()->user_uuid)->get(['companyId']))
                ->orderBy('canceled_at', 'DESC')
                ->paginate(config('global.DATA_LIMIT'),[
                    'company_id', 'advice_notes.advice_note_id', 'companyName', 'advice_note_deadline', 'invoice_type',
                    'advice_note_status', 'advice_note_description', 'advice_note_created_at', 'registrationID', 'companyLocation',
                    'companyPhone', 'control_number', 'balance', 'total_amount', 'currency', 'amount_paid','canceled_at'
                ]);
            //END CURRENT EVENT INVOICES
        }
        #----------------------------------- END EXTERNAL USERS------------------------#
        return view('advice_note.CanceledInvoices',compact('advice_note')); // return on fair advice note with data
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
