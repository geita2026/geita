<?php

namespace App\Services\Gepg;

use App\Models\GepgBill;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * A stand-in for GePG, for development, tests and demonstrations before the
 * Ministry of Finance grants test access. It answers like GePG does:
 *
 * - checks GeMEMIS's signature on every request;
 * - acknowledges a bill, then "later" sends the control number back;
 * - cancels bills; reissues a control number for reuse;
 * - takes payments (simulatePayment) and notifies GeMEMIS, signed as GePG;
 * - answers reconciliation requests from its own ledger of payments.
 *
 * "Later" is flush(): callbacks are held until GeMEMIS has finished handling
 * the acknowledgement, then delivered through the same code path as a real
 * GePG callback. The ledger lives in storage so it survives between requests.
 */
class SimulatorTransport implements GepgTransport
{
    private const LEDGER = 'gepg-simulator/ledger.json';

    /** @var string[] messages waiting to be "posted" to GeMEMIS */
    private array $outbox = [];

    public function __construct(private GepgService $service, private GepgXml $xml, private GepgSigner $signer) {}

    public function post(string $path, string $xml): array
    {
        $msg = $this->xml->parse($this->asGepgView($xml));
        $ok = config('gepg.codes.success');
        $bad = config('gepg.codes.invalid_signature');

        if (! $msg['signature_valid']) {
            return $this->reply($msg['type'].'Ack', $bad);
        }

        $d = $msg['data'];

        return match ($msg['type']) {
            'gepgBillSubReq' => $this->bill($d),
            'gepgBillCanclReq' => $this->cancel($d),
            'gepgSpReconcReq' => $this->reconcile($d),
            default => $this->reply($msg['type'].'Ack', config('gepg.codes.failure')),
        };
    }

    public function flush(): void
    {
        while ($xml = array_shift($this->outbox)) {
            $this->service->receive($xml);
        }
    }

    /**
     * The exhibitor pays at a bank or by mobile money. With $notify false GePG
     * "loses" the notification, which reconciliation then has to recover.
     */
    public function pay(GepgBill $bill, float $amount, bool $notify = true): array
    {
        $trx = [
            'TrxId' => 'SIM'.strtoupper(Str::random(10)),
            'BillId' => $bill->bill_id,
            'PayCtrNum' => $bill->control_number,
            'BillAmt' => $this->xml->money($bill->amount),
            'PaidAmt' => $this->xml->money($amount),
            'CCy' => $bill->currency,
            'TrxDtTm' => now()->format('Y-m-d\TH:i:s'),
            'UsdPayChnl' => 'MOBILE',
            'PyrCellNum' => preg_replace('/\D/', '', (string) $bill->invoice->exhibitor->phone),
            'PyrName' => $bill->invoice->exhibitor->organization_name,
            'PspReceiptNumber' => 'RC'.random_int(10000000, 99999999),
            'PspName' => 'Simulated Bank',
            'CtrAccNum' => (string) (config('gepg.collection_account') ?: '0150000000000'),
        ];
        $this->record($trx);

        if ($notify) {
            $fields = collect($trx)->only(['TrxId'])->map(fn ($v, $k) => $this->xml->el($k, $v))->implode('')
                .$this->xml->el('SpCode', config('gepg.sp_code'))
                .$this->xml->el('PayRefId', $trx['TrxId'])
                .collect($trx)->except(['TrxId'])->map(fn ($v, $k) => $this->xml->el($k, $v))->implode('')
                .$this->xml->el('BillPayOpt', (string) config('gepg.payment_option'))
                .$this->xml->el('PyrEmail', $bill->invoice->exhibitor->email);
            $this->service->receive($this->xml->envelope("<gepgPmtSpInfo><PymtTrxInf>$fields</PymtTrxInf></gepgPmtSpInfo>", asGepg: true));
        }

        return $trx;
    }

    /* ------------------------------------------------------------- handlers */

    private function bill($d): array
    {
        $info = $d->BillTrxInf;
        $billId = (string) $info->BillId;
        // A reuse request keeps its control number; a new bill gets the next one.
        $control = (string) $info->PayCntrNum ?: '9914'.str_pad((string) (GepgBill::where('bill_id', $billId)->value('id') ?? random_int(1, 9999999)), 8, '0', STR_PAD_LEFT);

        $this->outbox[] = $this->xml->envelope('<gepgBillSubResp><BillTrxInf>'
            .$this->xml->el('BillId', $billId)
            .$this->xml->el('TrxSts', 'GS')
            .$this->xml->el('PayCntrNum', $control)
            .$this->xml->el('TrxStsCode', config('gepg.codes.success'))
            .'</BillTrxInf></gepgBillSubResp>', asGepg: true);

        return $this->reply('gepgBillSubReqAck', config('gepg.codes.success'));
    }

