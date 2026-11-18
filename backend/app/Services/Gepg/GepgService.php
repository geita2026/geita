<?php

namespace App\Services\Gepg;

use App\Exceptions\DomainException;
use App\Models\GepgBill;
use App\Models\GepgMessage;
use App\Models\GepgReconciliation;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\Audit;
use App\Services\BillingService;
use App\Services\Notifier;
use App\Services\Ref;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use Throwable;

/**
 * FR-059 / BR-008: GeMEMIS as a GePG Service Provider system.
 *
 *   invoice changes ─▶ sync() ─▶ bill submitted ─▶ GePG acknowledges
 *                                  ◀─ control number callback ─ GePG
 *   exhibitor pays at bank / mobile money
 *                                  ◀─ payment callback ──────── GePG
 *                                     receipt issued, participation confirmed
 *   daily ─▶ reconciliation request ◀─ list of the day's payments ─ GePG
 *
 * A control number collects a fixed amount, so when an invoice's balance
 * changes (a space added, moved to a different price, or cancelled) the open
 * bill is cancelled and a new one is issued for the new balance. Payments
 * reduce a bill's outstanding amount without replacing it.
 */
class GepgService
{
    /** @var array<string, GepgTransport> */
    private array $transports = [];

    public function __construct(private GepgXml $xml, private BillingService $billing) {}

    public function mode(): string
    {
        return in_array(config('gepg.mode'), ['off', 'simulator', 'live'], true) ? config('gepg.mode') : 'off';
    }

    public function enabled(): bool
    {
        return $this->mode() !== 'off';
    }

    public function transport(): GepgTransport
    {
        return $this->transports[$this->mode()] ??= $this->mode() === 'live'
            ? new LiveTransport
            : new SimulatorTransport($this, $this->xml, app(GepgSigner::class));
    }

    /* ================================================================= bills */

    /** Bring the invoice's GePG bill in line with its balance. Safe to call any number of times. */
    public function sync(Invoice $invoice): ?GepgBill
    {
        if (! $this->enabled()) {
            return null;
        }
        $invoice->refresh();
        $active = $invoice->activeGepgBill()->first();
        $balance = $invoice->balance;

        if (in_array($invoice->status, ['paid', 'cancelled', 'refunded'], true) || $balance <= 0) {
            // A control number still open here was not paid through GePG (the invoice was settled
            // another way, or cancelled): cancel it at GePG so nobody can pay it twice.
            if ($active) {
                $this->cancel($active, $invoice->status === 'paid' ? 'Invoice settled' : "Invoice {$invoice->status}");
            }
            $invoice->update(['gepg_status' => $invoice->status === 'paid' ? 'paid' : 'cancelled']);

            return null;
        }

        if ($active) {
            $same = abs($active->outstanding() - $balance) < 0.01 && $active->currency === $invoice->currency;
            if ($same) {
                return $active->status === 'issued' && $active->expires_at->isPast() ? $this->reuse($active, $invoice) : $active;
            }
            $this->cancel($active, 'Invoice amount changed', 'superseded');
        }

        return $this->submit($invoice, $balance);
    }

    private function submit(Invoice $invoice, float $amount): GepgBill
    {
        $invoice->loadMissing('exhibitor', 'exhibition', 'items');
        $bill = GepgBill::create([
            'invoice_id' => $invoice->id,
            'bill_id' => Ref::temp(),
            'amount' => $amount,
            'currency' => $invoice->currency,
            'expires_at' => $this->expiry($invoice),
            'status' => 'requesting',
            'requested_at' => now(),
            'attempts' => 1,
        ]);
        // BillId must be unique for this Service Provider for ever; the row id guarantees it.
        $bill->forceFill(['bill_id' => 'GMS'.str_pad((string) $bill->id, 10, '0', STR_PAD_LEFT)])->saveQuietly();
        $invoice->update(['gepg_status' => 'requesting']);

        try {
            $request = $this->xml->billSubmission($bill, $this->items($invoice, $amount));
        } catch (Throwable $e) {
            return $this->fail($bill, $e->getMessage());
        }

        $reply = $this->send('bill', $request, $bill);
        if (! $reply || $reply['code'] !== config('gepg.codes.success')) {
            return $this->fail($bill, $reply ? "GePG did not accept the bill (code {$reply['code']})." : $this->lastError, $reply['code'] ?? null);
        }

        $bill->update(['status_code' => $reply['code']]);
        Audit::log('gepg.bill_submitted', "Bill {$bill->bill_id} for ".number_format($amount, 2)." {$bill->currency} sent to GePG for {$invoice->number}", $bill, reference: $invoice->number);
        $this->transport()->flush();

        return $bill->fresh();
    }

