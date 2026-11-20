<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\YasMandateTrait;
use App\Models\Collections\YasTransaction;

/**
 * Fire a single YAS / Tigo Pesa USSD push from the CLI, for UAT.
 *
 * Records the attempt in the yas_transactions ledger (booking_id = null,
 * since this is a standalone test not tied to a ticket booking), so every
 * UAT push is auditable just like a real one.
 *
 * Examples:
 *   php artisan yas:push 255652442919            # default 1000 TZS
 *   php artisan yas:push 255652442919 5000
 *   php artisan yas:push 255652442919 1000 --remarks="DITF UAT"
 */
class YasPushTest extends Command
{
    use YasMandateTrait;

    protected $signature = 'yas:push
        {phone   : Customer MSISDN, 255 + 9 digits (e.g. 255652442919)}
        {amount=1000 : Amount in TZS}
        {--remarks= : Optional remarks shown to the customer}';

    protected $description = 'Send a single YAS USSD push (UAT). Logs to yas_transactions. Moves real money if approved.';

    public function handle(): int
    {
        $phone   = $this->argument('phone');
        $amount  = (float) $this->argument('amount');
        $remarks = $this->option('remarks') ?: 'TANTRADE UAT test';

        if (! preg_match('/^255\d{9}$/', $phone)) {
            $this->error("Phone must be 255 followed by 9 digits. Got: {$phone}");
            return self::FAILURE;
        }

        $referenceId = $this->makeYasReference();

        $this->line('');
        $this->info('--- YAS USSD push (UAT) ---');
        $this->line('  Customer    : ' . $phone);
        $this->line('  Biller      : ' . config('yas.biller_msisdn', '<MISSING - set YAS_BILLER_MSISDN>'));
        $this->line('  Amount      : ' . number_format($amount) . ' TZS');
        $this->line('  ReferenceID : ' . $referenceId);
        $this->line('  Endpoint    : ' . rtrim((string) config('yas.api_base_url'), '/') . config('yas.paths.biller_pay'));
        $this->line('');

        // Ledger entry BEFORE the call, so nothing is lost.
        $txn = YasTransaction::create([
            'reference_id'    => $referenceId,
            'booking_id'      => null,
            'customer_msisdn' => $phone,
            'biller_msisdn'   => config('yas.biller_msisdn'),
            'amount'          => $amount,
            'currency'        => 'TZS',
            'remarks'         => $remarks,
            'status'          => YasTransaction::STATUS_PENDING,
        ]);

        $result = $this->yasPushBillPay($phone, $amount, $referenceId, $remarks);

        $txn->update([
            'status'           => $result['ok'] ? YasTransaction::STATUS_PUSHED : YasTransaction::STATUS_FAILED,
            'request_payload'  => [
                'CustomerMSISDN' => $phone,
                'BillerMSISDN'   => config('yas.biller_msisdn'),
                'Amount'         => $amount,
                'ReferenceID'    => $referenceId,
            ],
            'response_payload' => $result['body'],
            'response_code'    => $result['code'],
            'error'            => $result['ok'] ? null : ($result['description'] ?? 'Push failed'),
        ]);

        $this->line('  HTTP        : ' . $result['status']);
        $this->line('  Code        : ' . ($result['code'] ?? '-'));
        $this->line('  Description : ' . ($result['description'] ?? '-'));
        $this->line('');

        if ($result['ok']) {
            $this->info("OK - push accepted. Customer should see a prompt. Ledger row id={$txn->id}, status=PUSHED.");
            $this->line('When the customer approves, YAS calls /api/v1/yas/callback and this row becomes SUCCESS.');
            return self::SUCCESS;
        }

        $this->error("Push rejected by YAS. Ledger row id={$txn->id}, status=FAILED. See Code/Description above.");
        return self::FAILURE;
    }
}
