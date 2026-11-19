<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Exhibition;
use App\Models\Exhibitor;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** FR-050..057: dashboard, reports (JSON or CSV) and global search. */
class ReportController extends Controller
{
    /** FR-055 */
    public function dashboard(Request $request, Exhibition $exhibition)
    {
        $user = $this->actor();

        if (! $user->isStaff()) {
            return $this->exhibitorDashboard($exhibition, $user->exhibitor_id);
        }

        $spaces = $exhibition->spaces()->select('status', DB::raw('count(*) as n'), DB::raw('sum(area_sqm) as area'))->groupBy('status')->get()->keyBy('status');
        $total = (int) $spaces->sum('n');
        $n = fn (string ...$s) => (int) collect($s)->sum(fn ($k) => $spaces[$k]->n ?? 0);
        $apps = $exhibition->applications()->where('status', '!=', 'draft')->select('status', DB::raw('count(*) as n'))->groupBy('status')->pluck('n', 'status');
        $inv = Invoice::where('exhibition_id', $exhibition->id)->whereNotIn('status', ['cancelled', 'refunded']);
        $billed = (float) (clone $inv)->sum('total');
        $collected = (float) (clone $inv)->sum('amount_paid');
        $held = $n('allocated', 'occupied');

        return [
            'exhibition' => $exhibition->only(['id', 'code', 'name', 'venue', 'status', 'start_date', 'end_date', 'registration_closes_on', 'payment_deadline', 'registration_open', 'currency']),
            'kpis' => [
                'total_spaces' => $total,
                'available_spaces' => $n('available'),
                'allocated_spaces' => $held,
                'occupancy_rate' => $total ? round($n('occupied') / $total * 100, 1) : 0,
                'allocation_rate' => $total ? round($held / $total * 100, 1) : 0,
                'registered_exhibitors' => $exhibition->applications()->where('status', '!=', 'draft')->distinct()->count('exhibitor_id'),
                'approved_applications' => (int) (($apps['approved'] ?? 0) + ($apps['pending_payment'] ?? 0) + ($apps['confirmed'] ?? 0)),
                'pending_applications' => (int) ($apps['submitted'] ?? 0),
                'total_billed' => $billed,
                'total_collected' => $collected,
                'outstanding' => max(0, $billed - $collected),
                'payments_to_verify' => Payment::where('verification_status', 'pending')->whereHas('invoice', fn ($i) => $i->where('exhibition_id', $exhibition->id))->count(),
                'selections_to_confirm' => Allocation::where('exhibition_id', $exhibition->id)->where('status', 'pending')->count(),
                'checked_in' => Allocation::where('exhibition_id', $exhibition->id)->where('status', 'allocated')->whereIn('occupancy_status', ['checked_in', 'occupied'])->count(),
            ],
            'spaces_by_status' => collect(config('gememis.space_statuses'))->map(fn ($s) => ['status' => $s, 'count' => $n($s)])->values(),
            'applications_by_status' => collect(config('gememis.application_statuses'))->reject(fn ($s) => $s === 'draft')
                ->map(fn ($s) => ['status' => $s, 'count' => (int) ($apps[$s] ?? 0)])->values(),
            'by_category' => $this->byCategory($exhibition),
            'by_hall' => $exhibition->halls()->withCount(['spaces', 'spaces as held_count' => fn ($q) => $q->whereIn('status', ['allocated', 'occupied', 'pending_approval'])])
                ->get(['id', 'code', 'name']),
            'recent_activity' => $user->allowed('audit.view') || $user->allowed('exhibitions.manage')
                ? AuditLog::latest('id')->limit(8)->get(['id', 'user_name', 'action', 'summary', 'reference', 'created_at']) : [],
        ];
    }

    private function exhibitorDashboard(Exhibition $exhibition, ?int $exhibitorId): array
    {
        $application = Application::where('exhibition_id', $exhibition->id)->where('exhibitor_id', $exhibitorId)
            ->whereNotIn('status', ['cancelled', 'rejected'])->with(['activeAllocations.space.hall:id,name', 'category:id,name'])->latest()->first();
        $invoices = Invoice::where('exhibitor_id', $exhibitorId)->where('exhibition_id', $exhibition->id)->whereNotIn('status', ['cancelled'])->with('activeGepgBill')->get();

        return [
            'exhibition' => $exhibition->only(['id', 'code', 'name', 'venue', 'theme', 'status', 'start_date', 'end_date', 'registration_closes_on', 'payment_deadline', 'registration_open', 'currency', 'allow_self_selection']),
            'application' => $application,
            'missing_documents' => $application?->missingDocuments() ?? [],
            'invoices' => $invoices,
            'billed' => (float) $invoices->sum('total'),
            'paid' => (float) $invoices->sum('amount_paid'),
            'available_spaces' => $exhibition->spaces()->where('status', 'available')->count(),
        ];
    }