    /** A control number that expired before it was paid is extended rather than replaced. */
    private function reuse(GepgBill $bill, Invoice $invoice): GepgBill
    {
        $expires = $this->expiry($invoice);
        $original = $bill->expires_at;
        $bill->expires_at = $expires;
        $reply = $this->send('reuse', $this->xml->billSubmission($bill, $this->items($invoice, $bill->outstanding()), $bill->control_number), $bill);

        if ($reply && $reply['code'] === config('gepg.codes.success')) {
            $bill->save();
            Audit::log('gepg.bill_reused', "Control number {$bill->control_number} extended to {$expires->format('d M Y')}", $bill, reference: $invoice->number);
            $this->transport()->flush();
        } else {
            $bill->expires_at = $original;
            $bill->update(['error' => 'GePG refused to extend the control number'.($reply ? " (code {$reply['code']})" : '').'.']);
        }

        return $bill->fresh();
    }

    public function cancel(GepgBill $bill, string $reason, string $endStatus = 'cancelled'): void
    {
        $reply = $this->send('cancel', $this->xml->cancellation($bill, $reason), $bill);
        $ok = $reply && $reply['code'] === config('gepg.codes.success');

        // Ended here either way. If GePG refused, the control number may still take a
        // payment; one that arrives is still matched to the invoice by its BillId.
        $bill->update([
            'status' => $endStatus, 'ended_at' => now(), 'status_code' => $reply['code'] ?? $bill->status_code,
            'error' => $ok ? null : 'GePG did not confirm the cancellation'.($reply ? " (code {$reply['code']})" : '').'.',
        ]);
        Audit::log('gepg.bill_'.$endStatus, "Bill {$bill->bill_id}".($bill->control_number ? " (control number {$bill->control_number})" : '')." $endStatus: $reason", $bill,
            reference: $bill->invoice->number);
    }

    private function fail(GepgBill $bill, ?string $error, ?string $code = null): GepgBill
    {
        $bill->update(['status' => 'failed', 'error' => $error, 'status_code' => $code, 'ended_at' => now()]);
        $bill->invoice->update(['gepg_status' => 'failed']);
        Audit::log('gepg.bill_failed', "Bill {$bill->bill_id} for {$bill->invoice->number} failed: $error", $bill, reference: $bill->invoice->number);
        Notifier::roles(['finance_officer'], 'payment', 'Control number not issued',
            "GePG did not issue a control number for {$bill->invoice->number}: $error It is retried automatically every morning, or press Retry on the invoice.",
            "/invoices/{$bill->invoice_id}");

        return $bill->fresh();
    }

    private function expiry(Invoice $invoice): CarbonInterface
    {
        // The payment deadline, or a week from today once that has passed.
        $due = $invoice->due_on->copy()->endOfDay();

        return $due->isFuture() ? $due : now()->addDays(7)->endOfDay();
    }

    /**
     * Bill lines grouped by GFS code, scaled to the amount billed (the
     * balance, VAT included). GePG requires the lines to add up exactly.
     *
     * @return array<int, array{ref: string, amount: float, gfs_code: string}>
     */
    private function items(Invoice $invoice, float $amount): array
    {
        $groups = [];
        foreach ($invoice->items as $item) {
            $code = $item->gfs_code ?: config('gepg.default_gfs_code') ?: ($this->mode() === 'simulator' ? '000000' : null);
            if (! $code) {
                throw new DomainException("No GFS code for \"{$item->space_category}\". Set one on the space category, or GEPG_DEFAULT_GFS_CODE.");
            }
            $groups[$code] = ($groups[$code] ?? 0) + $item->amount;
        }
        $base = array_sum($groups);
        if ($base <= 0) {
            throw new DomainException("Invoice {$invoice->number} has no charges to bill.");
        }

        $lines = [];
        $n = 0;
        foreach ($groups as $code => $value) {
            $lines[] = ['ref' => $invoice->number.'-'.(++$n), 'amount' => round($amount * $value / $base, 2), 'gfs_code' => (string) $code];
        }
        // Rounding left-overs go on the largest line.
        $diff = round($amount - array_sum(array_column($lines, 'amount')), 2);
        if ($diff != 0) {
            $largest = array_keys(array_column($lines, 'amount'), max(array_column($lines, 'amount')))[0];
            $lines[$largest]['amount'] = round($lines[$largest]['amount'] + $diff, 2);
        }

        return $lines;
    }

