<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $item->advice_note_id }}</title>

    <style>
        @page {
            margin: 25mm 15mm 18mm 15mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .right { text-align: right; }
        .center { text-align: center; }
        .muted { color: #6b7280; font-size: 10px; }

        .header-line-blue { height: 5px; background: #1d4ed8; }
        .header-line-green { height: 5px; background: #16a34a; margin-bottom: 14px; }

        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .box {
            border: 1px solid #e5e7eb;
            padding: 10px;
            border-radius: 6px;
        }

        .items th {
            background: #f1f5f9;
            font-size: 11px;
            text-transform: uppercase;
            border: 1px solid #d1d5db;
            padding: 6px;
        }

        .items td {
            border: 1px solid #e5e7eb;
            padding: 6px;
        }

        .totals td {
            padding: 5px;
            font-size: 12px;
        }

        .totals .grand {
            font-size: 13px;
            font-weight: bold;
        }

        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px;
            font-weight: bold;
            color: rgba(0,0,0,0.07);
            z-index: -1;
            pointer-events: none;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 12mm;
            font-size: 9px;
            color: #374151;
            border-top: 1px solid #e5e7eb;
        }

        .footer-table td {
            padding: 2px 6px;
            vertical-align: middle;
        }

        .pagenum:before { content: counter(page); }
        .pagecount:before { content: counter(pages); }
    </style>
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>

<body>

{{-- ================= WATERMARK ================= --}}
@if ($item->advice_note_status === 'CANCELED')
    <div class="watermark">CANCELLED</div>
@elseif ($item->amount_paid >= $item->total_amount && $item->total_amount > 0)
    <div class="watermark">PAID</div>
@endif

{{-- ================= HEADER ================= --}}
<table>
    @include('reports.reports_layouts.report_header')
</table>

<div class="header-line-blue"></div>
<div class="header-line-green"></div>

{{-- ================= META ================= --}}
<table>
    <tr>
        <td>
            <div class="invoice-title">INVOICE</div>
            <div class="muted">Invoice #: {{ $item->advice_note_id }}</div>
            <div class="muted">
                Control No:
                <strong style="letter-spacing:2px;color:#1d4ed8;">
                    {{ $item->control_number ?? 'Not Issued' }}
                </strong>
            </div>
        </td>
        <td class="right">
            <div class="box">
                <b>Issue Date:</b> {{ date('M d, Y', strtotime($item->advice_note_created_at)) }}<br>
                <b>Deadline:</b> {{ date('M d, Y', strtotime($item->advice_note_deadline)) }}<br>
                <b>Total:</b> {{ number_format($item->total_amount,2) }} {{ $item->currency }}<br>
                <b>Paid:</b> {{ number_format($item->amount_paid,2) }} {{ $item->currency }}<br>
                <b>Balance:</b>
                <span style="color:#dc2626">
                    {{ number_format($item->total_amount - $item->amount_paid,2) }}
                    {{ $item->currency }}
                </span>
            </div>
        </td>
    </tr>
</table>

<br>

{{-- ================= BILL TO ================= --}}
<div class="box">
    <b>Invoiced To</b><br>
    {{ $item->companyName }}<br>
    TSRN: {{ $item->registrationID }}<br>
    {{ $item->companyAddress ?? '' }} {{ $item->companyLocation }}<br>
    Email: {{ $item->companyEmail ?? '-' }} |
    Tel: {{ $item->companyPhone ?? '-' }}<br>
    TIN: {{ $item->tinNo ?? '-' }}
</div>

<br>

<p><b>Description:</b> Charges for participation in {{ $item->event_name }}.</p>

{{-- ================= ITEMS ================= --}}
<table class="items">
    <thead>
        <tr>
            <th>Particular</th>
            <th class="center">Qty</th>
            <th class="right">Unit Price</th>
            <th class="right">Sub Total</th>
        </tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp
        @foreach ($particulars as $p)
            @php
                $line = $p->particular_quantity * $p->cost_for_one_particular;
                $total += $line;
            @endphp
            <tr>
                <td>
                    {{ $p->particular_name }}
                    <div class="muted">{{ $p->particular_category }} @if ($p->particular_description) [{{ $p->particular_description }}] @endif</div>
                </td>
                <td class="center">{{ number_format($p->particular_quantity,2) }}</td>
                <td class="right">{{ number_format($p->cost_for_one_particular,2) }} {{ $p->type_of_currency }}</td>
                <td class="right">{{ number_format($line,2) }} {{ $p->type_of_currency }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>

{{-- ================= TOTALS ================= --}}
@php $vat = $item->vat ? $total * 0.18 : 0; @endphp
<table class="totals">
    <tr>
        <td class="right" width="80%">Sub Total</td>
        <td class="right">{{ number_format($total,2) }} {{ $item->currency }}</td>
    </tr>
    @if ($vat > 0)
    <tr>
        <td class="right">VAT (18%)</td>
        <td class="right">{{ number_format($vat,2) }} {{ $item->currency }}</td>
    </tr>
    @endif
    <tr class="grand">
        <td class="right">TOTAL</td>
        <td class="right">{{ number_format($total + $vat,2) }} {{ $item->currency }}</td>
    </tr>
</table>

<br>

{{-- ================= PAYMENT CHANNEL (PRESERVED) ================= --}}
@if ($item->control_number)
    @include('advice_note.InvoiceLayouts.BankDetails')
@endif

<br>

{{-- ================= QR ================= --}}
<div class="center">
    <img src="data:image/png;base64,{{ base64_encode(
        QrCode::size(55)->generate(env('APP_URL').'/verify/'.$item->advice_note_id)
    ) }}">
    <div class="muted" style="letter-spacing:5px">
        {{ $item->advice_note_id }}
    </div>
</div>

{{-- ================= FOOTER (OWNERSHIP ADDED â€“ SMALL) ================= --}}
<footer>
    <table class="footer-table" width="100%">
        <tr>
            <td>
                Â© {{ date('Y') }} Tanzania Trade Development Authority (TANTRADE) â€¢ System Generated
            </td>
            <td class="right">
                Page <span class="pagenum"></span>/<span class="pagecount"></span>
            </td>
        </tr>
    </table>
</footer>

</body>
</html>

