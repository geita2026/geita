<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\Gepg\GepgService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Brings an invoice's GePG bill in line with its balance. Queued so a slow or
 * unavailable GePG never holds up the officer allocating a space; dispatched
 * after the database commit so it sees the final invoice.
 */
class SyncGepgBill implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public array $backoff = [30, 120, 600, 1800];

    public function __construct(public int $invoiceId) {}

    public function handle(GepgService $gepg): void
    {
        if ($invoice = Invoice::find($this->invoiceId)) {
            $gepg->sync($invoice);
        }
    }
}
