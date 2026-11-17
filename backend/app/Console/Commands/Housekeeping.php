<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Exhibition;
use App\Models\Invoice;
use App\Services\Audit;
use App\Services\Notifier;
use Illuminate\Console\Command;

/**
 * Daily rules that depend on the calendar rather than on a user:
 * BR-010 closes registration after the deadline, FR-032 marks invoices
 * overdue, FR-039 sends reminders. Scheduled in routes/console.php.
 */
class Housekeeping extends Command
{
    protected $signature = 'gememis:housekeeping';

    protected $description = 'Close registration, flag overdue invoices and send reminders';

    public function handle(): int
    {
        $today = now()->startOfDay();

        // BR-010
        Exhibition::where('status', 'published')->whereDate('registration_closes_on', '<', $today)->each(function ($e) {
            $e->update(['status' => 'registration_closed']);
            Audit::log('exhibition.registration_closed', "Registration for {$e->name} closed automatically after the deadline", $e, ['status' => 'published'], ['status' => 'registration_closed']);
            $this->info("Closed registration: {$e->code}");
        });

        // FR-032
        Invoice::whereIn('status', ['unpaid'])->whereDate('due_on', '<', $today)->with('exhibitor')->each(function ($i) {
            $i->update(['status' => 'overdue']);
            Notifier::exhibitor($i->exhibitor, 'payment', 'Invoice overdue', "Invoice {$i->number} was due on {$i->due_on->format('d M Y')}. Pay ".number_format($i->balance)." {$i->currency} to keep your space.", "/invoices/{$i->id}");
        });

        // FR-039: payment due in 3 days
        Invoice::whereIn('status', ['unpaid', 'partially_paid'])->whereDate('due_on', $today->copy()->addDays(3))->with('exhibitor')->each(fn ($i) => Notifier::exhibitor($i->exhibitor, 'reminder', 'Payment due in 3 days',
            "Invoice {$i->number}: ".number_format($i->balance)." {$i->currency} is due on {$i->due_on->format('d M Y')}.", "/invoices/{$i->id}"));

        // FR-039: registration closes in 3 days — drafts not yet submitted
        Application::where('status', 'draft')->whereHas('exhibition', fn ($e) => $e->where('status', 'published')->whereDate('registration_closes_on', $today->copy()->addDays(3)))
            ->with('exhibitor', 'exhibition')->each(fn ($a) => Notifier::exhibitor($a->exhibitor, 'reminder', 'Registration closes in 3 days',
                "Your application {$a->reference} for {$a->exhibition->name} is still a draft. Submit it before registration closes.", "/applications/{$a->id}"));

        // FR-039: applications waiting more than 3 days for review
        $waiting = Application::where('status', 'submitted')->where('submitted_at', '<', now()->subDays(3))->count();
        if ($waiting) {
            Notifier::roles(['registration_officer'], 'reminder', 'Applications awaiting review', "$waiting application(s) have been waiting more than three days.", '/applications?status=submitted');
        }

        // FR-039: exhibition opens tomorrow — check-in reminder
        Application::where('status', 'confirmed')->whereHas('exhibition', fn ($e) => $e->whereDate('start_date', $today->copy()->addDay()))
            ->with('exhibitor', 'exhibition')->each(fn ($a) => Notifier::exhibitor($a->exhibitor, 'reminder', 'Check-in opens tomorrow',
                "{$a->exhibition->name} starts tomorrow at {$a->exhibition->venue}. Bring your allocation letter for check-in.", "/applications/{$a->id}"));

        $this->info('Housekeeping complete.');

        return self::SUCCESS;
    }
}