    private function byCategory(Exhibition $exhibition)
    {
        $revenue = DB::table('invoice_items')->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('invoices.exhibition_id', $exhibition->id)->whereNotIn('invoices.status', ['cancelled', 'refunded'])
            ->select('space_category', DB::raw('sum(invoice_items.amount) as billed'))->groupBy('space_category')->pluck('billed', 'space_category');

        return $exhibition->spaceCategories()->withCount(['spaces', 'spaces as held_count' => fn ($q) => $q->whereIn('status', ['allocated', 'occupied', 'pending_approval'])])
            ->get()->map(fn ($c) => [
                'id' => $c->id, 'name' => $c->name, 'color' => $c->color, 'price_per_sqm' => $c->price_per_sqm,
                'spaces' => $c->spaces_count, 'held' => $c->held_count, 'billed' => (float) ($revenue[$c->name] ?? 0),
            ]);
    }

    public function report(Request $request, Exhibition $exhibition, string $type)
    {
        $this->permit('reports.view');
        if (in_array($type, ['payments'], true)) {
            $this->permit('finance.view');
        }

        $report = match ($type) {
            'space-availability' => $this->availability($exhibition),
            'space-allocation' => $this->allocation($exhibition, $request),
            'exhibitors' => $this->exhibitorsReport($exhibition),
            'payments' => $this->paymentsReport($exhibition, $request),
            'performance' => $this->performance($exhibition),
            default => abort(404, 'Unknown report.'),
        };
        $report['generated_at'] = now()->toIso8601String();
        $report['exhibition'] = $exhibition->only(['id', 'code', 'name']);

        if ($request->query('format') === 'csv') {
            return $this->csv("{$exhibition->code}-$type-".now()->format('Ymd-Hi').'.csv', $report);
        }

        return $report;
    }

