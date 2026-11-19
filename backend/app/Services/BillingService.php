<?php

namespace App\Services;

use App\Exceptions\DomainException;
use App\Jobs\SyncGepgBill;
use App\Models\Allocation;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/** FR-031..034, BR-004, BR-008. */
class BillingService
{
    private const OPEN = ['unpaid', 'partially_paid', 'overdue'];

    /** Add the allocation's space to the application's open invoice, creating one if needed. */
    public function billAllocation(Allocation $allocation): Invoice
    {
        $allocation->loadMissing('space.category', 'space.hall', 'exhibition', 'application');
        $exhibition = $allocation->exhibition;

        $invoice = Invoice::where('application_id', $allocation->application_id)->whereIn('status', self::OPEN)->first();
        $created = ! $invoice;

        if ($created) {
            $invoice = Invoice::create([
                'number' => Ref::temp(),
                'exhibition_id' => $exhibition->id,
                'exhibitor_id' => $allocation->exhibitor_id,
                'application_id' => $allocation->application_id,
                'vat_rate' => $exhibition->vat_rate,
                'currency' => $exhibition->currency,
                'issued_on' => now()->toDateString(),
                'due_on' => max($exhibition->payment_deadline->toDateString(), now()->addDays(7)->toDateString()),
            ]);
            Ref::assign($invoice, 'number', 'INV', $exhibition->code);
        }

        $space = $allocation->space;
        $invoice->items()->create([
            'allocation_id' => $allocation->id,
            'description' => "Exhibition space {$space->number} — {$space->hall->name}",
            'space_number' => $space->number,
            'space_size' => $space->size_label,
            'space_category' => $space->category->name,
            'gfs_code' => $space->category->gfs_code,
            'unit_price' => $space->price,
            'quantity' => 1,
            'amount' => $space->price,
        ]);
        $this->recompute($invoice);

        Audit::log($created ? 'invoice.generated' : 'invoice.updated', "Invoice {$invoice->number} billed space {$space->number}", $invoice, null, ['total' => $invoice->total]);

        if ($created) {
            Notifier::exhibitor($allocation->exhibitor, 'payment', 'Invoice issued',
                "Invoice {$invoice->number} for ".number_format($invoice->total)." {$invoice->currency} is due on {$invoice->due_on->format('d M Y')}."
                .(config('gepg.mode') !== 'off' ? ' Your GePG control number follows in a separate message.' : ''),
                "/invoices/{$invoice->id}");
        }

        return $invoice;
    }

    /** Take an allocation off its invoice (cancellation, transfer). */
    public function unbillAllocation(Allocation $allocation): void
    {
        $invoices = Invoice::whereHas('items', fn ($q) => $q->where('allocation_id', $allocation->id))->get();

        foreach ($invoices as $invoice) {
            $invoice->items()->where('allocation_id', $allocation->id)->delete();
            $this->recompute($invoice);
        }
    }

    /** Point the invoice line at the new space after a reallocation, on the same invoice. */
    public function rebillAllocation(Allocation $old, Allocation $new): ?Invoice
    {
        $item = InvoiceItem::where('allocation_id', $old->id)->first();
        if (! $item) {
            return $this->billAllocation($new);
        }
        $space = $new->space()->with('category', 'hall')->first();
        $item->update([
            'allocation_id' => $new->id,
            'description' => "Exhibition space {$space->number} — {$space->hall->name}",
            'space_number' => $space->number, 'space_size' => $space->size_label, 'space_category' => $space->category->name,
            'gfs_code' => $space->category->gfs_code,
            'unit_price' => $space->price, 'amount' => $space->price,
        ]);

        return $this->recompute(Invoice::findOrFail($item->invoice_id));
    }

    public function recompute(Invoice $invoice): Invoice
    {
        $subtotal = round((float) $invoice->items()->sum('amount'), 2);
        $vat = round($subtotal * $invoice->vat_rate / 100, 2);
        $total = $subtotal + $vat;
        $paid = round((float) $invoice->payments()->where('verification_status', 'verified')->sum('amount'), 2);

        $status = match (true) {
            $invoice->status === 'refunded' => 'refunded',
            $subtotal <= 0 && $paid <= 0 => 'cancelled',
            $paid >= $total && $paid > 0 => 'paid', // an overpayment after a change is held as credit
            $paid > 0 => 'partially_paid',
            $invoice->due_on->isPast() && ! $invoice->due_on->isToday() => 'overdue',
            default => 'unpaid',
        };

        $invoice->update(['subtotal' => $subtotal, 'vat_amount' => $vat, 'total' => $total, 'amount_paid' => $paid, 'status' => $status]);

        // FR-059: once the change is committed, the GePG bill follows the new balance.
        DB::afterCommit(fn () => SyncGepgBill::dispatch($invoice->id));

        return $invoice;
    }

