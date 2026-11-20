<x-app-layout>
    <title>{{ env('APP_NAME') }} | Service Booking</title>

    <style>
        .section-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 6px 18px rgba(0,0,0,.06);
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .section-note {
            font-size: 13px;
            color: #b91c1c;
            margin-bottom: 16px;
        }

        table th {
            background: #f8fafc;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .total-row {
            font-size: 16px;
            font-weight: 700;
            color: #2563eb;
        }

        .form-footer {
            margin-top: 16px;
            text-align: right;
        }
    </style>

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>

        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li>
                    <a href="#">
                        <i class="icon-home position-left"></i> Home
                    </a>
                </li>
                <li class="active">Service Booking</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="content">

        {{-- ================= BOOKING FORM ================= --}}
        <div class="section-card">

            <div class="section-title text-center">
                Exhibition Extra Services Booking
            </div>

            <div class="section-note text-center">
                Note: Extra services can only be booked after exhibitor confirmation.
            </div>

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('book-services.store') }}" method="POST">
                @csrf

                <div class="row">

                    {{-- CHECK EXISTING CUSTOMER --}}
                    @php $hasCustomer = false; @endphp
                    @foreach ($booked_services as $item)
                        <input type="hidden" name="customer_name" value="{{ $item->company_id }}">
                        @php $hasCustomer = true; @endphp
                    @endforeach

                    {{-- CUSTOMER SELECT (INTERNAL USERS) --}}
                    @if (!$hasCustomer)
                        <div class="col-md-6 form-group">
                            <label>Select Customer (Company) <i class="text-danger">*</i></label>
                            <select id="search-select" name="customer_name" class="form-control" required></select>
                        </div>
                    @endif

                    {{-- SERVICE SELECT --}}
                    <div class="col-md-4 form-group">
                        <label>Select Service <i class="text-danger">*</i></label>
                        <select name="service_name" class="bootstrap-select"
                                data-live-search="true" data-width="100%" required>
                            <option value="">— Select Service —</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->serviceID }}">
                                    {{ $service->serviceName }}
                                    — {{ number_format($service->serviceCost,2) }}
                                    {{ $service->type_of_currency }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- QUANTITY --}}
                    <div class="col-md-2 form-group">
                        <label>Quantity <i class="text-danger">*</i></label>
                        <input type="number" name="service_quantity"
                               class="form-control" min="1" value="1" required>
                    </div>

                    {{-- EXHIBITION YEAR --}}
                    @foreach ($services as $val) @endforeach
                    <input type="hidden" name="exhibition_year" value="{{ $val->service_event ?? '' }}">

                    <div class="col-md-12 form-footer">
                        <button class="btn btn-primary">
                            Add Service <i class="icon-plus2"></i>
                        </button>
                    </div>

                </div>
            </form>
        </div>

        {{-- ================= BOOKED SERVICES ================= --}}
        <div class="section-card">

            <div class="section-title text-center">
                @if (Auth::user()->account_type == 1 && isset($item))
                    Services Booking For <span class="text-primary">{{ $item->companyName ?? '' }}</span>
                @else
                    List of Booked Services
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Service</th>
                            <th class="text-center">Qty</th>
                            <th>Unit Cost</th>
                            <th>Sub Total</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php $i = 1; $subtotal = 0; @endphp
                        @foreach ($booked_services as $item)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $item->serviceName }}</td>
                                <td class="text-center">{{ $item->service_quantity }}</td>
                                <td>{{ number_format($item->serviceCost,2) }}</td>
                                <td>
                                    {{ number_format($item->serviceCost * $item->service_quantity,2) }}
                                    {{ $item->type_of_currency }}
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('book-services.destroy',['book_service'=>$item->service_charge_id]) }}"
                                          method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            Remove <i class="icon-cross2"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @php
                                $subtotal += $item->serviceCost * $item->service_quantity;
                                $i++;
                            @endphp
                        @endforeach

                        <tr>
                            <td colspan="4" class="text-right">VAT (18%)</td>
                            <td>{{ number_format($vat = $subtotal * 0.18,2) }} {{ $item->type_of_currency ?? '' }}</td>
                            <td></td>
                        </tr>

                        <tr class="total-row">
                            <td colspan="4" class="text-right">Total Cost</td>
                            <td>{{ number_format($subtotal + $vat,2) }} {{ $item->type_of_currency ?? '' }}</td>
                            <td></td>
                        </tr>

                    </tbody>
                </table>
            </div>

            {{-- CONFIRM BOOKING --}}
            @if ($i > 1)
                <form action="{{ route('adviceNotes.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="company_id" value="{{ $item->company_id }}">
                    <input type="hidden" name="exhibition_year" value="{{ $item->exhibition_year }}">
                    <input type="hidden" name="event" value="{{ $item->service_event }}">
                    <input type="hidden" name="advice_note" value="advice_note">
                    <input type="hidden" name="vat" value="1">

                    <div class="form-footer">
                        <button type="submit" class="btn btn-success">
                            Confirm Booking
                        </button>
                    </div>
                </form>
            @endif

        </div>

    </div>
</x-app-layout>

{{-- CUSTOMER SEARCH --}}
<script>
    $(document).ready(function () {
        $('#search-select').select2({
            ajax: {
                url: '{{ route('search-exhibitors') }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({
                    results: data.map(item => ({
                        id: item.companyID,
                        text: item.registrationID + ' | ' + item.companyName + ' | ' +
                              item.companyPhone + ' | ' + item.companyEmail
                    }))
                })
            },
            placeholder: 'Search exhibitors',
            minimumInputLength: 0
        });
    });
</script>