    private function csv(string $name, array $report)
    {
        return response()->streamDownload(function () use ($report) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // Excel reads UTF-8 only with a BOM
            fputcsv($out, array_column($report['columns'], 'label'));
            foreach ($report['rows'] as $row) {
                fputcsv($out, array_map(fn ($c) => $row[$c['key']] ?? '', $report['columns']));
            }
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function cols(array $map): array
    {
        return collect($map)->map(fn ($label, $key) => is_array($label) ? ['key' => $key] + $label : ['key' => $key, 'label' => $label])->values()->all();
    }

    /** FR-050 */
    private function availability(Exhibition $exhibition): array
    {
        $rows = $exhibition->halls()->with('spaces:id,hall_id,status')->get()->map(function ($hall) {
            $c = $hall->spaces->countBy('status');

            return [
                'hall' => $hall->name, 'total' => $hall->spaces->count(), 'available' => $c['available'] ?? 0, 'reserved' => ($c['reserved'] ?? 0) + ($c['pending_approval'] ?? 0),
                'allocated' => $c['allocated'] ?? 0, 'occupied' => $c['occupied'] ?? 0,
                'unavailable' => ($c['blocked'] ?? 0) + ($c['maintenance'] ?? 0) + ($c['cancelled'] ?? 0),
            ];
        });
        $vacant = Allocation::where('exhibition_id', $exhibition->id)->where('status', 'allocated')->whereIn('occupancy_status', ['not_occupied', 'vacant'])->count();

        return [
            'title' => 'Space availability',
            'columns' => $this->cols(['hall' => 'Hall / area', 'total' => ['label' => 'Total', 'num' => true], 'available' => ['label' => 'Available', 'num' => true],
                'reserved' => ['label' => 'Reserved', 'num' => true], 'allocated' => ['label' => 'Allocated', 'num' => true],
                'occupied' => ['label' => 'Occupied', 'num' => true], 'unavailable' => ['label' => 'Blocked / maintenance', 'num' => true]]),
            'rows' => $rows,
            'summary' => ['Total spaces' => $rows->sum('total'), 'Available' => $rows->sum('available'), 'Reserved' => $rows->sum('reserved'),
                'Allocated' => $rows->sum('allocated'), 'Occupied' => $rows->sum('occupied'), 'Allocated but vacant' => $vacant],
        ];
    }

    /** FR-051 — also the "space allocation schedule" of FR-058. */
    private function allocation(Exhibition $exhibition, Request $request): array
    {
        $query = Allocation::where('exhibition_id', $exhibition->id)->with(['exhibitor:id,organization_name', 'space.hall:id,name', 'space.category:id,name', 'application:id,reference,status']);
        $query->whereIn('status', $request->query('status') ? explode(',', $request->query('status')) : Allocation::LIVE);

        $rows = $query->get()->sortBy(fn ($a) => $a->space->number, SORT_NATURAL)->values()->map(fn ($a) => [
            'space' => $a->space->number, 'hall' => $a->space->hall->name, 'category' => $a->space->category->name, 'size' => $a->space->size_label,
            'exhibitor' => $a->exhibitor->organization_name, 'application' => $a->application->reference,
            'allocated_on' => $a->allocated_at?->format('Y-m-d'), 'status' => $a->status, 'participation' => $a->application->status, 'occupancy' => $a->occupancy_status,
        ]);

        return [
            'title' => 'Space allocation',
            'columns' => $this->cols(['space' => 'Space', 'hall' => 'Hall', 'category' => 'Category', 'size' => 'Size', 'exhibitor' => 'Exhibitor', 'application' => 'Application',
                'allocated_on' => 'Allocated on', 'status' => 'Allocation', 'participation' => 'Participation', 'occupancy' => 'Occupancy']),
            'rows' => $rows,
            'summary' => ['Allocations' => $rows->count(), 'Confirmed' => $rows->where('participation', 'confirmed')->count(), 'Checked in' => $rows->whereIn('occupancy', ['checked_in', 'occupied'])->count()],
        ];
    }

    /** FR-052 */
    private function exhibitorsReport(Exhibition $exhibition): array
    {
        $rows = Exhibitor::whereHas('applications', fn ($a) => $a->where('exhibition_id', $exhibition->id)->where('status', '!=', 'draft'))
            ->with(['applications' => fn ($a) => $a->where('exhibition_id', $exhibition->id)->withCount('activeAllocations')])
            ->orderBy('organization_name')->get()->map(fn ($e) => [
                'organization' => $e->organization_name, 'type' => $e->exhibitor_type, 'country' => $e->country, 'sector' => $e->business_sector,
                'contact' => $e->contact_person, 'phone' => $e->phone, 'email' => $e->email, 'tin' => $e->tin,
                'application' => $e->applications->first()?->reference, 'status' => $e->applications->first()?->status,
                'spaces' => (int) $e->applications->sum('active_allocations_count'),
            ]);

        return [
            'title' => 'Registered exhibitors',
            'columns' => $this->cols(['organization' => 'Organisation', 'type' => 'Type', 'country' => 'Country', 'sector' => 'Sector', 'contact' => 'Contact person',
                'phone' => 'Phone', 'email' => 'Email', 'tin' => 'TIN', 'application' => 'Application', 'status' => 'Status', 'spaces' => ['label' => 'Spaces', 'num' => true]]),
            'rows' => $rows,
            'summary' => ['Exhibitors' => $rows->count(), 'Countries' => $rows->pluck('country')->unique()->count(), 'International' => $rows->where('type', 'international')->count()],
        ];
    }

    /** FR-053 */
    private function paymentsReport(Exhibition $exhibition, Request $request): array
    {
        $query = Invoice::where('exhibition_id', $exhibition->id)->with('exhibitor:id,organization_name');
        if ($status = $request->query('status')) {
            $query->whereIn('status', explode(',', $status));
        }
        $invoices = $query->orderBy('number')->get();
        $live = $invoices->whereNotIn('status', ['cancelled', 'refunded']);

        return [
            'title' => 'Payments',
            'columns' => $this->cols(['number' => 'Invoice', 'control_number' => 'Control no.', 'exhibitor' => 'Exhibitor', 'issued_on' => 'Issued', 'due_on' => 'Due',
                'total' => ['label' => 'Total', 'num' => true, 'money' => true], 'paid' => ['label' => 'Paid', 'num' => true, 'money' => true],
                'balance' => ['label' => 'Balance', 'num' => true, 'money' => true], 'status' => 'Status']),
            'rows' => $invoices->map(fn ($i) => [
                'number' => $i->number, 'control_number' => $i->control_number, 'exhibitor' => $i->exhibitor->organization_name,
                'issued_on' => $i->issued_on->format('Y-m-d'), 'due_on' => $i->due_on->format('Y-m-d'),
                'total' => $i->total, 'paid' => $i->amount_paid, 'balance' => $i->balance, 'status' => $i->status,
            ])->values(),
            'summary' => ['Billed' => $live->sum('total'), 'Collected' => $live->sum('amount_paid'), 'Outstanding' => $live->sum('balance'),
                'Paid invoices' => $live->where('status', 'paid')->count(), 'Unpaid' => $live->where('status', 'unpaid')->count(),
                'Partially paid' => $live->where('status', 'partially_paid')->count(), 'Overdue' => $live->where('status', 'overdue')->count()],
            'money_summary' => ['Billed', 'Collected', 'Outstanding'],
            'by_category' => $this->byCategory($exhibition),
        ];
    }

    /** FR-054 */
    private function performance(Exhibition $exhibition): array
    {
        $rows = $this->byCategory($exhibition)->map(fn ($c) => [
            'category' => $c['name'], 'rate' => $c['price_per_sqm'], 'spaces' => $c['spaces'], 'held' => $c['held'],
            'utilisation' => $c['spaces'] ? round($c['held'] / $c['spaces'] * 100, 1).'%' : '—', 'billed' => $c['billed'],
        ]);
        $apps = $exhibition->applications()->where('status', '!=', 'draft');
        $submitted = (clone $apps)->count();
        $approved = (clone $apps)->whereIn('status', ['approved', 'pending_payment', 'confirmed'])->count();

        return [
            'title' => 'Exhibition performance',
            'columns' => $this->cols(['category' => 'Space category', 'rate' => ['label' => 'Rate / m²', 'num' => true, 'money' => true], 'spaces' => ['label' => 'Spaces', 'num' => true],
                'held' => ['label' => 'Taken', 'num' => true], 'utilisation' => ['label' => 'Utilisation', 'num' => true], 'billed' => ['label' => 'Billed', 'num' => true, 'money' => true]]),
            'rows' => $rows->values(),
            'summary' => [
                'Applications received' => $submitted,
                'Approval rate' => $submitted ? round($approved / $submitted * 100).'%' : '—',
                'Confirmed participants' => (clone $apps)->where('status', 'confirmed')->count(),
                'Space utilisation' => $rows->sum('spaces') ? round($rows->sum('held') / $rows->sum('spaces') * 100, 1).'%' : '—',
                'Revenue billed' => $rows->sum('billed'),
            ],
            'money_summary' => ['Revenue billed'],
        ];
    }

    /** FR-056: one box, six record types. */
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q'));
        if (mb_strlen($q) < 2) {
            return ['results' => []];
        }
        $user = $this->actor();
        $staff = $user->isStaff();
        $like = "%$q%";
        $mine = fn ($query) => $staff ? $query : $query->where('exhibitor_id', $user->exhibitor_id);
        $results = collect();

        Exhibition::when(! $staff, fn ($w) => $w->whereNotIn('status', ['draft', 'cancelled']))
            ->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('code', 'like', $like))->limit(5)->get()
            ->each(fn ($e) => $results->push(['type' => 'Exhibition', 'title' => $e->name, 'subtitle' => $e->code.' · '.$e->venue, 'href' => "/exhibitions/{$e->id}"]));

        $mine(Application::with('exhibitor:id,organization_name')->where('status', '!=', $staff ? 'draft' : '-'))
            ->where(fn ($w) => $w->where('reference', 'like', $like)->orWhereHas('exhibitor', fn ($e) => $e->where('organization_name', 'like', $like)))->limit(6)->get()
            ->each(fn ($a) => $results->push(['type' => 'Application', 'title' => $a->reference, 'subtitle' => $a->exhibitor->organization_name.' · '.$a->status, 'href' => "/applications/{$a->id}"]));

        $mine(Invoice::with('exhibitor:id,organization_name'))
            ->where(fn ($w) => $w->where('number', 'like', $like)->orWhere('control_number', 'like', $like))->limit(6)->get()
            ->each(fn ($i) => $results->push(['type' => 'Invoice', 'title' => $i->number, 'subtitle' => $i->exhibitor->organization_name.' · '.$i->status, 'href' => "/invoices/{$i->id}"]));

        if ($staff) {
            Exhibitor::where('organization_name', 'like', $like)->orWhere('email', 'like', $like)->limit(6)->get()
                ->each(fn ($e) => $results->push(['type' => 'Exhibitor', 'title' => $e->organization_name, 'subtitle' => $e->country.' · '.$e->contact_person, 'href' => "/exhibitors/{$e->id}"]));
            Space::with('exhibition:id,code', 'hall:id,name')->where('number', 'like', $like)->limit(6)->get()
                ->each(fn ($s) => $results->push(['type' => 'Space', 'title' => $s->number, 'subtitle' => "{$s->exhibition->code} · {$s->hall->name} · {$s->status}", 'href' => "/map?exhibition={$s->exhibition_id}&space={$s->id}"]));
            if ($user->allowed('finance.view')) {
                Payment::with('invoice:id,number')->where(fn ($w) => $w->where('reference', 'like', $like)->orWhere('payer_reference', 'like', $like)->orWhere('receipt_number', 'like', $like))->limit(5)->get()
                    ->each(fn ($p) => $results->push(['type' => 'Payment', 'title' => $p->receipt_number ?? $p->reference, 'subtitle' => $p->invoice->number.' · '.$p->verification_status, 'href' => "/invoices/{$p->invoice_id}"]));
            }
        }

        return ['results' => $results->values()];
    }
}
