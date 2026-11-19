@extends('pdf.layout')
@section('title', 'Space Allocation Schedule')
@section('number', $exhibition->code.' — '.$exhibition->name)
@section('content')
<table class="grid">
  <tr><th>#</th><th>Space</th><th>Hall / area</th><th>Category</th><th>Size</th><th>Exhibitor</th><th>Contact</th><th>Application</th><th>Participation</th><th>Occupancy</th></tr>
  @foreach($allocations as $a)
  <tr><td>{{ $loop->iteration }}</td><td><strong>{{ $a->space->number }}</strong></td><td>{{ $a->space->hall->name }}</td><td>{{ $a->space->category->name }}</td>
    <td>{{ $a->space->size_label }}</td><td>{{ $a->exhibitor->organization_name }}</td><td>{{ $a->exhibitor->contact_person }}<br>{{ $a->exhibitor->phone }}</td>
    <td>{{ $a->application->reference }}</td><td>{{ str_replace('_', ' ', $a->application->status) }}</td><td>{{ str_replace('_', ' ', $a->occupancy_status) }}</td></tr>
  @endforeach
</table>
<p class="muted">{{ $allocations->count() }} live allocations.</p>
@endsection
