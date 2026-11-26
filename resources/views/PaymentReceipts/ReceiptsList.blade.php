<x-app-layout>
    <title>{{ env('APP_NAME') }} | Menu</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-comptwont">
            <ul class="breadcrumb">
                <li><a href="#"><i
                            class=" icon-home position-left"></i>Home</a></li>
                <li class="active">View all customer payments</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- Content area -->
    <div class="content">

        <div class="panel panel-flat">
            <div class=" panel-body">
                <div class=" col-md-12 form-group">
                    <select id="search-select" name="search"  class=" form-control"
                        onchange="location = this.value;" >
                    </select>
                </div>


                <table data-table-paginated class="table table-striped table-bordered  table-responsive">
                    <thead>
                        <tr>
                            <th class=" text-center text-bold">#</th>
                            <th class=" text-center text-bold">COMPANY NAME</th>
                            <th class=" text-center text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($payments as $item)
                            <tr>
                                <td>{{ $item->registrationID }}</td>
                                <td>{{ $item->companyName }} @if(Auth::user()->account_type==1) <b class=" text-success">[ {{ number_format($item->amount_paid,2) }} {{ $item->currency }} ] </b> {{ date('M d, Y h:i:s',strtotime($item->payment_made_on)) }}@endif</td>
                                <td class="text-center">
                                    <a href="{{ route('payments.edit', ['payment' => $item->companyID]) }}"
                                        class=" btn btn-primary">View Payments<i class=" icon-arrow-right13"></i></a>

                                   
                                </td>

                            </tr>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                    </tbody>

                </table>


            </div>
            <div class=" text-right form-group mb-3">
                {{ $payments->withQueryString()->links() }}
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
                                id: '{{ env('APP_URL') }}/accountingManagement/payments/' +
                                    item.company_id+'/edit',
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
            placeholder: 'Search Payments By company name or Control Number or Invoice Number . . .',
            minimumInputLength: 0,
        });
    });

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }  
</script>