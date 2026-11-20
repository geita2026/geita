<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsorship Booking</title>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-server position-left"></i> Home</a></li>
                <li class="active"> Book Sponsorship Packages </li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <div class="content">
        <div class="panel panel-white">
            <div class=" panel-body">

                @php $i=0; @endphp
                @foreach ($booked_package as $data)
                    @php $i++; @endphp
                @endforeach

                <div class=" form-group col-md-12">

                    <div class=" form-group col-md-12 table-header">
                        <h4 class="panel-title text-bold text-center p-10">
                               <b>{{ $data->companyName ?? 'SPONSORSHIP' }}</b> BOOKINGS
                        </h4>
                    </div>
                </div>

                @if ($i == 0)
                <div class=" row">
                    <div class=" form-group col-md-12">
                        <form action="{{ route('book-packages.store') }}" method="POST">
                            @csrf
                            <div class="">
                                
                                    <div class=" form-group col-md-6">
                                        <label>Select Cusomer / Company Name <i class=" text-danger">*</i></label>
                                        <select id="search-select" name="customer_name" required>
                                        </select>
                                        <span class="text-danger"> @error('customer_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                <div class=" form-group col-md-6" id="package">
                                    <label>Select Package Name<i class=" text-danger">*</i></label>
                                    <select id="type" name="package_name" class="bootstrap-select "
                                        data-live-search="true" data-width="100%">
                                        <option value="">--Select Sponsorship package--</option>
                                        @foreach ($packages as $value)
                                            <option value="{{ $value->package_id }}">{{ $value->package_name }} -
                                                {{ number_format($value->package_amount) }}
                                                {{ $value->type_of_currency }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger"> @error('package_name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <input type="hidden" name="booking" value="booking">
                                <div class="form-group  text-right col-md-12 mt-20">
                                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                            class="ladda-label"> Book Package</span></button>
                                </div>
                            </div>
                        </form>
                    </div>

                    @endif


                    {{--  booked services  --}}
                    <div class=" form-group col-md-12">
                        <table class=" table  table-bordered table-striped  table-lg text-nowrap">
                            <thead>
                                <tr>
                                    
                                    @if ($i > 0)
                                        <th colspan="6" class="text-uppercase text-bold">
                                            <code>PACKAGE BENEFITS:</code> {{ $packageBenefits }}
                                        </th>
                                    @endif
                                </tr>
                                <tr>
                                    <th class=" text-bold text-uppercase text-center">Package Name</th>
                                    <th class=" text-bold text-uppercase text-center">Customer Amount</th>
                                    <th class=" text-bold text-uppercase text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($booked_package as $item)
                                    <tr>
                                        <td>{{ $item->package_name }}</td>
                                        <td class=" text-right">{{ number_format($item->customer_amount, 2) }}
                                            {{ $item->type_of_currency }}</td>
                                        <td class=" text-center">
                                            <form
                                                action="{{ route('sponsorship-bookings.destroy', ['sponsorship_booking' => $item->booking_id]) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type=" submit"
                                                    class=" btn btn-default border-warning text-danger">Remove <i
                                                        class="icon-cross"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach

                                @if ($i > 1)
                                    <form action="{{ route('adviceNotes.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="company_id" value="{{ $item->companyID }}">
                                        <input type="hidden" name="event" value="{{ $item->exhibition_year }}">
                                        <input type="hidden" name="advice_note" value="ok">
                                        <input type="hidden" name="source" value="sponsor">
                                        <input type="hidden" name="advice_note_description" value="{{ $packageBenefits }}">
                                        <tr>
                                            <td colspan="6">
                                                <div class=" text-right">
                                                    <button type="submit" class=" btn btn-primary ">
                                                        Confirm Booking <i class=" icon-arrow-right13"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </form>
                                @endif

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- /content area -->
    </div>
    <!-- /main content -->
</x-app-layout>

{{--  --}}

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
            placeholder: 'Search Companies by company name or Tantrade Service Registration Number',
            minimumInputLength: 0,
        });
    });
</script>
