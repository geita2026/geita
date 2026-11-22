<x-app-layout>
    <title>{{ env('APP_NAME') }} | Invoice</title>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href=""><i class=" icon-file-spreadsheet position-left"></i>INVOICES WITH BALANCE</a></li>
                <li class="active">View All Invoices with balance</li>
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
                    <div class=" col-md-12 table-header form-group">
                        <h4 class="text-center text-default text-bold">LIST OF INVOICES WITH PENDING PAYMENTS</h4>
                    </div>
                    <div class=" form-group ">
                        <select id="search-select" name="search" class=" form-control"
                            onchange="location = this.value;">
                        </select>
                    </div>

                    <div class=" form-group col-md-12">
                        <table class="table table-striped table-bordered  table-responsive">
                            <thead>
                                <tr>
                                    <th colspan="8">
                                        <b>TOTAL AMOUNT: {{ number_format($totalBalance,2) }} TZS</b>
                                    </th>
                                </tr>
                                <tr>
                                    <th class=" text-bold text-center">INVOICE ID</th>
                                    <th class=" text-bold text-center">CUSTOMER NAME</th>
                                    <th class=" text-bold text-center">CONTROL NO</th>
                                    <th class=" text-center text-bold">AMOUNT PAID</th>
                                    <th class=" text-center text-bold">BALANCE</th>
                                    <th class=" text-center text-bold">DEADLINE</th>
                                    <th class=" text-center text-bold">INVOICE STATUS</th>
                                    <th class=" text-center text-bold">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach ($invoices as $item)
                                    <tr>
                                        <td><a href="{{ route('adviceNotes.show', ['adviceNote' => $item->advice_note_id]) }}"
                                                class=" text-bold"
                                                title="Click here to view {{ $item->advice_note_id }} Advice Note details">{{ $item->advice_note_id }}</a>
                                        </td>
                                        <td>{{ $item->companyName }}</td>
                                        <td class=" text-center"
                                            title="Generation Date: {{ date('M d, Y', strtotime($item->advice_note_created_at)) }}. Expire Date: {{ date('M d, Y', strtotime($item->advice_note_deadline)) }}">
                                            @if ($item->control_number == null)
                                                <i class=" text-brown-300">fetching . . .</i>
                                            @else
                                                <b>{{ $item->control_number }}</b>
                                            @endif
                                        </td>


                                        <td class=" text-right">{{ number_format($item->amount_paid, 2) }}
                                            {{ $item->currency }}</td>
                                        <td
                                            class=" text-right @if ($item->balance > 0) text-warning @else text-success @endif">
                                            {{ number_format($item->balance, 2) }} {{ $item->currency }}</td>
                                        <td>{{ date('d-m-Y', strtotime($item->advice_note_deadline)) }}</td>
                                        <td class=" text-center">
                                            @if ($item->balance > 1)
                                                @if ($item->advice_note_deadline < date('Y-m-d'))
                                                    <label class=" label label-danger p-10">EXPIRED</label>
                                                @else
                                                    <label class=" label label-success p-10">ACTIVE</label>
                                                @endif
                                            @endif
                                        </td>
                                        <td class=" text-center">
                                            <a href="{{ route('adviceNotes.show', ['adviceNote' => $item->advice_note_id]) }}"
                                                class=" btn btn-default text-primary text-bold">View <i
                                                    class=" icon-eye"></i></a>
                                        </td>


                                    </tr>
                                    @php $i++; @endphp
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>
                <div class=" col-md-12 text-right">
                    {{ $invoices->links() }}
                </div>

            </div>
        </div>
    </div>
    <!-- /content area -->
    <!-- /main content -->
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
                                id: '{{ env('APP_URL') }}/accountingManagement/invoice_with_balance/' +
                                    item.companyID,
                                text: item.registrationID+' || '+item.companyName+' || '+ item.companyPhone+' || '+ item.companyEmail,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Companies (write company name or Service Registration Number) . . .',
            minimumInputLength: 0,
        });
    });
</script>