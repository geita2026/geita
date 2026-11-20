<x-app-layout>
    <title>{{ env('APP_NAME') }} | Ticket Bookings</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left text-uppercase"></i>Home</a></li>
                <li class="active">View All ticket Bookings</li>
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

                <div class=" form-group col-md-12 ">
                    <h4 class=" card-header">
                        <form action="{{ route('ticket_bookings.index') }}" method="POST">
                            @csrf
                            @method('GET')
                            <div class=" row">
                                <div class="col-md-3">
                                    <input type="hidden" name="source" value="1">
                                    <input type="date" name="collectionDate" class=" form-control"
                                        value="{{ date('Y-m-d') }}">
                                    <label>Collection Date</label>
                                </div>
                                <div class="col-md-3">
                                    <input name="phone_number" class="form-control">
                                    <label>Customer Number</label>
                                </div>
                                <div class="col-md-3">
                                    <select name="serviceCode" class="form-control">
                                        <option value="">All Service Codes</option>
                                        @foreach ($serviceCodes as $code)
                                            <option value="{{ $code }}"
                                                {{ session('serviceCode') == $code ? 'selected' : '' }}>{{ $code }}</option>
                                        @endforeach
                                    </select>
                                    <label>Service Code</label>
                                </div>
                                <div class=" col-md-3 text-right">
                                    <button type="submit" class=" btn btn-primary btn-block">Load Data</button>
                                </div>
                            </div>

                        </form>
                    </h4>
                </div>

                <h2 class=" text-center">ONLINE TICKET BOOKING SUMMARY AS AT {{ session('date') }}</h2>
                <div class="category-content">
                    <div class="row row-condensed">

                        <a>
                            <div class="col-md-4 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($totalExpected) }} TZS</i>
                                    <span>Total Expected Collections</span>
                                </button>
                            </div>
                        </a>

                        <a>
                            <div class="col-md-4 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($totalCollection) }} TZS</i>
                                    <span>Total Collected</span>
                                </button>
                            </div>
                        </a>

                        <a>
                            <div class="col-md-4 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($totalTicket) }}</i>
                                    <span>Total Ticket Issued</span>
                                </button>
                            </div>
                        </a>

                    </div>
                </div>
                <div class=" col-md-12 form-group">
                    <table class="datatable-button-html5-columns table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class=" text-bold text-center">#</th>
                                <th class="text-bold text-center">CUSTOMER NAME</th>
                                <th class=" text-bold text-center">CUSTOMER PHONE</th>
                                <th class=" text-bold text-center">CONTROL NUMBER</th>
                                <th class=" text-bold text-center">AMOUNT INVOICED</th>
                                <th class=" text-bold text-center">AMOUNT PAID</th>
                                <th class=" text-bold text-center">TICKET DESCRIPTIONS</th>
                                <th class="text-bold text-center text-uppercase">TICKET REQUEST DATE</th>
                                <th class="text-bold text-center text-uppercase">SERVICE CODE</th>
                                <th class="text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach ($ticket_order as $item)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $item->customer_name }}</td>
                                    <td>{{ $item->phone_number }}</td>
                                    <td class=" text-center text-bold">
                                        @if ($item->control_number != null)
                                            <i
                                                class=" text-primary">{{ $item->control_number ?? 'Failed to request' }}</i>
                                        @else
                                            <i class=" text-primary">{{ $item->serviceCode }}</i>
                                        @endif


                                    </td>
                                    <td class=" text-right">{{ number_format($item->ticket_amount, 2) }}
                                        {{ $item->currency }}</td>
                                    <td class=" text-right">{{ number_format($item->amount_paid, 2) }}
                                        {{ $item->currency }}</td>
                                    <td>{{ $item->ticketDescription }}</td>
                                    <td>{{ date('d-m, Y h:i:s', strtotime($item->ticket_booked_on)) }}</td>
                                    <td class=" text-center text-bold">{{ $item->serviceCode }}</td>
                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">

                                                    @can('view-tickets')
                                                        @if ($item->ticket_amount >= $item->amount_paid)
                                                            <li>
                                                                <a href="{{ route('myTicket.show', ['myTicket' => $item->ticket_booking_id]) }}"
                                                                    class=" text-primary"><i class=" icon-download"></i> Download
                                                                    Tickets</a>
                                                            </li>
                                                        @endif
                                                    @endcan


                                                    @can('resend-control-number')
                                                        {{-- Resend Control Number --}}
                                                        @if ($item->control_number != null && $item->ticket_amount > $item->amount_paid)
                                                            <li>
                                                                <a data-toggle="modal"
                                                                    data-target="#ResendControlNo{{ $item->ticket_booking_id }}"
                                                                    class=" text-primary"><i class=" icon-reply"></i> Resend
                                                                    ControlNo</a>
                                                            </li>
                                                        @endif
                                                    @endcan

                                                    @can('resend-ticket')
                                                        @if ($item->ticket_amount <= $item->amount_paid)
                                                            <li>
                                                                <a data-toggle="modal"
                                                                    data-target="#ResendTicket{{ $item->ticket_booking_id }}"
                                                                    class=" text-primary"><i class=" icon-reply-all"></i>
                                                                    Resend
                                                                    Ticket</a>
                                                            </li>
                                                        @endif
                                                    @endcan

                                                    @can('cancel-ticket')
                                                        @if ($item->ticket_amount > $item->amount_paid)
                                                            <li>
                                                                <a data-toggle="modal"
                                                                    data-target="#delete{{ $item->ticket_booking_id }}"
                                                                    class="text-danger"> <i class=" icon-cross"></i> Cancel
                                                                    Invoice</a>
                                                            </li>
                                                        @endif
                                                    @endcan
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>

                                    {{-- *********** delete Ticket ************* --}}
                                    <div id="delete{{ $item->ticket_booking_id }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you whant to delete
                                                        <b>{{ $item->package_name }}</b> ?
                                                    </h5>
                                                </div>

                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('ticket_bookings.destroy', ['ticket_booking' => $item->ticket_booking_id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        {{-- <div class=" form-group col-md-12">
                                                        <textarea name="reasons" class=" form-control" placeholder="Reasons for cancelling invoice" required>{{ old('reasons') }}</textarea>
                                                    </div> --}}
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-default">Close</button>
                                                            <button type="submit"
                                                                class="btn btn-danger btn-ladda btn-ladda-spinner ml-20"
                                                                data-style="slide-down">Yes, Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- delete modal --}}

                                    {{-- ******RESEND Control No TICKET *********** --}}
                                    <div id="ResendControlNo{{ $item->ticket_booking_id }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you want to resend Control
                                                        Number for ? <b> {{ $item->customer_name }}</b></h5>
                                                </div>

                                                <div class="modal-body">
                                                    <form action="{{ route('ticket_bookings.store') }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="bookingId"
                                                            value="{{ $item->ticket_booking_id }}">
                                                        <div class=" form-group">
                                                            <input type="text" name="customerNo"
                                                                class="form-control"
                                                                value="{{ $item->phone_number }}">
                                                        </div>
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


                                    {{-- ******RESEND TICKET *********** --}}
                                    <div id="ResendTicket{{ $item->ticket_booking_id }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you want to resend ticket ?
                                                        <b> {{ $item->ticket_issued }}</b> Ticket
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('ticket_bookings.show', ['ticket_booking' => $item->ticket_booking_id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('GET')
                                                        <div class=" form-group">
                                                            <input type="text" name="customerNo"
                                                                class="form-control"
                                                                value="{{ $item->phone_number }}">
                                                        </div>
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
                    {{ $ticket_order->links() }}
                </div>

            </div>
            {{--  end here  --}}
        </div>
    </div>
    <!-- /dashboard content -->
</x-app-layout>