    /* ============================================================ callbacks */

    /**
     * Everything GePG posts to GeMEMIS comes through here: control numbers,
     * payments, reconciliation results. Returns the signed acknowledgement.
     *
     * @return array{status: int, body: string}
     */
    public function receive(string $raw): array
    {
        try {
            $msg = $this->xml->parse($raw);
        } catch (Throwable $e) {
            GepgMessage::create(['direction' => 'in', 'type' => 'unreadable', 'payload' => mb_substr($raw, 0, 60000), 'error' => $e->getMessage()]);

            return ['status' => 400, 'body' => 'Not a GePG message.'];
        }

        $logged = GepgMessage::create([
            'direction' => 'in', 'type' => $msg['type'], 'payload' => $raw, 'signature_valid' => $msg['signature_valid'],
            'reference' => $this->referenceOf($msg['data']), 'status_code' => $this->xml->statusCode($msg['data']),
        ]);

        if (! $msg['signature_valid']) {
            $code = config('gepg.codes.invalid_signature');
            $logged->update(['error' => 'Signature does not match GePG\'s certificate; ignored.']);
        } else {
            try {
                // All or nothing: a callback that fails half-way leaves no trace but the log.
                $code = DB::transaction(fn () => match ($msg['type']) {
                    'gepgBillSubResp' => $this->onControlNumber($msg['data']->BillTrxInf),
                    'gepgPmtSpInfo' => $this->onPayment($this->fields($msg['data']->PymtTrxInf), 'notification'),
                    'gepgSpReconcResp' => $this->onReconciliation($msg['data']),
                    default => config('gepg.codes.failure'),
                });
            } catch (Throwable $e) {
                report($e);
                $logged->update(['error' => mb_strimwidth($e->getMessage(), 0, 250)]);
                $code = config('gepg.codes.failure');
            }
        }

        $ack = $this->xml->acknowledgement($msg['type'], $code);
        GepgMessage::create(['direction' => 'out', 'type' => $msg['type'].'Ack', 'payload' => $ack, 'reference' => $logged->reference, 'status_code' => $code]);

        return ['status' => 200, 'body' => $ack];
    }

    private function onControlNumber(SimpleXMLElement $info): string
    {
        $bill = GepgBill::where('bill_id', (string) $info->BillId)->first();
        if (! $bill) {
            return config('gepg.codes.failure');
        }
        $code = (string) $info->TrxStsCode;
        $control = trim((string) $info->PayCntrNum);

        if ($code !== config('gepg.codes.success') || $control === '') {
            $this->fail($bill, "GePG refused the bill (code $code).", $code);

            return config('gepg.codes.success'); // the message itself was received fine
        }

        $live = in_array($bill->status, GepgBill::OPEN, true);
        $bill->update(['control_number' => $control, 'status_code' => $code] + ($live ? ['status' => 'issued', 'issued_at' => now()] : []));

        if ($live) {
            $invoice = $bill->invoice;
            $invoice->update(['control_number' => $control, 'gepg_status' => 'issued']);
            Audit::log('gepg.control_number', "Control number $control issued for {$invoice->number}", $bill, reference: $invoice->number);
            Notifier::exhibitor($invoice->exhibitor, 'payment', "Control number $control",
                'Pay '.number_format($bill->amount, 2)." {$bill->currency} for invoice {$invoice->number} using control number $control, at any bank or by mobile money, before {$bill->expires_at->format('d M Y')}.",
                "/invoices/{$invoice->id}");
        }

        return config('gepg.codes.success');
    }

