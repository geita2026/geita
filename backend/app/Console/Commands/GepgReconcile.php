<?php

namespace App\Console\Commands;

use App\Services\Gepg\GepgService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GepgReconcile extends Command
{
    protected $signature = 'gepg:reconcile {date? : Transaction date, YYYY-MM-DD (default: yesterday)}';

    protected $description = 'Ask GePG for a day\'s payments and compare them with GeMEMIS (FR-059)';

    public function handle(GepgService $gepg): int
    {
        if (! $gepg->enabled()) {
            $this->warn('GePG is off; nothing to reconcile.');

            return self::SUCCESS;
        }
        $run = $gepg->reconcile(Carbon::parse($this->argument('date') ?? 'yesterday'));
        $this->line("{$run->request_id}: {$run->status}".($run->error ? " — {$run->error}" : ''));
        if ($run->status === 'received') {
            $this->line("  at GePG {$run->gepg_count} · matched {$run->matched} · recovered ".count($run->recovered ?? [])
                .' · unknown to GePG '.count($run->missing_at_gepg ?? []).' · amount mismatches '.count($run->amount_mismatches ?? []));
        }

        return $run->status === 'failed' ? self::FAILURE : self::SUCCESS;
    }
}
