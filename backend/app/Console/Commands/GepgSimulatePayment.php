<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\Gepg\GepgService;
use Illuminate\Console\Command;

class GepgSimulatePayment extends Command
{
    protected $signature = 'gepg:simulate-payment {control_number} {amount? : default: the full outstanding amount} {--silent : GePG takes the money but its notification is "lost"}';

    protected $description = 'Simulator only: pay a control number as an exhibitor would at the bank';

    public function handle(GepgService $gepg): int
    {
        $invoice = Invoice::where('control_number', $this->argument('control_number'))->firstOrFail();
        $amount = (float) ($this->argument('amount') ?? $invoice->balance);
        $trx = $gepg->simulatePayment($invoice, $amount, ! $this->option('silent'));
        $this->info("Paid {$trx['PaidAmt']} {$trx['CCy']} to {$trx['PayCtrNum']} (transaction {$trx['TrxId']}). Invoice {$invoice->number} is now {$invoice->fresh()->status}.");

        return self::SUCCESS;
    }
}