    private function cancel($d): array
    {
        $billId = (string) $d->BillCanclTrxDt->BillId;

        return ['status' => 200, 'body' => $this->xml->envelope('<gepgBillCanclResp><BillCanclTrxDt>'
            .$this->xml->el('BillId', $billId)
            .$this->xml->el('TrxSts', 'GS')
            .$this->xml->el('TrxStsCode', config('gepg.codes.success'))
            .'</BillCanclTrxDt></gepgBillCanclResp>', asGepg: true)];
    }

    private function reconcile($d): array
    {
        $date = (string) $d->TnxDt;
        $rows = collect($this->ledger())->filter(fn ($t) => str_starts_with($t['TrxDtTm'], $date))
            ->map(fn ($t) => '<ReconcTrxInf>'
                .$this->xml->el('SpBillId', $t['BillId'])
                .$this->xml->el('BillCtrNum', $t['PayCtrNum'])
                .$this->xml->el('pspTrxId', $t['TrxId'])
                .$this->xml->el('PaidAmt', $t['PaidAmt'])
                .$this->xml->el('CCy', $t['CCy'])
                .$this->xml->el('PayRefId', $t['TrxId'])
                .$this->xml->el('TrxDtTm', $t['TrxDtTm'])
                .$this->xml->el('CtrAccNum', $t['CtrAccNum'])
                .$this->xml->el('UsdPayChnl', $t['UsdPayChnl'])
                .$this->xml->el('PspName', $t['PspName'])
                .$this->xml->el('PspReceiptNumber', $t['PspReceiptNumber'])
                .$this->xml->el('DptCellNum', $t['PyrCellNum'])
                .$this->xml->el('DptName', $t['PyrName'])
                .'</ReconcTrxInf>')->implode('');

        $this->outbox[] = $this->xml->envelope('<gepgSpReconcResp><ReconcBatchInfo>'
            .$this->xml->el('SpReconcReqId', (string) $d->SpReconcReqId)
            .$this->xml->el('SpCode', config('gepg.sp_code'))
            .$this->xml->el('SpName', config('gepg.sp_name'))
            .$this->xml->el('ReconcStsCode', config('gepg.codes.success'))
            .'</ReconcBatchInfo><ReconcTrans>'.$rows.'</ReconcTrans></gepgSpReconcResp>', asGepg: true);

        return $this->reply('gepgSpReconcReqAck', config('gepg.codes.success'));
    }

    /* ------------------------------------------------------------- plumbing */

    private function reply(string $type, string $code): array
    {
        return ['status' => 200, 'body' => $this->xml->envelope("<$type>".$this->xml->el('TrxStsCode', $code)."</$type>", asGepg: true)];
    }

    /**
     * GePG checks GeMEMIS's signature with GeMEMIS's public key, not its own
     * certificate. parse() verifies against GePG's key, so for the simulator's
     * own check the signature is re-verified here with the SP key.
     */
    private function asGepgView(string $xml): string
    {
        if (preg_match('#<Gepg>\s*(<(gepg\w+)>.*</\2>)\s*<gepgSignature>(.*?)</gepgSignature>#s', $xml, $m)) {
            $key = openssl_pkey_get_public($this->signer->spPublicKey());
            $algo = strtolower((string) config('gepg.signature_algorithm')) === 'sha256' ? OPENSSL_ALGO_SHA256 : OPENSSL_ALGO_SHA1;
            $valid = openssl_verify($m[1], base64_decode($m[3]), $key, $algo) === 1;

            // Re-sign as GePG only when GeMEMIS's signature checked out, so parse() reports the same verdict.
            return '<Gepg>'.$m[1].'<gepgSignature>'.($valid ? $this->signer->signAsGepg($m[1]) : 'invalid').'</gepgSignature></Gepg>';
        }

        return $xml;
    }

    private function ledger(): array
    {
        return json_decode(Storage::disk('local')->get(self::LEDGER) ?? '[]', true) ?: [];
    }

    private function record(array $trx): void
    {
        $all = $this->ledger();
        $all[] = $trx;
        Storage::disk('local')->put(self::LEDGER, json_encode($all, JSON_PRETTY_PRINT));
    }
}
