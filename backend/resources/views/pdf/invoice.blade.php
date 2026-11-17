@extends('pdf.layout')
@section('title', 'Invoice')
@section('number', $invoice->number)
@section('content')
<table class="meta"><tr>
  <td style="width:55%">
    <h2 style="margin-top:0">Billed to</h2>
    <strong>{{ $invoice->exhibitor->organization_name }}</strong><br>
    {{ $invoice->exhibitor->physical_address }}<br>{{ $invoice->exhibitor->country }}<br>
    @if($invoice->exhibitor->tin) TIN: {{ $invoice->exhibitor->tin }}<br>@endif
    {{ $invoice->exhibitor->contact_person }} · {{ $invoice->exhibitor->phone }}
  </td>
  <td>
    <table class="meta">
      <tr><td class="k">Exhibition</td><td>{{ $invoice->exhibition->name }}</td></tr>
      <tr><td class="k">Application</td><td>{{ $invoice->application->reference }}</td></tr>
      <tr><td class="k">Issued</td><td>{{ $invoice->issued_on->format('d M Y') }}</td></tr>
      <tr><td class="k">Payment deadline</td><td><strong>{{ $invoice->due_on->format('d M Y') }}</strong></td></tr>
      <tr><td class="k">Control number</td><td><strong>{{ $invoice->control_number }}</strong></td></tr>
      <tr><td class="k">Status</td><td><span class="pill {{ $invoice->status === 'paid' ? 'paid' : 'due' }}">{{ str_replace('_', ' ', $invoice->status) }}</span></td></tr>
    </table>
  </td>
</tr></table>

<h2>Charges</h2>
<table class="grid">
  <tr><th>Space</th><th>Description</th><th>Category</th><th>Size</th><th class="num">Unit price</th><th class="num">Qty</th><th class="num">Amount ({{ $invoice->currency }})</th></tr>
  @foreach($invoice->items as $item)
  <tr><td><strong>{{ $item->space_number }}</strong></td><td>{{ $item->description }}</td><td>{{ $item->space_category }}</td><td>{{ $item->space_size }}</td>
    <td class="num">{{ number_format($item->unit_price, 2) }}</td><td class="num">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td><td class="num">{{ number_format($item->amount, 2) }}</td></tr>
  @endforeach
</table>
<table class="totals">
  <tr><td>Subtotal</td><td class="num">{{ number_format($invoice->subtotal, 2) }}</td></tr>
  <tr><td>VAT ({{ rtrim(rtrim(number_format($invoice->vat_rate, 2), '0'), '.') }}%)</td><td class="num">{{ number_format($invoice->vat_amount, 2) }}</td></tr>
  <tr class="grand"><td>Total</td><td class="num">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td></tr>
  <tr><td>Paid</td><td class="num">{{ number_format($invoice->amount_paid, 2) }}</td></tr>
  <tr><td><strong>Balance due</strong></td><td class="num"><strong>{{ number_format($invoice->balance, 2) }}</strong></td></tr>
</table>
<div class="box">
  <strong>How to pay.</strong> Quote control number <strong>{{ $invoice->control_number }}</strong> at the bank or on mobile money, then upload the
  payment evidence against this invoice in GeMEMIS. Participation is confirmed once Finance verifies the payment.
  Unpaid spaces may be released after the payment deadline.
</div>
@endsection
