<x-app-layout>
    <title>{{ env('APP_NAME') }} | Invoice</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-list"></i> INVOICE LIST</a></li>
                <li class="active">View All invoice</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">

        <div class="panel panel-flat">
            <div class=" panel-body">

                {{--  search company from the list  --}}

                <div class="row form-group">
                    <div class=" col-md-4">
                        <div class=" form-group">
                            <a href="{{ route('controlNoRequests.index') }}" class=" btn btn-default border-primary"><i
                                    class=" icon-arrow-left12"></i> Back</a>
                        </div>
                    </div>

                </div>
                {{--  end searching  --}}
                {{-- // end of messages --}}
               

                <div class=" form-group">
                    <table class=" table table-bordered table-striped">
                        <thead>

                            <tr>
                                <th colspan="8" class=" text-bold text-center"
                                    style="background-color: rgb(235, 225, 225);">LIST OF GENERATED INVOICE</th>
                            </tr>
                            <tr>
                                <th colspan="8">
                                    <div class=" form-group">
                                        <form data-action="{{ route('search_invoices') }}" id="searchCompany" method="POST">
                                            @csrf
                                            <div class="form-group has-feedback">
                                                <input type="text" id="search" name="search" class="form-control input-sm"
                                                    placeholder="Search any information from invoice ">
                                                <div class="form-control-feedback">
                                                    <i class=" icon-search4"></i>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th class=" text-bold text-center">ADVICE NOTE ID</th>
                                <th class=" text-bold text-center">COMPANY NAME</th>
                                <th class=" text-bold text-center">CONTROL NUMBER</th>
                                <th class="text-bold text-center">AMOUNT INVOICED</th>
                                <th class="text-bold text-center">AMOUNT PAID</th>
                                <th class=" text-bold text-center">AMOUNT PAID</th>
                                <th class=" text-bold text-center">PAYMENT MODE</th>
                                <th class=" text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $i = 1;
                            @endphp
                            <script type="text/javascript">
                                @foreach ($adviceNotes as $item)
                            </script>
                            <tr>
                                <td>
                                    <a href="{{ route('controlNoRequests.show', ['controlNoRequest' => $item->advice_note_id]) }}"
                                        class=" text-bold">{{ $item->advice_note_id }}</a>
                                </td>
                                <td>{{ $item->companyName }}</td>

                                <td class=" text-center">
                                    <a
                                        href="{{ route('controlNoRequests.show', ['controlNoRequest' => $item->advice_note_id]) }}">{{ $item->control_number }}</a>
                                </td>
                                <td class=" text-right">{{ number_format($item->total_amount, 2) }} {{ $item->currency }}
                                </td>
                                <td class=" text-right">{{ number_format($item->amount_paid, 2) }} {{ $item->currency }}
                                </td>
                                <td class="text-center">
                                    @if ($item->advice_note_status == 3)
                                        <a class=" text-bold text-success">APPROVED</a>
                                    @elseif ($item->advice_note_status == 2)
                                        <a class=" text-warning text-bold">CONFIRMED</a>
                                    @endif
                                </td>

                                <td class=" text-center">
                                    @if ($item->payment_mode==1)
                                        <label class=" label label-warning" title="Payments must be equal or greater than invoiced Amount">FULL PAYMENTS</label>
                                    @elseif ($item->payment_mode==2)
                                    <label class=" label label-primary" title="Payments may be Paid in installement">PARTIAL PAYMENTS</label>
                                    @elseif ($item->payment_mode==3)
                                    <label class=" label label-success" title="Payments must be equal to invoiced Amount">EXACT PAYMENT</label>
                                    @endif
                                    
                                   </td>

                                {{--  action selector  --}}
                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a href="{{ route('controlNoRequests.show', ['controlNoRequest' => $item->advice_note_id]) }}"
                                                        class=" text-teal-800 "><b><i class=" icon-eye"></i> View Advice
                                                            Note</b></a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                                {{--  //end action selector  --}}
                                {{-- delete modal --}}
                                <div id="sendBack{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to Send back this Advice
                                                    Note ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('controlNoRequests.update', ['controlNoRequest' => $item->advice_note_id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class=" text-right">
                                                        <button type="button" data-dismiss="modal"
                                                            class=" btn btn-secondary">Close</button>
                                                        <button type="submit" class="btn btn-primary ml-20"> Send
                                                            Back</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </tr>

                            <script>
                                @php
                                    $i++;
                                @endphp
                                @endforeach
                            </script>
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
<script>
    $(document).ready(function() {
        var form = '#searchCompany';
        $('#searchCompany').on('keyup', function() {
            event.preventDefault();
            var url = $(this).attr('data-action');
            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    var htmlView = '';
                    if (response.status == 1) {
                        if (response.data.length <= 0) {
                            htmlView = +`
                    <tr>
                        <td colspan="4">No data.</td>
                    </tr>`;
                        } else {
                            for (let i = 0; i < response.data.length; i++) {
                                htmlView += `
                        <tr>
                            <td>
                                <a href="{{ env('APP_URL') }}/adviceNotes/` + response.data[i].advice_note_id + `" class=" text-primary text-bold">` + response.data[i].advice_note_id +`</a>
                            </td>
                            <td class=" text-uppercase">
                                <a href="{{ env('APP_URL') }}/adviceNotes/` + response.data[i].advice_note_id + `" title="Click to update company">
                                    ` + response.data[i].companyName + `
                                </a>
                            </td>
                            <td>` + response.data[i].control_number + `</td>
                            <td> ` + new Intl.NumberFormat().format(response.data[i].total_amount) + " " + response.data[i].currency + ` </td>
                            <td class=" text-success text-bold">` + new Intl.NumberFormat().format(response.data[i].amount_paid)   + " " + response.data[i].currency + `</td>
                            <td class="text-warning text-bold">` + new Intl.NumberFormat().format((response.data[i].total_amount-response.data[i].amount_paid))  + " " + response.data[i].currency + `</td>
                            <td class="text-center">
                              <a href="{{ env('APP_URL') }}/controlNoRequests/` + response.data[i].advice_note_id + `" class=" text-primary text-bold btn btn-block border-primary">View Invoice <i class=" icon-file-empty2"></i></a>
                            </td></tr>`;
                            }
                            $('tbody').html(htmlView);
                        }
                    }
                }
            });
        });

    })
</script>
