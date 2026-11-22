<x-app-layout>
    <title>{{ env('APP_NAME') }} | Invoice</title>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class=" icon-home position-left"></i>Home</a></li>
                <li class="active">View All Invoices</li>
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
            <div class=" panel-body">
                <div class=" form-group mt-20 table-responsive">
                    <div class=" col-md-12">
                        <h4 class=" text-center text-default text-bold">LIST OF ON FAIR INVOICES</h4>

                    </div>
                    <div class=" col-md-12 form-group">
                        <select id="search-select" name="search" class=" form-control"
                            onchange="location = this.value;">
                        </select>
                    </div>

                    <div class=" form-group col-md-12">
                        <table data-table-paginated class="table table-bordered table-striped table-responsive">
                            <thead>
                                <tr>
                                    <th class=" text-bold text-center">INVOICE ID</th>
                                    <th class=" text-bold text-center">CUSTOMER NAME</th>
                                    <th class=" text-bold text-center">CONTROL NO</th>
                                    <th class=" text-center text-bold">INVOICED AMOUNT</th>
                                    <th class=" text-center text-bold">AMOUNT PAID</th>
                                    <th class=" text-center text-bold">BALANCE</th>
                                    <th class=" text-center text-bold">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach ($advice_note as $item)
                                    <tr>
                                        <td><a href="{{ route('adviceNotes.show', ['adviceNote' => $item->advice_note_id]) }}"
                                                class=" text-bold"
                                                title="Click here to view {{ $item->advice_note_id }} Advice Note details">{{ $item->advice_note_id }}</a>
                                        </td>
                                        <td>{{ $item->companyName }}</td>
                                        <td class=" text-center"
                                            title="Generation Date: {{ date('M d, Y', strtotime($item->advice_note_created_at)) }}. Expire Date: {{ date('M d, Y', strtotime($item->advice_note_deadline)) }}">
                                            <a
                                                href="{{ route('adviceNotes.show', ['adviceNote' => $item->advice_note_id]) }}">
                                                @if ($item->sp_invoice_no == null)
                                                    <i class="text-brown-300"> Not Requested </i>
                                                @elseif ($item->control_number == null)
                                                    <i class="text-brown-300">Failed to fetch control number</i>
                                                @else
                                                    <b>{{ trim(strrev(chunk_split(strrev($item->control_number), 3))) }}</b>
                                                @endif
                                            </a>
                                        </td>

                                        <td class=" text-right"> {{ number_format($item->total_amount, 2) }}
                                            {{ $item->currency }}</td>
                                        <td class=" text-right">{{ number_format($item->amount_paid, 2) }}
                                            {{ $item->currency }}</td>
                                        <td
                                            class=" text-right @if ($item->balance > 0) text-warning @else text-success @endif">
                                            {{ number_format($item->balance, 2) }} {{ $item->currency }}</td>

                                        <td class=" text-center">
                                            <a href="{{ route('adviceNotes.show', ['adviceNote' => $item->advice_note_id]) }}"
                                                class=" text-bold btn btn-default text-primary"
                                                title="Click here to view {{ $item->advice_note_id }} Advice Note details">View
                                                Invoice <i class=" icon-file-empty2"></i></a>
                                        </td>


                                    </tr>
                                    @php $i++; @endphp
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    <div class=" col-md-12 text-right form-group">
                        {{ $advice_note->withQueryString()->links() }}
                    </div>

                </div>

            </div>
        </div>
    </div>
    <!-- /content area -->
    <!-- /main content -->
</x-app-layout>

<script>
    $(document).ready(function() {
        const invoiceUrl = @json(route('adviceNotes.show', ['adviceNote' => '__INVOICE_ID__']));
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
                                id: invoiceUrl.replace('__INVOICE_ID__', encodeURIComponent(item.advice_note_id)),
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
