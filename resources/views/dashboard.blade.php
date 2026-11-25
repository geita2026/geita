<x-app-layout>
    <title>{{ config('app.name') }} | Dashboard</title>
    <link rel="stylesheet" href="{{ asset('assets/css/exhibition-dashboard.css') }}">
    <script src="{{ asset('assets/js/core/exhibition-dashboard.js') }}" defer></script>

    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li class="active" aria-current="page">Dashboard</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    <div class="content">
        <div class="panel panel-white">
            <div class="panel-body exhibition-dashboard">
                @if (!$event)
                    <h1>Exhibition dashboard</h1>
                    <p>No exhibition event is active.</p>
                    @can('VIEW_EXHIBITION_EVENT_SUBMENU', \Spatie\Permission\Models\Role::class)
                        <a class="btn btn-primary" href="{{ route('Exhibition-Events.index') }}">Manage exhibition events</a>
                    @else
                        <p>Ask an administrator to activate an exhibition event.</p>
                    @endcan
                @else
                @php
                    $canBook = Auth::user()->can('BOOK_SPACE');
                    $canBrowseSpaces = $canBook || Auth::user()->can('VIEW_BOOKING_SUBMENU');
                    $invoiceCount = $invoiceTotals->sum('invoice_count');
                    $bookingBars = [
                        ['label' => 'Awaiting invoice', 'value' => $draftSpaces],
                        ['label' => 'Invoiced', 'value' => $invoicedSpaces],
                    ];
                    $metrics = [];
                    if ($canViewBookings) {
                        $metrics[] = ['Awaiting invoice', $draftSpaces, 'icon-hour-glass', 'Spaces held in the booking cart'];
                        $metrics[] = ['Invoiced spaces', $invoicedSpaces, 'icon-grid6', 'Spaces with an active invoice'];
                    }
                    if ($canViewCollections) {
                        $metrics[] = ['Invoices issued', $invoiceCount, 'icon-file-text2', 'Active invoices for this event'];
                    }
                @endphp

                <header class="dashboard-heading">
                    <div>
                        <p class="dashboard-context">{{ $isStaff ? 'Exhibition operations' : 'Exhibitor workspace' }}</p>
                        <h1>{{ $event->event_name }}</h1>
                        <p class="dashboard-booking-status">
                            <span class="{{ $bookingOpen ? 'is-open' : '' }}" aria-hidden="true"></span>
                            {{ $bookingOpen ? 'Space booking is open' : 'Space booking is closed' }}
                        </p>
                    </div>
                    <div class="dashboard-actions">
                        @if ($canBrowseSpaces)
                            <a class="btn btn-primary" href="{{ route('booking.index') }}">
                                <i class="icon-grid6" aria-hidden="true"></i>
                                {{ $bookingOpen && $canBook ? 'Book exhibition space' : 'Browse exhibition spaces' }}
                            </a>
                        @endif
                        @if ($canViewInvoices)
                            <a class="btn btn-default" href="{{ route('adviceNotes.index') }}">
                                <i class="icon-file-text2" aria-hidden="true"></i>
                                {{ $isStaff ? 'Manage invoices' : 'My invoices' }}
                            </a>
                        @endif
                    </div>
                </header>

                @if ($metrics)
                    <section class="dashboard-metrics" aria-label="Current event totals">
                        @foreach ($metrics as [$label, $value, $icon, $description])
                            <article class="dashboard-metric">
                                <span class="dashboard-metric-icon"><i class="{{ $icon }}" aria-hidden="true"></i></span>
                                <div>
                                    <h2>{{ $label }}</h2>
                                    <p class="dashboard-count">
                                        <span data-dashboard-count="{{ $value }}" aria-hidden="true">{{ number_format($value) }}</span>
                                        <span class="sr-only">{{ number_format($value) }}</span>
                                    </p>
                                    <p class="dashboard-muted">{{ $description }}</p>
                                </div>
                            </article>
                        @endforeach
                    </section>
                @endif

                @if ($canViewBookings || $canViewCollections)
                    <div class="dashboard-charts">
                        @if ($canViewBookings)
                            <section class="dashboard-panel" aria-labelledby="booking-chart-title">
                                @include('Dashboards.StatusBars', [
                                    'title' => 'Space bookings',
                                    'titleId' => 'booking-chart-title',
                                    'description' => $isStaff ? 'Space reservations for this event' : 'Your cart and company reservations for this event',
                                    'bars' => $bookingBars,
                                    'maximum' => $draftSpaces + $invoicedSpaces,
                                ])
                                @if (!$draftSpaces && !$invoicedSpaces)
                                    <p class="dashboard-empty">No space reservations for this event yet.</p>
                                @endif
                                @if ($isStaff && Auth::user()->can('VIEW_PENDING_BOOKING'))
                                    <a class="dashboard-panel-link" href="{{ route('pending_bookings') }}">View pending bookings <i class="icon-arrow-right13" aria-hidden="true"></i></a>
                                @elseif (!$isStaff && $bookingOpen && $canBook && $draftSpaces)
                                    <a class="dashboard-panel-link" href="{{ route('booking.edit', ['booking' => 'booking']) }}">Continue to booking cart <i class="icon-arrow-right13" aria-hidden="true"></i></a>
                                @endif
                            </section>
                        @endif

                        @if ($canViewCollections)
                            <section class="dashboard-panel" aria-labelledby="collections-chart-title">
                                <h2 id="collections-chart-title">Invoice collections</h2>
                                <p class="dashboard-muted">Payments as a share of billed amounts by currency</p>
                                <ul class="dashboard-bars dashboard-collections">
                                    @foreach (['TZS', 'USD'] as $currency)
                                        @php
                                            $totals = $invoiceTotals->get($currency);
                                            $billed = (float) ($totals->billed ?? 0);
                                            $paid = (float) ($totals->paid ?? 0);
                                            $percentage = $billed > 0 ? round(max(0, $paid) / $billed * 100, 1) : 0;
                                        @endphp
                                        <li>
                                            <div class="dashboard-bar-label">
                                                <strong>{{ $currency }}</strong>
                                                <span>{{ $billed > 0 ? number_format($percentage, 1) . '% collected' : 'No billed amount' }}</span>
                                            </div>
                                            <div class="dashboard-track" aria-hidden="true">
                                                <span class="dashboard-fill is-paid" style="--dashboard-bar-size: {{ min(100, $percentage) }}%"></span>
                                            </div>
                                            <dl class="dashboard-amounts">
                                                <div><dt>Billed</dt><dd>{{ number_format($billed, 2) }}</dd></div>
                                                <div><dt>Collected</dt><dd>{{ number_format($paid, 2) }}</dd></div>
                                                <div><dt>Outstanding</dt><dd>{{ number_format($totals->outstanding ?? 0, 2) }}</dd></div>
                                            </dl>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif
                    </div>
                @endif

                @if ($canViewInvoices)
                    <section class="dashboard-panel" aria-labelledby="recent-invoices-title">
                        <div class="dashboard-panel-heading">
                            <div>
                                <h2 id="recent-invoices-title">Recent invoices</h2>
                                <p class="dashboard-muted">Latest invoices issued for this event</p>
                            </div>
                            <a class="dashboard-panel-link" href="{{ route('adviceNotes.index') }}">View all invoices <i class="icon-arrow-right13" aria-hidden="true"></i></a>
                        </div>

                        @if ($recentInvoices->isEmpty())
                            <p class="dashboard-empty">No active invoices for this event yet.</p>
                        @else
                            <div class="table-responsive dashboard-invoices" tabindex="0" role="region" aria-labelledby="recent-invoices-title">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Invoice</th>
                                            <th scope="col">Company</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-right">Total</th>
                                            <th scope="col" class="text-right">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentInvoices as $invoice)
                                            @php
                                                $balance = max(0, $invoice->balance ?? ($invoice->total_amount - $invoice->amount_paid));
                                                $status = match (true) {
                                                    $invoice->advice_note_status === 'REJECTED' => 'Rejected',
                                                    $balance <= 0 => 'Paid',
                                                    (bool) $invoice->control_number => 'Payment due',
                                                    default => 'Awaiting control number',
                                                };
                                            @endphp
                                            <tr>
                                                <th scope="row">
                                                    <a href="{{ route('adviceNotes.show', ['adviceNote' => $invoice->advice_note_id]) }}">{{ $invoice->advice_note_id }}</a>
                                                    @if ($invoice->advice_note_created_at)
                                                        <span class="dashboard-muted dashboard-invoice-date">{{ \Illuminate\Support\Carbon::parse($invoice->advice_note_created_at)->format('d M Y') }}</span>
                                                    @endif
                                                </th>
                                                <td>{{ $invoice->companyName ?: '—' }}</td>
                                                <td><span class="dashboard-invoice-status {{ $status === 'Paid' ? 'is-paid' : '' }}">{{ $status }}</span></td>
                                                <td class="text-right dashboard-money">{{ $invoice->currency }} {{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                                                <td class="text-right dashboard-money">{{ $invoice->currency }} {{ number_format($balance, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </section>
                @endif

                @if ($participation->isNotEmpty() || $exhibitionStats)
                    <div class="dashboard-charts">
                        @if ($participation->isNotEmpty())
                            <section class="dashboard-panel" aria-labelledby="participation-chart-title">
                                @include('Dashboards.StatusBars', [
                                    'title' => 'Exhibitor participation',
                                    'titleId' => 'participation-chart-title',
                                    'description' => 'Exhibitor registrations across the latest editions',
                                    'bars' => $participation,
                                    'maximum' => $participation->max('value'),
                                ])
                                <a class="dashboard-panel-link" href="{{ route('exhibitors.index') }}">View exhibitors <i class="icon-arrow-right13" aria-hidden="true"></i></a>
                            </section>
                        @endif

                        @if ($exhibitionStats)
                            @include('Dashboards.ExhibitionOverview')
                        @endif
                    </div>
                @endif

                @include('Dashboards.QuickActions')
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