    /**
     * A payment from GePG — a notification, or one recovered by reconciliation.
     * Recorded once per GePG transaction id; a repeat is acknowledged and ignored.
     */
    private function onPayment(array $p, string $source): string
    {
        $trxId = $p['TrxId'] ?? $p['pspTrxId'] ?? null;
        if (! $trxId) {
            return config('gepg.codes.failure');
        }
        if (Payment::where('gepg_trx_id', $trxId)->exists()) {
            return config('gepg.codes.success');
        }

        $billId = $p['BillId'] ?? $p['SpBillId'] ?? null;
        $control = $p['PayCtrNum'] ?? $p['BillCtrNum'] ?? null;
        $bill = GepgBill::where('bill_id', $billId)->first() ?? ($control ? GepgBill::where('control_number', $control)->latest('id')->first() : null);
        if (! $bill) {
            throw new RuntimeException("Payment $trxId refers to an unknown bill ($billId / $control).");
        }

        DB::transaction(function () use ($p, $trxId, $bill, $source) {
            $amount = (float) $p['PaidAmt'];
            $receipt = $p['PspReceiptNumber'] ?? null;
            $payment = Payment::create([
                'reference' => Ref::temp(),
                'invoice_id' => $bill->invoice_id,
                'gepg_bill_id' => $bill->id,
                'gepg_trx_id' => $trxId,
                'amount' => $amount,
                'paid_on' => substr($p['TrxDtTm'] ?? now()->toDateString(), 0, 10),
                'method' => 'control_number',
                'payer_reference' => $receipt ?: $trxId,
                'psp_name' => $p['PspName'] ?? null,
                'psp_receipt' => $receipt,
                'payment_channel' => $p['UsdPayChnl'] ?? null,
                'collection_account' => $p['CtrAccNum'] ?? null,
                'payer_name' => $p['PyrName'] ?? $p['DptName'] ?? null,
                'payer_phone' => $p['PyrCellNum'] ?? $p['DptCellNum'] ?? null,
                'channel' => 'gateway',
            ]);
            Ref::assign($payment, 'reference', 'PAY');

            $paid = round($bill->paid_amount + $amount, 2);
            $bill->update(['paid_amount' => $paid] + ($paid >= $bill->amount && in_array($bill->status, GepgBill::OPEN, true) ? ['status' => 'paid', 'ended_at' => now()] : []));

            $how = trim(($p['PspName'] ?? 'GePG').' '.($receipt ? "receipt $receipt" : ''));
            $this->billing->verify($payment, null, $source === 'reconciliation' ? "Recovered by GePG reconciliation ($how)" : "Paid through GePG ($how)", 'gateway');
        });

        return config('gepg.codes.success');
    }

    /* ======================================================= reconciliation */

    public function reconcile(CarbonInterface $date, ?User $by = null): GepgReconciliation
    {
        if (! $this->enabled()) {
            throw new DomainException('GePG is switched off (GEPG_MODE=off).');
        }
        $run = GepgReconciliation::create([
            'request_id' => 'REC'.$date->format('Ymd').strtoupper(Str::random(6)),
            'transaction_date' => $date->toDateString(),
            'requested_by' => $by?->id,
        ]);

        $reply = $this->send('reconciliation', $this->xml->reconciliationRequest($run), null, $run->request_id);
        if (! $reply || $reply['code'] !== config('gepg.codes.success')) {
            $run->update(['status' => 'failed', 'error' => $reply ? "GePG refused the request (code {$reply['code']})." : $this->lastError]);

            return $run;
        }
        $this->transport()->flush();

        return $run->fresh();
    }