    public function submitPayment(Invoice $invoice, array $data, ?UploadedFile $evidence, User $actor): Payment
    {
        if (in_array($invoice->status, ['paid', 'cancelled', 'refunded'], true)) {
            throw new DomainException("Invoice {$invoice->number} is {$invoice->status}; it does not accept payments.");
        }
        if (! empty($data['payer_reference']) && Payment::where('payer_reference', $data['payer_reference'])->where('verification_status', '!=', 'rejected')->exists()) {
            throw new DomainException('A payment with this transaction reference has already been submitted for this invoice.');
        }

        $payment = Payment::create([
            'reference' => Ref::temp(),
            'invoice_id' => $invoice->id,
            'amount' => $data['amount'],
            'paid_on' => $data['paid_on'],
            'method' => $data['method'] ?? 'bank',
            'payer_reference' => $data['payer_reference'] ?? null,
            'evidence_path' => $evidence?->store("payments/{$invoice->id}", 'local'),
            'evidence_name' => $evidence?->getClientOriginalName(),
            'manual_reason' => $data['manual_reason'] ?? null,
            'submitted_by' => $actor->id,
        ]);
        Ref::assign($payment, 'reference', 'PAY');

        Audit::log('payment.submitted', "Payment {$payment->reference} of ".number_format($payment->amount)." submitted against {$invoice->number}", $payment);
        Notifier::roles(['finance_officer'], 'payment', 'Payment awaiting verification',
            "{$invoice->exhibitor->organization_name} submitted ".number_format($payment->amount)." {$invoice->currency} against {$invoice->number}.", '/payments');

        return $payment;
    }

    /** FR-033/034: verify, issue the receipt, settle the invoice, confirm participation. */
    public function verify(Payment $payment, ?User $actor, ?string $notes = null, string $channel = 'manual'): Payment
    {
        return DB::transaction(function () use ($payment, $actor, $notes, $channel) {
            if ($payment->verification_status !== 'pending') {
                throw new DomainException("Payment {$payment->reference} has already been {$payment->verification_status}.");
            }
            // Two-person rule: whoever recorded a payment can never be the one who accepts it.
            if ($actor && $payment->submitted_by === $actor->id) {
                throw new DomainException('You recorded this payment, so another Finance Officer must verify it.', 'two-person rule');
            }

            $payment->update([
                'verification_status' => 'verified', 'verified_by' => $actor?->id, 'verified_at' => now(),
                'verification_notes' => $notes, 'channel' => $channel,
                'receipt_number' => 'RCT-'.now()->format('y').'-'.str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT),
            ]);

            $invoice = $this->recompute($payment->invoice);
            $manual = $channel !== 'gateway';
            Audit::log($manual ? 'payment.verified_manual' : 'payment.verified',
                ($manual ? 'Manual payment ' : 'Payment ')."{$payment->reference} verified; receipt {$payment->receipt_number}", $payment,
                ['verification_status' => 'pending'], ['verification_status' => 'verified', 'invoice_status' => $invoice->status, 'recorded_by' => $payment->submitted_by]);

            // Every payment accepted by hand is reported to Management and the Auditor, to spot-check against the bank statement.
            if ($manual) {
                $by = $actor?->name ?? 'unknown';
                $from = $payment->submitter?->name ?? 'unknown';
                Notifier::roles(['management', 'auditor'], 'payment', 'Manual payment accepted',
                    number_format($payment->amount)." {$invoice->currency} on {$invoice->number} ({$invoice->exhibitor->organization_name}) was recorded by $from and verified by $by"
                    .($payment->payer_reference ? ", reference {$payment->payer_reference}" : '').'. Check it against the bank statement.',
                    "/invoices/{$invoice->id}");
            }

            Notifier::exhibitor($invoice->exhibitor, 'payment', 'Payment verified',
                "Receipt {$payment->receipt_number}: ".number_format($payment->amount)." {$invoice->currency} received against {$invoice->number}. Balance: ".number_format($invoice->balance).'.',
                "/invoices/{$invoice->id}");

            if ($invoice->status === 'paid') {
                $this->confirmParticipation($invoice);
            }

            return $payment;
        });
    }

    public function reject(Payment $payment, User $actor, string $notes): Payment
    {
        if ($payment->verification_status !== 'pending') {
            throw new DomainException("Payment {$payment->reference} has already been {$payment->verification_status}.");
        }
        $payment->update(['verification_status' => 'rejected', 'verified_by' => $actor->id, 'verified_at' => now(), 'verification_notes' => $notes]);
        Audit::log('payment.rejected', "Payment {$payment->reference} rejected: $notes", $payment);
        Notifier::exhibitor($payment->invoice->exhibitor, 'payment', 'Payment not accepted',
            "Payment {$payment->reference} against {$payment->invoice->number} could not be verified: $notes", "/invoices/{$payment->invoice_id}");

        return $payment;
    }

    /** Participation is confirmed once every live invoice is paid and BR-003 documents are complete. */
    private function confirmParticipation(Invoice $invoice): void
    {
        $application = $invoice->application;
        $owing = $application->invoices()->whereIn('status', self::OPEN)->exists();

        if ($owing || $application->status !== 'pending_payment') {
            return;
        }
        if ($missing = $application->missingDocuments()) {
            Notifier::exhibitor($application->exhibitor, 'application', 'Documents outstanding',
                'Payment is complete, but participation cannot be confirmed until these documents are uploaded: '.implode(', ', $missing).'.',
                "/applications/{$application->id}");

            return;
        }

        $application->update(['status' => 'confirmed', 'confirmed_at' => now()]);
        Audit::log('application.confirmed', "Participation confirmed for {$application->reference}", $application, ['status' => 'pending_payment'], ['status' => 'confirmed']);
        Notifier::exhibitor($application->exhibitor, 'application', 'Participation confirmed',
            "Your participation in {$application->exhibition->name} is confirmed. Download your allocation letter from the application page.",
            "/applications/{$application->id}");
    }
}
