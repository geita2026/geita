<x-app-layout>
    <title>{{ env('APP_NAME') }} | Invoice</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Home</a>
                </li>
                <li class="active">View All Off fair Invoices</li>
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
            <div class=" panel-body table-responsive">
                {{-- validation messages --}}
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <span class="alert text-danger">{{ $error }} ,</span>
                    @endforeach
                @endif
                {{-- //validation messages --}}
                <div class=" form-group row col-md-12 text-center text-bold bg-primary">
                    <h3>OFF FAIR INVOICE LIST</h3>
                </div>

                <div>
                    {{-- table for available customers in database --}}

                    <div class=" form-group col-md-12">
                        <select id="search-select" name="search" class=" form-control"
                        onchange="location = this.value;">
                       </select>
                    </div>

                    <div class=" col-md-12 form-group">
                        <table class=" table table-bordered table-striped">
                            <thead>
                                <tr>
                                    {{-- <th class=" text-bold text-center">#</th> --}}
                                    <th class=" text-bold text-center">ADVICE NOTE ID</th>
                                    <th class=" text-bold text-center">CUSTOMER NAME</th>
                                    <th class=" text-bold text-center">CONTROL NUMBER</th>
                                    <th class=" text-bold text-center">TOTAL AMOUNT</th>
                                    <th class=" text-bold text-center">BALANCE</th>
                                    <th class=" text-bold">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach ($advice_notes as $item)
                                    <tr>
                                        <td class=" text-uppercase">
                                            <a href="{{ route('Off-Fair-Advice-Notes.show', ['Off_Fair_Advice_Note' => $item->off_fair_advice_note_id]) }}"
                                                title="Click here to view advice Note"
                                                class=" text-bold">{{ $item->off_fair_advice_note_id }}</a>
                                        </td>

                                        <td class=" text-uppercase">{{ $item->customer_name ?? $item->companyName }}
                                        </td>

                                        {{-- set deadline --}}
                                        <div id="setDeadline{{ $item->off_fair_advice_note_id }}" class="modal fade"
                                            aria-modal="true" data-backdrop="static" data-keyboard="false">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h4 class="modal-title text-bold text-center">SET OFF FAIR
                                                            ADVICE NOTE DEADLINE</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form
                                                            action="{{ route('Off-Fair-Advice-Notes.update', ['Off_Fair_Advice_Note' => $item->off_fair_advice_note_id]) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class=" form-group">
                                                                <label>Set Deadline</label>
                                                                <input type="hidden" name="advice_note"
                                                                    value="{{ $item->off_fair_advice_note_id }}"
                                                                    value="{{ $item->off_fair_advice_note_deadline }}"
                                                                    required>
                                                            </div>
                                                            <input type="date" name="deadline" class=" form-control"
                                                                min="{{ date('Y-m-d') }}" required>
                                                    </div>
                                                    <div class=" modal-footer">
                                                        <div class="  form-group text-right">
                                                            <button type="submit"
                                                                class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                                                    class="ladda-label">Set Deadline</span></button>
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <td class=" text-center">
                                            @if ($item->control_number == null)
                                                <i class=" text-brown">Searching . . .</i>
                                            @else
                                                <a>{{ $item->control_number }}</a>
                                            @endif
                                        </td>
                                        {{-- end set deadline --}}


                                        <td class=" text-right"><b>{{ number_format($item->total_amount, 2) }}
                                                {{ $item->currency }}</b></td>
                                        <td class=" text-right">
                                            @if ($item->total_amount > 0)
                                                <b class=" text-warning">{{ number_format($item->balance, 2) }}
                                                    {{ $item->currency }}</b>
                                            @elseif($item->total_amount <= 0)
                                                <b class=" text-success">{{ number_format($item->balance, 2) }}
                                                    {{ $item->currency }}</b>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('Off-Fair-Advice-Notes.show',['Off_Fair_Advice_Note'=>$item->off_fair_advice_note_id]) }}"
                                                class=" text-primary text-bold btn btn-block border-primary">View
                                                Invoice <i class=" icon-file-empty2"></i></a>
                                        </td>
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class=" form-group col-md-12 text-right">
                        {{ $advice_notes->links() }}
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
                url: '{{ route('search_off_fair_invoice') }}', // URL for the AJAX request
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
                                id: '{{ env('APP_URL') }}/accountingManagement/Off-Fair-Advice-Notes/' +
                                    item.off_fair_advice_note_id,
                                text: item.companyName+' || '+ item.off_fair_advice_note_id + ' || '+ item.control_number+' || '+item.total_amount+' '+item.currency
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Invoice . . .',
            minimumInputLength: 0,
        });
    });
</script>