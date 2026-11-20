<x-app-layout>
    <title>{{ env('APP_NAME') }} | Ticket Bookings</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left text-uppercase"></i>TICKET BOOKINGS</a></li>
                <li class="active">View All ticket Requests</li>
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
            <div class=" panel-body table-responsive">

                <div class=" form-group col-md-12 table-header">
                    <h4 class="text-center text-bold">ONLINE SABASABA TICKET BOOKINGS.</h4>
                </div>

                <h2 class=" text-center">ONLINE TICKET BOOKING SUMMARY</h2>
                <div class=" col-md-12 form-group">
                    <table class="datatable-button-html5-columns table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class=" text-bold text-center">#</th>
                                <th class="text-bold text-center">CUSTOMER NAME</th>
                                <th class=" text-bold text-center">CUSTOMER PHONE</th>
                                <th class=" text-bold text-center">TICKET REQUESTED</th>
                                <th class=" text-bold text-center">TICKET QUANTITY</th>
                                <th class=" text-bold text-center">QUANTITY /UNIT</th>
                                <th class="text-bold text-center text-uppercase">TOTAL COST</th>
                                <th class="text-bold text-center text-uppercase">TICKET REQUEST DATE</th>
                                <th class="text-bold text-center text-uppercase">SERVICE CODE</th>
                                <th class="text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach ($requests as $item)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $item->requestedBy }}</td>
                                    <td>{{ $item->phoneNumber }}</td>
                                    <td>{{ $item->package_discriptions }}({{ $item->package_name }})</td>
                                    <td class=" text-center">{{ $item->quantity }}</td>
                                        <td class=" text-right">{{ number_format($item->package_cost, 2) }}
                                            {{ $item->currency }}</td>
                                    <td class=" text-right">{{ number_format($item->package_cost*$item->quantity, 2) }} {{ $item->currency }}</td>
                                    <td>{{ date('l M d, Y h:i:s',strtotime($item->requestedAt)) }}</td>
                                    <td class=" text-center text-bold">{{ $item->serviceCode ?? 'TANTRADE' }}</td>
                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">

                                                    @if ($item->ticket_amount <= $item->amount_paid)
                                                        @can('Confirm Booking')
                                                            <li>
                                                                <a data-toggle="modal"
                                                                data-target="#confirmTicket{{ $item->requestId }}"
                                                                    class=" text-primary"><i class=" icon-eye"></i> View
                                                                    Tickets</a>
                                                            </li>
                                                        @endcan
                                                    @endif

                                                    {{-- @can('Resend Control Number')
                                                        <li>
                                                            <a data-toggle="modal"
                                                                data-target="#ResendControlNo{{ $item->requestId }}"
                                                                class=" text-primary"><i class=" icon-reply"></i> Resend
                                                                ControlNo</a>
                                                        </li>
                                                    @endcan --}}

                                                    {{-- @can('Resend Ticket')
                                                        <li>
                                                            <a data-toggle="modal"
                                                                data-target="#ResendTicket{{ $item->requestId }}"
                                                                class=" text-primary"><i class=" icon-reply-all"></i> Resend
                                                                Ticket</a>
                                                        </li>
                                                    @endcan --}}

                                                    @can('Cancel Ticket')
                                                        <li>
                                                            <a data-toggle="modal"
                                                                data-target="#delete{{ $item->requestId }}"
                                                                class="text-danger"> <i class=" icon-cross"></i> Cancel
                                                                Invoice</a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>

                                    {{-- *********** delete Ticket ************* --}}
                                    <div id="delete{{ $item->requestId }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you whant to delete
                                                        <b>{{ $item->package_name }}</b> ?</h5>
                                                </div>

                                                <div class="modal-body">
                                                    
                                                        
                                                    {{-- <div class=" form-group col-md-12">
                                                        <textarea name="reasons" class=" form-control" placeholder="Reasons for cancelling invoice" required>{{ old('reasons') }}</textarea>
                                                    </div> --}}
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-default">Close</button>
                                                                <a href="{{ route('remove_ticket',['id'=>$item->requestNo]) }}"  class=" btn btn-danger">Delete <i class=" icon-trash"></i></a>
                                                        </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- delete modal --}}

                                    {{-- ******RESEND TICKET *********** --}}
                                    <div id="confirmTicket{{ $item->requestId }}" class="modal fade"
                                    aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div
                                                class="modal-header bg-primary">
                                                <button type="button"
                                                    class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Je!
                                                    una uhakika unataka
                                                    kuthibitisha taarifa hizi ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('online_tickets.store') }}"
                                                    method="POST">
                                                    @csrf

                                                    <div class=" form-group">

                                                        <p
                                                            class=" text-danger text-justify text-bold">
                                                             Tutakutumia Control Numba na ticket baada ya malipo hivyo hakikisha namba ya simu ni Sahihi.
                                                        </p>
                                                        <h6>Full Name <i
                                                                class=" text-danger">*</i>
                                                            <input
                                                                type="text"
                                                                name="fullName"
                                                                class=" form-control"
                                                                value="{{ $item->requestedBy }}"
                                                                required>
                                                        </h6>
                                                        <h6>PhoneNumber (<span
                                                                class=" text-muted">Must
                                                                start with
                                                                255</span>) <i
                                                                class=" text-danger">*</i>
                                                            <input
                                                                type="text"
                                                                name="phoneNumber"
                                                                class=" form-control"
                                                                value="{{ $item->phoneNumber }}"
                                                                required>
                                                        </h6>
                                                        @if ($item->plateNumber != null)
                                                            <h6>Car Plate Number
                                                                <i
                                                                    class=" text-danger">*</i>
                                                                <input
                                                                    type="text"
                                                                    name="plateNumber"
                                                                    class=" form-control"
                                                                    value="{{ $item->plateNumber }}">
                                                            </h6>
                                                        @endif
                                                        <h6>Ticket Total Cost
                                                            (TZS) <i
                                                                class=" text-danger">*</i>
                                                            <input
                                                                type="text"
                                                                name="ticketCost"
                                                                value="{{ number_format($item->quantity*$item->package_cost) }}"
                                                                class=" form-control"
                                                                readonly>
                                                        </h6>
                                                    </div>
                                                    <input type="hidden"
                                                        name="requestId"
                                                        value="{{ $item->requestNo }}">
                                                    <div class=" text-right">
                                                        <img src="{{ asset('images/loading2.gif') }}"
                                                            id="loader"
                                                            style="display: none;"
                                                            height="100"
                                                            width="100">
                                                        <button type="submit"
                                                            id="submitButton"
                                                            class="btn btn-primary ml-20"
                                                            onclick="showLoader()">thibitisha</button>
                                                    </div>
                                                </form>
                                            </div>

                                           
                                        </div>
                                    </div>
                                </div>
                                    {{-- ******** RESEND TICKET********** --}}


                                    {{-- ******RESEND TICKET *********** --}}
                                    <div id="ResendTicket{{ $item->requestId }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you want to resend ticket ?
                                                        <b> {{ $item->ticket_issued }}</b> Ticket</h5>
                                                </div>

                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('ticket_bookings.show', ['ticket_booking' => $item->requestId]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('GET')
                                                        <input type="hidden" name="source" value="2">
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-default">Close</button>
                                                            <button type="submit"
                                                                class="btn btn-primary btn-ladda btn-ladda-spinner ml-20"
                                                                data-style="slide-down">Yes, Resend</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- ******** RESEND TICKET********** --}}


                                </tr>
                                @php $i++; @endphp
                            @endforeach
                        </tbody>

                    </table>
                </div>
                <div class=" col-md-12 form-group text-right">
                    {{ $requests->links() }}
                </div>

            </div>
            {{--  end here  --}}
        </div>
    </div>
    <!-- /dashboard content -->
</x-app-layout>
