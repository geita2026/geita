<?php

namespace App\Services\Gepg;

use App\Models\GepgBill;
use App\Models\GepgReconciliation;
use RuntimeException;
use SimpleXMLElement;

/**
 * The only place that knows GePG's XML. Element names follow the published
 * GePG Service Provider API (bill submission, control number response, payment
 * information, cancellation, reconciliation). When the Ministry of Finance's
 * integration specification differs, change it here and nowhere else.
 *
 * Every message is <Gepg><content/><gepgSignature/></Gepg>, and the signature
 * covers the content element's exact text.
 */
class GepgXml
{
    public function __construct(private GepgSigner $signer) {}

    /* --------------------------------------------------------------- outgoing */

    public function billSubmission(GepgBill $bill, array $items, ?string $reuseControlNumber = null): string
    {
        $invoice = $bill->invoice;
        $payer = $invoice->exhibitor;
        $amount = $this->money($bill->amount);

        $lines = collect($items)->map(fn ($i) => '<BillItem>'
            .$this->el('BillItemRef', $i['ref'])
            .$this->el('UseItemRefOnPay', 'N')
            .$this->el('BillItemAmt', $this->money($i['amount']))
            .$this->el('BillItemEqvAmt', $this->money($i['amount']))
            .$this->el('BillItemMiscAmt', '0.00')
            .$this->el('GfsCode', $i['gfs_code'])
            .'</BillItem>')->implode('');

        $content = '<gepgBillSubReq>'
            .'<BillHdr>'.$this->el('SpCode', config('gepg.sp_code')).$this->el('RtrRespFlg', 'true').'</BillHdr>'
            .'<BillTrxInf>'
            .$this->el('BillId', $bill->bill_id)
            .$this->el('SubSpCode', config('gepg.sub_sp_code'))
            .$this->el('SpSysId', config('gepg.sp_sys_id'))
            .$this->el('BillAmt', $amount)
            .$this->el('MiscAmt', '0.00')
            .$this->el('BillExprDt', $bill->expires_at->format('Y-m-d\TH:i:s'))
            .$this->el('PyrId', 'EXH'.$payer->id)
            .$this->el('PyrName', $payer->organization_name)
            .$this->el('BillDesc', "{$invoice->number} — exhibition space, {$invoice->exhibition->name}")
            .$this->el('BillGenDt', now()->format('Y-m-d\TH:i:s'))
            .$this->el('BillGenBy', 'GeMEMIS')
            .$this->el('BillApprBy', 'GeMEMIS')
            .$this->el('PyrCellNum', preg_replace('/\D/', '', (string) $payer->phone))
            .$this->el('PyrEmail', $payer->email)
            .$this->el('Ccy', $bill->currency)
            .$this->el('BillEqvAmt', $amount)
            .$this->el('RemFlag', 'true')
            .$this->el('BillPayOpt', (string) config('gepg.payment_option'))
            .($reuseControlNumber ? $this->el('PayCntrNum', $reuseControlNumber) : '')
            .'<BillItems>'.$lines.'</BillItems>'
            .'</BillTrxInf>'
            .'</gepgBillSubReq>';

        return $this->envelope($content);
    }

    public function cancellation(GepgBill $bill, string $reason): string
    {
        return $this->envelope('<gepgBillCanclReq>'
            .$this->el('SpCode', config('gepg.sp_code'))
            .$this->el('SpSysId', config('gepg.sp_sys_id'))
            .'<BillCanclTrxDt>'.$this->el('BillId', $bill->bill_id).$this->el('CanclReasn', $reason).'</BillCanclTrxDt>'
            .'</gepgBillCanclReq>');
    }

    public function reconciliationRequest(GepgReconciliation $run): string
    {
        return $this->envelope('<gepgSpReconcReq>'
            .$this->el('SpReconcReqId', $run->request_id)
            .$this->el('SpCode', config('gepg.sp_code'))
            .$this->el('SpSysId', config('gepg.sp_sys_id'))
            .$this->el('TnxDt', $run->transaction_date->format('Y-m-d'))
            .$this->el('ReconcOpt', '1')
            .'</gepgSpReconcReq>');
    }

    /** Our acknowledgement of something GePG sent: gepgBillSubResp → gepgBillSubRespAck, and so on. */
    public function acknowledgement(string $receivedType, string $code): string
    {
        $type = $receivedType.'Ack';

        return $this->envelope("<$type>".$this->el('TrxStsCode', $code)."</$type>");
    }

    public function envelope(string $content, bool $asGepg = false): string
    {
        $signature = $asGepg ? $this->signer->signAsGepg($content) : $this->signer->sign($content);

        return '<Gepg>'.$content.'<gepgSignature>'.$signature.'</gepgSignature></Gepg>';
    }

    /* --------------------------------------------------------------- incoming */

    /**
     * Splits a message into its content element and signature, and checks the
     * signature against GePG's certificate.
     *
     * @return array{type: string, content: string, data: SimpleXMLElement, signature_valid: bool}
     */
    public function parse(string $xml): array
    {
        if (! preg_match('#<Gepg>\s*(<(gepg\w+)>.*</\2>)\s*<gepgSignature>(.*?)</gepgSignature>\s*</Gepg>#s', trim($xml), $m)) {
            throw new RuntimeException('Not a GePG message.');
        }
        $previous = libxml_use_internal_errors(true);
        $data = simplexml_load_string($m[1], options: LIBXML_NONET | LIBXML_NOCDATA);
        libxml_use_internal_errors($previous);
        if (! $data) {
            throw new RuntimeException('The GePG message is not valid XML.');
        }

        return ['type' => $m[2], 'content' => $m[1], 'data' => $data, 'signature_valid' => $this->signer->verify($m[1], trim($m[3]))];
    }

    /** First TrxStsCode anywhere in a response (acks and results both carry one). */
    public function statusCode(SimpleXMLElement $data): ?string
    {
        $found = $data->xpath('//TrxStsCode');

        return $found ? trim((string) $found[0]) : null;
    }

    public function el(string $name, ?string $value): string
    {
        return "<$name>".htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8')."</$name>";
    }

    public function money(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}
