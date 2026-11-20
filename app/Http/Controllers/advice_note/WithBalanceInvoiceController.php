<?php

namespace App\Http\Controllers\advice_note;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class WithBalanceInvoiceController extends Controller
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
        if ($request->user()->cannot('View Invoice Balances', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $totalBalance = advice_notes::leftJoin('companies', 'advice_notes.company_id', 'companies.companyID')
        ->orderBy('advice_note_deadline','DESC')->where('advice_note_status','!=','CANCELED')    
        ->where('advice_note_deadline','<',date('Y-m-d'))
        ->where('balance', '>', 1)->sum('balance');

        $invoices = advice_notes::leftJoin('companies', 'advice_notes.company_id', 'companies.companyID')
        ->orderBy('advice_note_deadline','DESC')->where('advice_note_status','!=','CANCELED')    
        ->where('advice_note_deadline','<',date('Y-m-d'))
        ->where('balance', '>', 1)->paginate(10);

        return view('advice_note.InvoiceWithBalance', compact('invoices','totalBalance'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if ($request->user()->cannot('View Invoice Balances', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $totalBalance = advice_notes::leftJoin('companies', 'advice_notes.company_id', 'companies.companyID')
        ->orderBy('advice_note_deadline','DESC')
        ->where('advice_note_status','!=','CANCELED')    
        ->where('advice_note_deadline','<',date('Y-m-d'))
        ->where('company_id',$id)
        ->where('balance', '>', 1)->sum('balance');

        $invoices = advice_notes::leftJoin('companies', 'advice_notes.company_id', 'companies.companyID')
        ->orderBy('advice_note_deadline','DESC')
        ->where('advice_note_status','!=','CANCELED')    
        ->where('advice_note_deadline','<',date('Y-m-d'))
        ->where('company_id',$id)
        ->where('balance', '>', 1)->paginate(10);
        return view('advice_note.InvoiceWithBalance', compact('invoices','totalBalance'));
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
