<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsorship</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">

            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('customer_packages.index') }}"><i class=" icon-package position-left"></i> CUSTOMER
                        PACKAGES</a></li>
                <li class="active">View Customer packages</li>
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
            <div class="panel-heading">
                <h6 class="panel-title text-bold">BOOKED SPONSORSHIP PACKAGES</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body table-responsive">

                <div class=" col-md-12 form-group">
                        <select id="search-select" name="search" class=" form-control"
                            onchange="location = this.value;">
                        </select>
                    </div>

                <div class=" form-group col-md-12">
                    <table class="table  table-responsive table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class=" text-bold">Company Name</th>
                                <th class=" text-bold">Package Name</th>
                                <th class=" text-bold">Amount Paid</th>
                                <th class=" text-bold">Balance</th>
                                <th class=" text-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($packages as $item)
                                <tr>
                                    <td>{{ $item->companyName }}</td>
                                    <td>{{ $item->package_name }} <b class=" text-warning">[ {{ number_format($item->total_amount, 2) }} {{ $item->type_of_currency }} ]</b></td>
                                    <td class=" text-right">{{ number_format($item->amount_paid,2) }} {{ $item->type_of_currency }}</td>
                                    <td class=" text-right">{{ number_format($item->balance,2) }} {{ $item->type_of_currency }}</td>
                                    <td class="text-center">
                                       <a href="{{ route('adviceNotes.show',['adviceNote'=>$item->advice_note_id]) }}" class=" btn btn-primary">Preview Invoice <i class=" icon-eye"></i></a>
                                    </td>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class=" col-md-12 text-right">
                    {{ $packages->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    $(document).ready(function() {
        $('#search-select').select2({
            ajax: {
                url: '{{ route('search_invoices') }}', // URL for the AJAX request
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
                                id: '{{ env('APP_URL') }}/accountingManagement/adviceNotes/' +
                                    item.advice_note_id,
                                text: item.advice_note_id + ' || ' + item.companyName +
                                    ' || ' + item.control_number + ' || ' +
                                    numberWithCommas(item.total_amount) + ' ' + item
                                    .currency,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Invoices By company name or Control Number or Invoice Number . . .',
            minimumInputLength: 0,
        });
    });

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>