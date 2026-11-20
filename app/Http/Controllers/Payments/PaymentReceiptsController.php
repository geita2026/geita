<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\Collections\PaymentDetails;
use App\Models\companies;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class PaymentReceiptsController extends Controller
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
            if ($request->user()->cannot('Manage Payments', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }

            if (Auth::user()->account_type == 1) {
                $payments = companies::orderBy('companyName','ASC')->paginate(10, [
                        'companyName',
                        'companyEmail',
                        'companyPhone',
                        'companyID',
                        'registrationID'
                    ]);
                    $payments=PaymentDetails::leftJoin('advice_notes','payment_details.referenceId','=','advice_notes.advice_note_id')
                    ->leftJoin('companies','advice_notes.company_id','=','companies.companyID')
                    ->orderBy('payment_made_on','DESC')
                    ->where('companyName','!=',Null)
                    ->paginate(10,[
                        'payment_id',
                        'referenceId',
                        'companyID',
                        'companyName',
                        'payment_details.control_number',
                        'payment_details.amount_paid',
                        'payment_details.currency',
                        'payment_made_on'
                    ]);
            }
            if (Auth::user()->account_type == 2) {
                $payments = companies::orderBy('companyName','ASC')
                    ->leftJoin('company_admins', 'companies.companyID', '=', 'company_admins.companyId')
                    ->where('company_admins.adminId', '=', Auth::user()->user_uuid)
                    ->paginate(10, [
                        'companyName',
                        'companyEmail',
                        'companyPhone',
                        'companies.companyID',
                        'registrationID'
                    ]);
            }


            return view('PaymentReceipts.ReceiptsList', compact('payments'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500').$th->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            if ($request->user()->cannot('Manage Payments', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $search = $request->get('q');
            if (Auth::user()->account_type == 1) {
                $data = PaymentDetails::orderBy('trans_date', 'DESC')
                    ->leftJoin('advice_notes', 'payment_details.referenceId', '=', 'advice_notes.advice_note_id')
                    ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                    ->where('amount_paid', '!=', null)
                    ->where(function ($query) use ($search) {
                        $query->orWhere('companyName', 'LIKE', '%' . $search . '%');
                        $query->orWhere('control_number', 'LIKE', '%' . $search . '%');
                        $query->orWhere('advice_note_id', 'LIKE', '%' . $search . '%');
                        $query->orWhere('total_amount', 'LIKE', '%' . $search . '%');
                        $query->orWhere('advice_note_created_at', 'LIKE', '%' . $search . '%');
                        $query->orWhere('receipt', 'LIKE', '%' . $search . '%');
                    })
                    ->get([
                        'advice_note_id',
                        'companyName',
                        'amount_paid',
                        'registrationID'
                    ]);
            } elseif (Auth::user()->accaount_type == 2) {
                $data = PaymentDetails::orderBy('trans_date', 'DESC')
                    ->leftJoin('advice_notes', 'payment_details.referenceId', '=', 'advice_notes.advice_note_id')
                    ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                    ->leftJoin('company_admins', 'advice_notes.company_id', '=', 'company_admins.companyId')
                    ->where('company_admins.adminId', '=', Auth::user()->user_uuid)
                    ->where('amount_paid', '!=', null)
                    ->where(function ($query) use ($search) {
                        $query->orWhere('companyName', 'LIKE', '%' . $search . '%');
                        $query->orWhere('control_number', 'LIKE', '%' . $search . '%');
                        $query->orWhere('advice_note_id', 'LIKE', '%' . $search . '%');
                        $query->orWhere('total_amount', 'LIKE', '%' . $search . '%');
                        $query->orWhere('advice_note_created_at', 'LIKE', '%' . $search . '%');
                        $query->orWhere('receipt', 'LIKE', '%' . $search . '%');
                    })
                    ->get([
                        'advice_note_id',
                        'companyName',
                        'amount_paid'
                    ]);
            }
            return response()->json($data);
        } catch (\Throwable $th) {
            return 0;
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
        if ($request->user()->cannot('Update Payment Receipt', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        try {

            #********************* INITIATE TRANSFER**************#
            if ($request->transfer == 1) {
                if (advice_notes::where('advice_note_id', $request->new_invoice)->exists()) {
                    $inv = advice_notes::find($request->new_invoice);
                    $pay = PaymentDetails::find($id);

                    if ($inv->currency != $pay->currency) {
                        return back()->with('fail', 'Invoice have different currency!');
                    }

                    $reference = PaymentDetails::find($id)->in_fair_invoice_id;
                    PaymentDetails::find($id)->update([
                        'in_fair_invoice_id' => $request->new_invoice,
                        'is_transfered' => 1,
                        'old_invoice' => $reference,
                        'transfered_by' => Auth::user()->user_uuid,
                        'transfer_reason' => $request->transfer_reason,
                    ]);

                    return back()->with('success', 'Payment Transfer initialized to ' . $request->new_invoice);
                } else {
                    return back()->with('fail', 'No invoice with Id ' . $request->new_invoice . ' payment Found!');
                }
            }
            #************END TRANSFER*********************#

            #***************APPROVE TRANSFER**************#
            if ($request->transfer == 2) {

                $reference = $request->new_invoice;

                $invoice = advice_notes::where('advice_note_id', $reference)->get();
                foreach ($invoice as $key) {
                    $total_amount = $key->total_amount;
                }
                $amount_paid = 0;
                $payments = PaymentDetails::where('in_fair_invoice_id', $reference)->get();
                foreach ($payments as $item) {
                    $amount_paid = $amount_paid + $item->amount_paid;
                }

                $balance = $total_amount - $amount_paid;
                advice_notes::find($reference)->update(['amount_paid' => $amount_paid, 'balance' => $balance]); // update invoice with amount paid

                PaymentDetails::find($id)->update([
                    'approved_by' => Auth::user()->user_uuid,
                ]);
                return back()->with('success', 'Transfer Approved successfully!');
            }
            #***************** END TRANSFER **************#

        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }


    public function show(Request $request, $id)
    {

        try {
            if ($request->user()->cannot('Manage Payments', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }

            $data=PaymentDetails::leftJoin('advice_notes','payment_details.referenceId','=','advice_notes.advice_note_id')
            ->find($id);
            $payments = PaymentDetails::orderBy('payment_made_on', 'DESC')
                ->leftJoin('advice_notes', 'payment_details.referenceId', '=', 'advice_notes.advice_note_id')
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
                ->leftJoin('exhibitors','companies.companyID','=','exhibitors.exhibitor_id')
                ->where('exhibitor_event',$data->event)
                ->where('payment_id', $id)
                ->get([
                    'payment_id',
                    'payment_details.control_number',
                    'payment_details.in_fair_invoice_id',
                    'payment_details.old_invoice',
                    'payment_details.amount_paid',
                    'payment_details.currency',
                    'receipt',
                    'companyName',
                    'transfered_by',
                    'is_transfered',
                    'approved_by',
                    'transfer_reason',
                    'payment_made_on',
                    'total_amount',
                    'companyID',
                    'event_name',
                    'registrationID',
                    'receipt',
                    'bank_receipt',
                    'companyPhone',
                    'companyEmail',
                    'exhibitionSpace',
                ]);
            $company = companies::find($id);
            $pdf = Pdf::loadView('PaymentReceipts.printableOnFairReceipt', compact('payments', 'company'));
            $pdf->getDomPDF()->setHttpContext(
                stream_context_create([
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ]
                ])
            );
            $pdf->render();
            return $pdf->stream($id);
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }


    public function edit(Request $request, $id)
    {

        try {
            if ($request->user()->cannot('Manage Payments', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $payments = PaymentDetails::orderBy('payment_made_on', 'DESC')
                ->leftJoin('advice_notes', 'payment_details.referenceId', '=', 'advice_notes.advice_note_id')
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
                ->where('advice_notes.company_id', $id)
                ->paginate(10, [
                    'payment_id',
                    'payment_details.control_number',
                    'payment_details.in_fair_invoice_id',
                    'payment_details.old_invoice',
                    'payment_details.amount_paid',
                    'payment_details.currency',
                    'receipt',
                    'companyName',
                    'transfered_by',
                    'is_transfered',
                    'approved_by',
                    'transfer_reason',
                    'payment_made_on',
                    'referenceId',
                    'companyID',
                    'event_name'
                ]);
            $company = companies::find($id);
            return view('PaymentReceipts.CompanyPaymentList', compact('payments', 'company'));
        } catch (\Throwable $th) {
            return back()->with('success', config('messages.500'));
        }
    }
}
