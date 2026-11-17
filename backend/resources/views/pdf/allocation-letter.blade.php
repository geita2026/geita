@extends('pdf.layout')
@section('title', 'Allocation Letter')
@section('number', $application->reference)
@section('content')
<p>{{ $generatedAt->format('d F Y') }}</p>
<p><strong>{{ $application->exhibitor->organization_name }}</strong><br>
{{ $application->exhibitor->physical_address }}<br>{{ $application->exhibitor->country }}</p>
<p>Attention: {{ $application->exhibitor->contact_person }}</p>
<h2 style="color:#1b1b22; font-size:13px">RE: Allocation of exhibition space — {{ $application->exhibition->name }}</h2>
<p>We are pleased to confirm your participation in <strong>{{ $application->exhibition->name }}</strong>, to be held at
{{ $application->exhibition->venue }} from {{ $application->exhibition->start_date->format('d F Y') }} to {{ $application->exhibition->end_date->format('d F Y') }}.
Payment has been received and verified, and the following space has been allocated to you:</p>
<table class="grid">
  <tr><th>Space</th><th>Hall / area</th><th>Category</th><th>Size</th><th>Facilities</th><th>Allocation ref.</th></tr>
  @foreach($allocations as $a)
  <tr><td><strong>{{ $a->space->number }}</strong></td><td>{{ $a->space->hall->name }}</td><td>{{ $a->space->category->name }}</td>
    <td>{{ $a->space->size_label }} ({{ $a->space->area_sqm }} m²)</td><td>{{ implode(', ', $a->space->facilities ?? []) ?: '—' }}</td><td>{{ $a->reference }}</td></tr>
  @endforeach
</table>
<div class="box">
  <strong>Conditions.</strong> Present this letter at the gate for check-in. The space is for the products declared in your application
  ({{ $application->product_category }}) and may not be sublet or transferred without written authorisation.
  @if($application->exhibition->terms)<br><br>{{ \Illuminate\Support\Str::limit($application->exhibition->terms, 900) }}@endif
</div>
<p style="margin-top:40px">_______________________________<br>Exhibition Management Unit<br>
<span class="muted">{{ $application->exhibition->contact_email }} · {{ $application->exhibition->contact_phone }}</span></p>
@endsection