    private function onReconciliation(SimpleXMLElement $d): string
    {
        $run = GepgReconciliation::where('request_id', (string) $d->ReconcBatchInfo->SpReconcReqId)->first();
        if (! $run) {
            return config('gepg.codes.failure');
        }

        $theirs = [];
        foreach ($d->ReconcTrans->ReconcTrxInf ?? [] as $row) {
            $theirs[] = $this->fields($row);
        }
        $date = $run->transaction_date->toDateString();
        $recovered = $mismatches = [];
        $matched = 0;

        foreach ($theirs as $t) {
            $ours = Payment::where('gepg_trx_id', $t['pspTrxId'] ?? '')->first();
            if (! $ours) {
                // GePG took the money but its notification never reached us: record it now.
                $this->onPayment($t, 'reconciliation');
                $recovered[] = ['trx' => $t['pspTrxId'], 'control_number' => $t['BillCtrNum'] ?? null, 'amount' => (float) $t['PaidAmt']];
            } elseif (abs($ours->amount - (float) $t['PaidAmt']) >= 0.01) {
                $mismatches[] = ['trx' => $t['pspTrxId'], 'ours' => $ours->amount, 'gepg' => (float) $t['PaidAmt'], 'payment' => $ours->reference];
            } else {
                $matched++;
            }
        }

        $known = array_filter(array_column($theirs, 'pspTrxId'));
        $missing = Payment::where('channel', 'gateway')->whereNotNull('gepg_trx_id')->whereDate('paid_on', $date)
            ->whereNotIn('gepg_trx_id', $known)->get()
            ->map(fn ($p) => ['trx' => $p->gepg_trx_id, 'amount' => $p->amount, 'payment' => $p->reference])->all();

        $run->update([
            'status' => 'received', 'received_at' => now(), 'gepg_count' => count($theirs),
            'gepg_total' => round(array_sum(array_map(fn ($t) => (float) $t['PaidAmt'], $theirs)), 2),
            'matched' => $matched, 'recovered' => $recovered, 'missing_at_gepg' => $missing, 'amount_mismatches' => $mismatches,
        ]);
        Audit::log('gepg.reconciled', "GePG reconciliation for $date: ".count($theirs).' at GePG, '.count($recovered).' recovered, '.count($missing).' unknown to GePG, '.count($mismatches).' amount mismatches',
            $run, reference: $run->request_id);

        if ($recovered || $missing || $mismatches) {
            Notifier::roles(['finance_officer'], 'payment', "GePG reconciliation for $date needs a look",
                count($recovered).' payment(s) recovered, '.count($missing).' not known to GePG, '.count($mismatches).' amount mismatch(es).', '/payments/gateway');
        }

        return config('gepg.codes.success');
    }

    /* ============================================================ simulator */

    public function simulatePayment(Invoice $invoice, float $amount, bool $notify = true): array
    {
        if ($this->mode() !== 'simulator') {
            throw new DomainException('Payments can only be simulated in simulator mode.');
        }
        $bill = $invoice->activeGepgBill()->first();
        if (! $bill || $bill->status !== 'issued') {
            throw new DomainException("Invoice {$invoice->number} has no control number to pay yet.");
        }

        return $this->transport()->pay($bill, $amount, $notify);
    }

    /* ============================================================= plumbing */

    private ?string $lastError = null;

    /** @return array{code: ?string, data: SimpleXMLElement}|null */
    private function send(string $path, string $xml, ?GepgBill $bill, ?string $reference = null): ?array
    {
        $type = preg_match('#<Gepg>\s*<(gepg\w+)>#', $xml, $m) ? $m[1] : 'unknown';
        $reference ??= $bill?->bill_id;
        GepgMessage::create(['direction' => 'out', 'type' => $type, 'gepg_bill_id' => $bill?->id, 'reference' => $reference, 'payload' => $xml]);
        $this->lastError = null;

        try {
            $response = $this->transport()->post(config("gepg.paths.$path"), $xml);
            $msg = $this->xml->parse($response['body']);
        } catch (Throwable $e) {
            $this->lastError = 'GePG could not be reached: '.mb_strimwidth($e->getMessage(), 0, 200);
            GepgMessage::create(['direction' => 'in', 'type' => $type.'Ack', 'gepg_bill_id' => $bill?->id, 'reference' => $reference,
                'payload' => $response['body'] ?? '', 'http_status' => $response['status'] ?? null, 'error' => $this->lastError]);

            return null;
        }

        $code = $this->xml->statusCode($msg['data']);
        GepgMessage::create(['direction' => 'in', 'type' => $msg['type'], 'gepg_bill_id' => $bill?->id, 'reference' => $reference,
            'payload' => $response['body'], 'http_status' => $response['status'], 'signature_valid' => $msg['signature_valid'], 'status_code' => $code,
            'error' => $msg['signature_valid'] ? null : 'Signature does not match GePG\'s certificate.']);

        if (! $msg['signature_valid']) {
            $this->lastError = 'GePG\'s reply was not signed with its certificate; ignored.';

            return null;
        }

        return ['code' => $code, 'data' => $msg['data']];
    }

    /** @return array<string, string> */
    private function fields(?SimpleXMLElement $node): array
    {
        $out = [];
        foreach ($node?->children() ?? [] as $child) {
            $out[$child->getName()] = trim((string) $child);
        }

        return $out;
    }

    private function referenceOf(SimpleXMLElement $data): ?string
    {
        foreach (['//BillId', '//SpReconcReqId', '//PayCtrNum'] as $path) {
            if ($found = $data->xpath($path)) {
                return (string) $found[0];
            }
        }

        return null;
    }
}
