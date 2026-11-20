<x-app-layout>
    <title>{{ env('APP_NAME') }} | Off Fair Booking</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Home</a></li>
                <li class="active">Make Space Booking</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">
            <div class=" panel-body ">
                <div class="">
                    {{-- New booking modal --}}
                    {{-- table for available customers in database --}}
                    <div class=" form-group row">
                        <div class=" col-md-12 table-responsive">
                            {{-- datatable-save-state --}}
                            <table class=" table table-bordered table-striped">
                                <thead class="">
                                    @foreach ($booked_spaces as $customer)
                                        <tr>
                                            <th colspan="6" class="text-center text-monospace">
                                                <h4>
                                                    <b class="text-center text-uppercase text-primary">{{ $customer->customer_name }}
                                                        BOOKED</b> SPACE
                                                    FROM
                                                    <b>{{ date('d-m-Y', strtotime($customer->start_date ?? date('d-m-Y'))) }}</b>
                                                    TO
                                                    <b>{{ date('d-m-Y', strtotime($customer->end_date ?? date('d-m-Y'))) }}</b>
                                                </h4>
                                            </th>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th class=" text-bold text-center">ITEM NAME</th>
                                        <th class=" text-bold text-center">Qty</th>
                                        <th class=" text-bold text-center">Unit Cost (TZS)</th>
                                        <th class=" text-bold text-center">Durations (Days / Moths)</th>
                                        <th class=" text-bold text-center">SUB TOTAL COST (TZS)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 0;
                                        $vat = 0;
                                    @endphp
                                    @foreach ($booked_spaces as $item)
                                        <tr>
                                            <td>{{ $item->space_name }}</td>

                                            <td>{{ number_format($item->space_size) }}</td>
                                            {{-- display calculated space cost from above logics --}}

                                            @php
                                                if ($item->activity_name == 'RELIGIOUS ACTIVITIES') {
                                                    $spaceCost = $item->activity_cost;
                                                } else {
                                                    $spaceCost = $item->space_cost;
                                                }
                                            @endphp
                                            <td class=" text-right">{{ number_format($spaceCost, 2) }}</td>
                                            <td>{{ $item->durationCount }}</td>
                                            <td class="text-right">
                                                {{ number_format($total1 = $item->space_size * $item->durationCount * $spaceCost, 2) }}
                                            </td>
                                        </tr>
                                        @php $total2=0; @endphp
                                        @foreach ($utilities as $data)
                                            <tr>
                                                <td>{{ $data->utility_name }} </td>
                                                <td>1</td>
                                                <td class=" text-right">{{ number_format($data->utility_cost, 2) }}
                                                </td>
                                                <td>{{ $item->durationCount }}</td>
                                                <td class=" text-right">
                                                    {{ number_format($item->durationCount * $data->utility_cost, 2) }}
                                                </td>
                                            </tr>
                                            @php
                                                $total2 = $total2 + $item->durationCount * $data->utility_cost; #calculates costs for all utilities
                                            @endphp
                                        @endforeach
                                        {{-- /end utilities --}}
                                        @if (session('month') == 1)
                                            <tr>
                                                <td colspan="4" class=" text-right">VAT 18 % </td>
                                                <td class=" text-right">
                                                    <div>
                                                        {{ number_format(($vat = $total1 + $total2) * 0.18, 2) }} TZS
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        @php
                                            $subTotal = $total1 + $total2;
                                            $vat = $subTotal * 0.18;
                                        @endphp
                                        <tr>
                                            <td colspan="5" class=" text-right">
                                                <div style="font-size: 18px;">OVER ALL TOTAL COST &equals;
                                                    <b>{{ number_format($vat + $subTotal, 2) }}</b> TZS
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=" text-center" colspan="5">
                                                <div class=" row">
                                                    {{-- cancel booking codes --}}
                                                    <div class="col-md-6">
                                                        <a class=" btn btn-danger text-bold btn-ladda btn-ladda-spinner"
                                                            onclick=" event.preventDefault();document.getElementById('remove').submit();"
                                                            ondblclick="false"> CANCEL BOOKING</a>
                                                        <form
                                                            action="{{ route('off-fair-space-booking.destroy', ['off_fair_space_booking' => $item->off_fair_booking_id]) }}"
                                                            method="post" id="remove">@csrf @method('DELETE')
                                                            <input type="hidden" name="space_id"
                                                                value="{{ $item->space_id }}">
                                                        </form>
                                                    </div>
                                                    {{-- end cancel booking codes --}}

                                                    {{-- confirm booking --}}
                                                    <div class="col-md-6">
                                                        <form action="{{ route('Off-Fair-Advice-Notes.store') }}"
                                                            method="POST">
                                                            @csrf
                                                            {{-- for tenants --}}
                                                            <input type="hidden" name="vat"
                                                                value="{{ session('month') }}">
                                                            <input type="hidden" name="customer_name"
                                                                value="{{ $item->off_fair_customer_id }}">
                                                            <input type="hidden" name="booking_id"
                                                                value="{{ $item->off_fair_booking_id }}">
                                                            <input type="hidden" name="space_id"
                                                                value="{{ $item->off_fair_space_id }}">


                                                            @if (session('month') == 0 or session('month') == 1)
                                                                <button type="submit"
                                                                    class="btn btn-success text-bold btn-ladda btn-ladda-spinner"
                                                                    title=" Click to confirm your booking">CONFIRM
                                                                    BOOKING</button>
                                                            @endif


                                                        </form>
                                                    </div>
                                                    {{-- end confirming booking --}}
                                                </div>
                                            </td>
                                        </tr>

                                        @php $i++; @endphp
                                    @endforeach
                                    @if ($i == 0)
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h4 class="modal-title text-bold text-center">OFF FAIR SPACE BOOKING
                                                    </h5>
                                            </div>
                                            <div class="modal-body">

                                                <div class=" form-group">
                                                    <code>Note!</code> fields with " <i class=" text-danger">*</i> "
                                                    mark are required
                                                </div>

                                                <form action="{{ route('off-fair-space-booking.store') }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row form-group">
                                                        <div class=" col-md-12 form-group">
                                                            <label>Select Space to be booked<i class=" text-danger">*</i></label>
                                                            <select id="searchOffFairSpace" name="selectedSpace" class=" form-control" required>
                                                            </select>
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Customer Name<i class=" text-danger">*</i> If your
                                                                Company is not in list <a
                                                                    href="{{ route('companies.create') }}"
                                                                    class=" text-bold">Click Here</a> to
                                                                Register</label>

                                                                <select id="search-select" name="customer_name" class=" form-control">
                                                                </select>
                                                            
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Select Activities Which will be done<i
                                                                    class=" text-danger">*</i></label>
                                                            <select name="customer_activity" class=" form-control"
                                                                required>
                                                                <option value="">- - - Select Activity from list -
                                                                    - -</option>
                                                                @foreach ($activities as $item)
                                                                    <option value="{{ $item->activity_id }}"
                                                                        class=" text-uppercase">
                                                                        {{ $item->activity_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Booked From<i class=" text-danger">*</i></label>
                                                            <input type="date" name="start_date"
                                                                value="{{ date('Y-m-d', strtotime(session('start_date') ?? date('d-m-Y'))) }}"
                                                                class=" form-control" placeholder=" " required>

                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Booked To<i class=" text-danger">*</i><span
                                                                    class=" text-muted"> (Repeat Booked From Date if
                                                                    the
                                                                    event is for one Day)</span></label>
                                                            <input type="date" name="end_date"
                                                                class=" form-control" placeholder=" Customer Email"
                                                                value="{{ date('Y-m-d', strtotime(session('end_date') ?? date('d-m-Y'))) }}"
                                                                required>

                                                        </div>
                                                    </div>
                                                    
                                            </div>
                                            <div class=" modal-footer">
                                                <div class="  form-group text-right">
                                                    <button type="submit"
                                                        class="btn btn-primary border-primary btn-ladda btn-ladda-spinner"><span
                                                            class="ladda-label">Book Space</span></button>
                                                </div>
                                            </div>
                                            </form>
                                        </div>
                                    @endif
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- //user registration  --}}
</x-app-layout>

<script>
    $(document).ready(function() {
        $('#search-select').select2({
            ajax: {
                url: '{{ route('search_companies') }}', // URL for the AJAX request
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // Search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.companyID,
                                text: item.registrationID + ' || ' + item.companyName +
                                    ' || ' + item.companyPhone + ' || ' + item.companyEmail,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Companies (write company name or Tantrade Service Registration Number) . . .',
            minimumInputLength: 0,
        });
    });
</script>



<script>
    $(document).ready(function() {
        $('#searchOffFairSpace').select2({
            ajax: {
                url: '{{ route('off-fair-space-booking.create') }}', // URL for the AJAX request
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // Search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.space_id,
                                text: item.space_name+' || '+item.space_cost+' || '+ item.spaceCostWeekend,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Off Fair Spaces . . .',
            minimumInputLength: 0,
        });
    });
</script>
