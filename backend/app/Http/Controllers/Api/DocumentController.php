<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\Application;
use App\Models\Exhibition;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Audit;
use Barryvdh\DomPDF\Facade\Pdf;

/** FR-058: printable documents. */
class DocumentController extends Controller
{
    private function pdf(string $view, array $data, string $name, string $orientation = 'portrait')
    {
        return Pdf::loadView("pdf.$view", $data + ['generatedAt' => now(), 'generatedBy' => $this->actor()->name])
            ->setPaper('a4', $orientation)->stream($name);
    }

    public function invoice(Invoice $invoice)
    {
        $this->ownOrStaff($invoice->exhibitor_id);
        $invoice->load('items', 'exhibitor', 'exhibition', 'application');

        return $this->pdf('invoice', compact('invoice'), "{$invoice->number}.pdf");
    }

    public function receipt(Payment $payment)
    {
        $payment->load('invoice.exhibitor', 'invoice.exhibition', 'verifier');
        $this->ownOrStaff($payment->invoice->exhibitor_id);
        abort_unless($payment->verification_status === 'verified', 422, 'A receipt exists only for a verified payment.');

        return $this->pdf('receipt', compact('payment'), "{$payment->receipt_number}.pdf");
    }

    /** Issued only once participation is confirmed. */
    public function allocationLetter(Application $application)
    {
        $this->ownOrStaff($application->exhibitor_id);
        abort_unless($application->status === 'confirmed', 422, 'The allocation letter is available once participation is confirmed.');
        $application->load('exhibitor', 'exhibition');
        $allocations = Allocation::where('application_id', $application->id)->where('status', 'allocated')->with('space.hall', 'space.category')->get();
        Audit::log('document.allocation_letter', "Allocation letter generated for {$application->reference}", $application);

        return $this->pdf('allocation-letter', compact('application', 'allocations'), "Allocation-letter-{$application->reference}.pdf");
    }

    public function schedule(Exhibition $exhibition)
    {
        $this->permit('reports.view');
        $allocations = Allocation::where('exhibition_id', $exhibition->id)->whereIn('status', Allocation::LIVE)
            ->with('exhibitor', 'space.hall', 'space.category', 'application')->get()->sortBy(fn ($a) => $a->space->number, SORT_NATURAL);

        return $this->pdf('schedule', compact('exhibition', 'allocations'), "{$exhibition->code}-allocation-schedule.pdf", 'landscape');
    }
}
