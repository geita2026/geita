<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GepgBill;
use App\Models\GepgMessage;
use App\Models\GepgReconciliation;
use App\Models\Invoice;
use App\Services\Audit;
use App\Services\Gepg\GepgService;
use App\Services\Gepg\GepgSigner;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/** FR-059: GePG callbacks (public, signature-checked) and the staff view of the gateway. */
class GepgController extends Controller
{
    public function __construct(private GepgService $gepg) {}

    /**
     * GePG posts control numbers, payments and reconciliation results here.
     * No token: each message carries GePG's signature, checked against its
     * certificate, and the reply is our signed acknowledgement.
     */
    public function receive(Request $request)
    {
        abort_unless($this->gepg->enabled(), 503, 'GePG is switched off.');
        $reply = $this->gepg->receive($request->getContent());

        return response($reply['body'], $reply['status'])->header('Content-Type', 'application/xml');
    }

    public function status()
    {
        $this->permit('finance.view');
        $signer = app(GepgSigner::class);

        return [
            'mode' => $this->gepg->mode(),
            'sp_code' => config('gepg.sp_code'),
            'sub_sp_code' => config('gepg.sub_sp_code'),
            'sp_sys_id' => config('gepg.sp_sys_id'),
            'base_url' => config('gepg.base_url'),
            'payment_option' => config('gepg.payment_option'),
            'default_gfs_code' => config('gepg.default_gfs_code'),
            'simulator_keys' => $signer->usingSimulatorKeys(),
            'callback_url' => url('/api/v1/integrations/gepg'),
            'bills' => GepgBill::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status'),
            'last_message_at' => GepgMessage::max('created_at'),
            'last_failure' => GepgMessage::whereNotNull('error')->latest('id')->first(['id', 'type', 'reference', 'error', 'created_at']),
        ];
    }

    public function messages(Request $request)
    {
        $this->permit('finance.view');
        $query = GepgMessage::query();
        if ($q = $request->query('q')) {
            $query->where('reference', 'like', "%$q%");
        }
        if ($request->boolean('errors')) {
            $query->where(fn ($w) => $w->whereNotNull('error')->orWhere('signature_valid', false));
        }

        return $query->latest('id')->paginate($this->perPage());
    }

    /** The verbatim XML, for a dispute or for GePG support. */
    public function message(GepgMessage $message)
    {
        $this->permit('finance.view');

        return $message;
    }

    public function reconciliations()
    {
        $this->permit('finance.view');

        return GepgReconciliation::with('requester:id,name')->latest('id')->limit(30)->get();
    }

    public function reconcile(Request $request)
    {
        $this->permit('finance.manage');
        $date = $request->validate(['date' => 'required|date|before_or_equal:today'])['date'];

        return response()->json($this->gepg->reconcile(Carbon::parse($date), $this->actor()), 201);
    }

    /** Ask GePG again after a failure (or check the bill matches the balance). */
    public function retry(Invoice $invoice)
    {
        $this->permit('finance.manage');
        Audit::log('gepg.retry', "Control number requested again for {$invoice->number}", $invoice);
        $this->gepg->sync($invoice);

        return $invoice->fresh()->load('gepgBills');
    }

    /** Simulator only: pay the invoice's control number as an exhibitor would at the bank. */
    public function simulatePayment(Request $request, Invoice $invoice)
    {
        $this->permit('finance.manage');
        $data = $request->validate(['amount' => 'required|numeric|min:1', 'silent' => 'nullable|boolean']);
        // "silent": GePG takes the money but its notification is lost, for reconciliation to recover.
        $trx = $this->gepg->simulatePayment($invoice, (float) $data['amount'], ! $request->boolean('silent'));

        return ['transaction' => $trx, 'invoice' => $invoice->fresh()];
    }
}
