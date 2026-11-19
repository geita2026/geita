@extends('pdf.layout')
@section('title', 'Payment Receipt')
@section('number', $payment->receipt_number)
@section('content')
@php($invoice = $payment->invoice)
<table class="meta">
  <tr><td class="k">Received from</td><td><strong>{{ $invoice->exhibitor->organization_name }}</strong></td></tr>
  <tr><td class="k">Exhibition</td><td>{{ $invoice->exhibition->name }}</td></tr>
  <tr><td class="k">Invoice</td><td>{{ $invoice->number }} (control no. {{ $invoice->control_number }})</td></tr>
  <tr><td class="k">Payment reference</td><td>{{ $payment->reference }}</td></tr>
  <tr><td class="k">Transaction reference</td><td>{{ $payment->payer_reference }}</td></tr>
  <tr><td class="k">Method</td><td>{{ ucwords(str_replace('_', ' ', $payment->method)) }}</td></tr>
  <tr><td class="k">Date paid</td><td>{{ $payment->paid_on->format('d M Y') }}</td></tr>
  <tr><td class="k">Verified</td><td>{{ $payment->verified_at?->format('d M Y H:i') }} — {{ $payment->verifier?->name ?? 'Payment gateway' }}</td></tr>
</table>
<div class="box" style="text-align:center; padding:22px">
  <div class="muted" style="text-transform:uppercase; letter-spacing:1.5px; font-size:9px">Amount received</div>
  <div style="font-size:26px; font-weight:bold; margin-top:4px">{{ $invoice->currency }} {{ number_format($payment->amount, 2) }}</div>
</div>
<table class="totals">
  <tr><td>Invoice total</td><td class="num">{{ number_format($invoice->total, 2) }}</td></tr>
  <tr><td>Paid to date</td><td class="num">{{ number_format($invoice->amount_paid, 2) }}</td></tr>
  <tr class="grand"><td>Balance</td><td class="num">{{ number_format($invoice->balance, 2) }}</td></tr>
</table>
@endsection
