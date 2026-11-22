@if (empty($invoicePdf) && empty($invoiceStylesLoaded))
    @once
        <link rel="stylesheet" href="{{ asset('assets/css/exhibition-invoice.css') }}">
    @endonce
@endif

@php
    $emblem = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/nembo.png')));
    $subtotal = $particulars->sum(fn ($line) => $line->particular_quantity * $line->cost_for_one_particular);
    $vat = $item->vat ? round($subtotal * 0.18, 2) : 0;
    $invoiceTotal = (float) ($item->total_amount ?? ($subtotal + $vat));
    $paid = (float) $item->amount_paid;
    $status = $item->advice_note_status === 'CANCELED' ? 'Canceled'
        : ($invoiceTotal > 0 && $paid >= $invoiceTotal ? 'Paid' : ucfirst(strtolower(str_replace('_', ' ', $item->advice_note_status ?? ''))));
    $bankAccounts = $item->control_number
        ? collect($transfer ?? [])->where('account_currency', $item->currency)
        : collect();
@endphp

<article class="geita-invoice" aria-label="Invoice {{ $item->advice_note_id }}">
    <header class="invoice-letterhead">
        <img class="invoice-emblem" src="{{ $emblem }}" alt="National emblem of Tanzania">
        <div class="invoice-country">United Republic of Tanzania</div>
        <h2>Geita Mineral Exhibition</h2>
        <p>GeMEMIS - Geita Mineral Exhibition Management Information System</p>
    </header>

    <div class="invoice-title-row">
        <h3>Invoice @if ($status)<span class="invoice-status">{{ $status }}</span>@endif</h3>
        <strong>{{ $item->advice_note_id }}</strong>
    </div>

    <table class="invoice-meta">
        <tr>
            <td>
                <div class="invoice-label">Billed to</div>
                <strong>{{ $item->companyName }}</strong><br>
                @if ($item->registrationID) Registration ID {{ $item->registrationID }}<br> @endif
                @if ($item->companyAddress || $item->companyLocation)
                    {{ $item->companyAddress }} {{ $item->companyLocation }}<br>
                @endif
                @if ($item->companyEmail) {{ $item->companyEmail }}<br> @endif
                @if ($item->companyPhone) {{ $item->companyPhone }}<br> @endif
                @if ($item->tinNo) TIN {{ $item->tinNo }} @endif
            </td>
            <td>
                <div class="invoice-label">Invoice details</div>
                <strong>Issue date</strong> {{ $item->advice_note_created_at ? date('d M Y', strtotime($item->advice_note_created_at)) : 'Pending' }}<br>
                <strong>Payment deadline</strong> {{ $item->advice_note_deadline ? date('d M Y', strtotime($item->advice_note_deadline)) : 'Pending' }}<br>
                <strong>Control number</strong> {{ $item->control_number ?? 'Pending' }}
            </td>
        </tr>
    </table>

    <p class="invoice-description">{{ $item->advice_note_description ?: 'Charges for ' . ($item->event_name ?: 'the Geita Mineral Exhibition') . '.' }}</p>

    <table class="invoice-items">
        <thead>
            <tr>
                <th scope="col">Item</th>
                <th scope="col" class="invoice-quantity">Qty</th>
                <th scope="col" class="invoice-amount">Unit price</th>
                <th scope="col" class="invoice-amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($particulars as $line)
                <tr>
                    <td>
                        {{ $line->particular_name }}
                        @if ($line->particular_category || $line->particular_description)
                            <div class="invoice-muted">{{ $line->particular_category }} {{ $line->particular_description }}</div>
                        @endif
                    </td>
                    <td class="invoice-quantity">{{ number_format($line->particular_quantity, 2) }}</td>
                    <td class="invoice-amount">{{ $line->type_of_currency }} {{ number_format($line->cost_for_one_particular, 2) }}</td>
                    <td class="invoice-amount">{{ $line->type_of_currency }} {{ number_format($line->particular_quantity * $line->cost_for_one_particular, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="invoice-totals">
        <tr><td>Subtotal</td><td class="invoice-amount">{{ $item->currency }} {{ number_format($subtotal, 2) }}</td></tr>
        @if ($vat > 0)
            <tr><td>VAT (18%)</td><td class="invoice-amount">{{ $item->currency }} {{ number_format($vat, 2) }}</td></tr>
        @endif
        <tr><td>Total</td><td class="invoice-amount">{{ $item->currency }} {{ number_format($invoiceTotal, 2) }}</td></tr>
        <tr><td>Amount paid</td><td class="invoice-amount">{{ $item->currency }} {{ number_format($paid, 2) }}</td></tr>
        <tr class="invoice-total"><td>Balance due</td><td class="invoice-amount">{{ $item->currency }} {{ number_format($invoiceTotal - $paid, 2) }}</td></tr>
    </table>

    @if ($item->control_number)
        <div class="invoice-payment">
            <h4 class="invoice-label">Payment</h4>
            <p>Use control number <strong>{{ $item->control_number }}</strong> as the payment reference.</p>
            @foreach ($bankAccounts as $account)
                <p>{{ $account->bank_name }} · {{ $account->account_name }} · {{ $account->account_number }}
                    @if ($account->swift_code) · SWIFT {{ $account->swift_code }} @endif
                </p>
            @endforeach
        </div>
    @endif

    <footer class="invoice-footer">
        <img src="data:image/png;base64,{{ base64_encode(QrCode::size(65)->generate(route('verify', ['data' => $item->advice_note_id]))) }}"
            alt="Invoice verification QR code">
        <div>{{ $item->advice_note_id }}</div>
        <div>Generated by GeMEMIS for the Geita Mineral Exhibition</div>
    </footer>
</article>
