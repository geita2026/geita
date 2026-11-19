<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FinanceController extends Controller
{
    public function __construct(private BillingService $billing) {}

    public function invoices(Request $request)
    {
        $user = $this->actor();
        $query = Invoice::with(['exhibitor:id,organization_name', 'exhibition:id,code,name', 'application:id,reference'])->withCount('items');

        if (! $user->isStaff()) {
            $query->where('exhibitor_id', $user->exhibitor_id);
        }
        foreach (['exhibition_id', 'exhibitor_id'] as $filter) {
            if ($value = $request->query($filter)) {
                $query->where($filter, $value);
            }
        }
        if ($status = $request->query('status')) {
            $query->whereIn('status', explode(',', $status));
        }
        if ($from = $request->query('from')) {
            $query->whereDate('issued_on', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('issued_on', '<=', $to);
        }
        if ($q = $request->query('q')) {
            $query->where(fn ($w) => $w->where('number', 'like', "%$q%")->orWhere('control_number', 'like', "%$q%")
                ->orWhereHas('exhibitor', fn ($e) => $e->where('organization_name', 'like', "%$q%")));
        }

        return $query->latest('id')->paginate($this->perPage());
    }

    public function invoice(Invoice $invoice)
    {
        $this->ownOrStaff($invoice->exhibitor_id);

        return $invoice->load(['items', 'gepgBills', 'payments.verifier:id,name', 'exhibitor', 'exhibition:id,code,name,venue,payment_deadline,contact_email,contact_phone', 'application:id,reference,status']);
    }

    public function payments(Request $request)
    {
        $this->permit('finance.view');
        $query = Payment::with(['invoice:id,number,total,amount_paid,currency,exhibitor_id,exhibition_id,status', 'invoice.exhibitor:id,organization_name', 'verifier:id,name']);

        if ($status = $request->query('status')) {
            $query->whereIn('verification_status', explode(',', $status));
        }
        if ($exhibition = $request->query('exhibition_id')) {
            $query->whereHas('invoice', fn ($i) => $i->where('exhibition_id', $exhibition));
        }
        if ($q = $request->query('q')) {
            $query->where(fn ($w) => $w->where('reference', 'like', "%$q%")->orWhere('payer_reference', 'like', "%$q%")->orWhere('receipt_number', 'like', "%$q%")
                ->orWhereHas('invoice', fn ($i) => $i->where('number', 'like', "%$q%")));
        }

        return $query->latest('id')->paginate($this->perPage());
    }

    /** The exhibitor (or Finance on their behalf) records a payment with its evidence. */
    public function submitPayment(Request $request, Invoice $invoice)
    {
        $this->ownOrStaff($invoice->exhibitor_id);
        if ($this->actor()->isStaff()) {
            $this->permit('finance.manage');
        }
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'paid_on' => 'required|date|before_or_equal:today',
            'method' => 'required|in:bank,mobile_money,control_number,cash',
            'payer_reference' => 'required|string|max:100',
            'evidence' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        return response()->json($this->billing->submitPayment($invoice, $data, $request->file('evidence'), $this->actor()), 201);
    }

    public function verify(Request $request, Payment $payment)
    {
        $this->permit('finance.manage');
        $data = $request->validate(['decision' => 'required|in:verify,reject', 'notes' => 'required_if:decision,reject|nullable|string|max:255']);

        return $data['decision'] === 'verify'
            ? $this->billing->verify($payment, $this->actor(), $data['notes'] ?? null)
            : $this->billing->reject($payment, $this->actor(), $data['notes']);
    }

    public function evidence(Payment $payment)
    {
        $this->ownOrStaff($payment->invoice->exhibitor_id);
        abort_unless($payment->evidence_path && Storage::disk('local')->exists($payment->evidence_path), 404);

        return Storage::disk('local')->download($payment->evidence_path, $payment->evidence_name);
    }
}
