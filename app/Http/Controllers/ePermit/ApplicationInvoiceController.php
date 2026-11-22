<?php

namespace App\Http\Controllers\ePermit;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\Companies\CompanyAdmin;
use App\Models\ePermit\ApplicationInvoice;
use App\Models\setting\BankAccount;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Traits\IntergrationTraits;

class ApplicationInvoiceController extends Controller
{
    use IntergrationTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($request->user()->cannot('MANAGE_INVOICES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICES'));
            }
            if (Auth::user()->account_type==1) {
                $invoices = ApplicationInvoice::orderBy('invoiceDate', 'ASC')
                ->leftJoin('companies', 'application_invoices.customerId', '=', 'companies.companyID')
                ->leftJoin('trade_promotion_events', 'application_invoices.referenceApplication', '=', 'trade_promotion_events.eventApplicationId')
                ->leftJoin('e_permit_applications', 'application_invoices.referenceApplication', '=', 'e_permit_applications.applicationId')
                ->leftJoin('application_reviewers', 'e_permit_applications.applicationId', '=', 'application_reviewers.applicationReference')
                ->leftJoin('users', 'application_reviewers.asignedUser', '=', 'users.user_uuid')
                ->get();
            }
            elseif (Auth::user()->account_type==2) {
                $invoices = ApplicationInvoice::orderBy('invoiceDate', 'ASC')
                ->leftJoin('companies', 'application_invoices.customerId', '=', 'companies.companyID')
                ->leftJoin('trade_promotion_events', 'application_invoices.referenceApplication', '=', 'trade_promotion_events.eventApplicationId')
                ->leftJoin('e_permit_applications', 'application_invoices.referenceApplication', '=', 'e_permit_applications.applicationId')
                ->leftJoin('application_reviewers', 'e_permit_applications.applicationId', '=', 'application_reviewers.applicationReference')
                ->leftJoin('users', 'application_reviewers.asignedUser', '=', 'users.user_uuid')
                ->whereIn('e_permit_applications.companyId',CompanyAdmin::where('adminId',Auth::user()->user_uuid)->get('companyId'))
                ->get();
            }
            

            return view('ePermit.Invoices.applicationInvoiceList', compact('invoices'));
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
        if ($request->user()->cannot('MANAGE_INVOICES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICES'));
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
        if ($request->user()->cannot('MANAGE_INVOICES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICES'));
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
            if ($request->user()->cannot('MANAGE_INVOICES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICES'));
            }
            $invoices = ApplicationInvoice::leftJoin('e_permit_applications', 'application_invoices.referenceApplication', '=', 'e_permit_applications.applicationId')
                ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
                ->leftJoin('companies', 'application_invoices.customerId', '=', 'companies.companyID')
                ->leftJoin('promotion_event_types', 'trade_promotion_events.tradeEventType', '=', 'promotion_event_types.eventTypeId')
                ->leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
                ->where('invoiceNo', $id)->get();

            $transfer = BankAccount::get();

            $pdf = PDF::loadView('ePermit.Invoices.DownloadableInvoice', compact('invoices', 'transfer'));
            $pdf->setPaper('A4', 'potrait');
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
            return $pdf->stream($id . '.pdf');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('MANAGE_INVOICES', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICES'));
            }

            $payments = ApplicationInvoice::leftJoin('payment_details', 'application_invoices.invoiceNo', '=', 'payment_details.referenceId')
                ->leftJoin('companies', 'application_invoices.customerId', '=', 'companies.companyID')
                ->leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
                ->where('invoiceNo', $id)->get();

            $pdf = Pdf::loadView('ePermit.Invoices.PaymentReceipt', compact('payments'));
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('MANAGE_INVOICES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICES'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        try {
            if ($request->user()->cannot('DELETE_PERMIT_INVOICE', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('DELETE_PERMIT_INVOICE'));
            }
            $validator=Validator::make($request->all(),[
                'cancelReasons'=>'required|max:200|min:5'
            ]);
            if ($validator->fails()) {
                return back()->with('fail',implode(',',$validator->getMessageBag()->all()));
            }
             $invoice=ApplicationInvoice::where('invoiceNo',$id)->where('amountPaid',0)->first();
            if ($invoice) {
               $company=companies::where('companyID',$invoice->customerId)->first();
                $data=array('customer_no' => $company->sp_customer_no, 'invoice_no' => $invoice->spInvoiceNo, 'reference' => $invoice->invoiceNo, 'cancelledby' =>Auth::user()->first_name, 'reason' => $request->cancelReasons);
                if (config('global.APP_STATUS') == 1 && $invoice->controlNumber!=null) {
                    $status = $this->CancelInvoiceFromSp($data, $id); // if their is control number in invoice, cancel control number first from billing system.
                } else {
                    $status = 1;
                }
                if ($status==1) {
                    $invoice->delete();
                    return back()->with('success',config('Messages.200'));
                }
                return back()->with('fail','Action Failed!, It might be already received Payments!');
            }
            return back()->with('fail','Control Number can not be deleted, It might be already received Payments!');
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500').$th->getMessage());
        }
    }


    
   
}
