<x-app-layout>
    <title>{{ config('app.name') }} | Book Services</title>
    <link rel="stylesheet" href="{{ asset('assets/css/service-booking.css') }}">

    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home position-left"></i> GeMEMIS</a></li>
                <li class="active">Book Services</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    <div class="content service-booking">
        <header class="service-booking-header">
            <h1>Book exhibition services</h1>
            @if ($event)
                <p>{{ $event->event_name }}</p>
            @endif
        </header>

        <div class="service-booking-layout">
            <section class="service-booking-card" aria-labelledby="add-booking-service">
                <h2 id="add-booking-service">Add a service</h2>

                @if (!$bookingOpen)
                    <p class="service-booking-note">Service booking is currently closed.</p>
                @elseif (!$event)
                    <p class="service-booking-note">There is no exhibition selected for service booking.</p>
                @elseif ($services->isEmpty())
                    <p class="service-booking-note">No other services are available for this booking.</p>
                @elseif (!$draft && $companies->isEmpty())
                    <p class="service-booking-note">No eligible companies are registered for this exhibition.</p>
                @else
                    <form id="service-booking-form" action="{{ route('book-services.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="exhibition_year" value="{{ $event->event_id }}">

                        <div class="form-group">
                            @if ($draft)
                                <span class="service-booking-label">Company</span>
                                <div class="service-booking-selection">{{ $draft->companyName ?? 'Selected company' }}</div>
                                <input type="hidden" name="customer_name" value="{{ $draft->company_id }}">
                            @else
                                <label for="service-booking-company">Company</label>
                                <select id="service-booking-company" name="customer_name" class="form-control"
                                    @error('customer_name') aria-invalid="true" aria-describedby="service-company-error" @enderror required>
                                    <option value="">Search by company or registration ID</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->companyID }}" {{ old('customer_name') === $company->companyID ? 'selected' : '' }}>
                                            {{ $company->companyName }}{{ $company->registrationID ? ' · ' . $company->registrationID : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('customer_name')
                                <p id="service-company-error" class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="service-booking-service">Service</label>
                            <select id="service-booking-service" name="service_name" class="form-control"
                                @error('service_name') aria-invalid="true" aria-describedby="service-option-error" @enderror required>
                                <option value="">Search available services</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->serviceID }}" data-price="{{ $service->serviceCost }}"
                                        data-currency="{{ $service->type_of_currency }}"
                                        {{ old('service_name') === $service->serviceID ? 'selected' : '' }}>
                                        {{ $service->serviceName }} · {{ $service->type_of_currency }} {{ number_format($service->serviceCost, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_name')
                                <p id="service-option-error" class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="service-booking-quantity">
                            <div class="form-group">
                                <label for="service-booking-quantity">Quantity</label>
                                <input id="service-booking-quantity" name="service_quantity" type="number"
                                    class="form-control" min="1" step="1" value="{{ old('service_quantity', 1) }}"
                                    @error('service_quantity') aria-invalid="true" aria-describedby="service-quantity-error" @enderror required>
                                @error('service_quantity')
                                    <p id="service-quantity-error" class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <span class="service-booking-label">Line subtotal</span>
                                <output id="service-booking-preview" aria-live="polite">—</output>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Add to booking</button>
                    </form>
                @endif
            </section>

            <section class="service-booking-card" aria-labelledby="booking-summary">
                <h2 id="booking-summary">Booking summary</h2>

                @if (!$draft)
                    <p class="service-booking-note">No services have been added to this booking.</p>
                @else
                    <div class="table-responsive">
                        <table class="table gememis-table" data-table-static>
                            <thead>
                                <tr>
                                    <th scope="col">Service</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Subtotal</th>
                                    @can('CANCEL_SERVICE_BOOKING')
                                        <th scope="col"><span class="sr-only">Actions</span></th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingServices as $line)
                                    <tr>
                                        <td>
                                            {{ $line->serviceName ?? 'Unavailable service' }}
                                            <small class="text-muted">{{ $line->type_of_currency }} {{ number_format($line->serviceCost, 2) }} each</small>
                                        </td>
                                        <td>{{ $line->service_quantity }}</td>
                                        <td>{{ $line->type_of_currency }} {{ number_format($line->serviceCost * $line->service_quantity, 2) }}</td>
                                        @can('CANCEL_SERVICE_BOOKING')
                                            <td>
                                                <form action="{{ route('book-services.destroy', ['book_service' => $line->service_charge_id]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-default btn-sm">Remove</button>
                                                </form>
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @foreach ($pendingServices->groupBy('type_of_currency') as $currency => $lines)
                        @php
                            $subtotal = $lines->sum(fn ($line) => $line->serviceCost * $line->service_quantity);
                            $vat = $subtotal * 0.18;
                        @endphp
                        <dl class="service-booking-totals">
                            <div><dt>Subtotal</dt><dd>{{ $currency }} {{ number_format($subtotal, 2) }}</dd></div>
                            <div><dt>VAT (18%)</dt><dd>{{ $currency }} {{ number_format($vat, 2) }}</dd></div>
                            <div class="service-booking-total"><dt>Total</dt><dd>{{ $currency }} {{ number_format($subtotal + $vat, 2) }}</dd></div>
                        </dl>
                    @endforeach

                    @can('MANAGE_INVOICES')
                        <form action="{{ route('adviceNotes.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="company_id" value="{{ $draft->company_id }}">
                            <input type="hidden" name="exhibition_year" value="{{ $draft->exhibition_year }}">
                            <input type="hidden" name="event" value="{{ $draft->service_event }}">
                            <input type="hidden" name="advice_note" value="advice_note">
                            <input type="hidden" name="vat" value="1">
                            <button type="submit" class="btn btn-primary">Confirm booking</button>
                        </form>
                    @endcan
                @endif
            </section>
        </div>
    </div>
</x-app-layout>

<script src="{{ asset('assets/js/core/service-booking.js') }}"></script>
