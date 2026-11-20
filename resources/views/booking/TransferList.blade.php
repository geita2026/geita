<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booking Transfer</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-home position-left"></i>Home</a></li>
                <li class="active">View All Booked space</li>
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
            <div>
                <div class="panel-heading">
                    <h5 class="panel-title">Booking Transfer List</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <a href="{{ route('booking_transfer.create') }}" class=" btn btn-primary">New Booking
                                Transfer <i class=" icon icon-plus-circle2"></i></a>
                        </ul>
                    </div>
                </div>
                <div class=" panel-body">
                    <div class=" form-group">
                        <form action="{{ route('booking_transfer.index') }}" method="POST">
                            @csrf
                            @method('GET')
                            <label>Select Event</label>
                            <select name="eventName" class=" form-control" onchange="this.form.submit()">
                                <option value="">Select Event . . . .</option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->event_id }}">
                                        {{ $event->event_name }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class=" form-group">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class=" text-bold">#</th>
                                    <th class=" text-bold">Company Name</th>
                                    <th class=" text-bold">Transfered To</th>
                                    <th class=" text-center text-bold">Transfer Status</th>
                                    <th class=" text-center text-bold"> Transfer reansons</th>
                                    <th class=" text-center text-bold"> Requested By</th>
                                    <th class=" text-bold text-center">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transfers as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->companyName }}</td>
                                        <td>{{ $item->boothNo }}</td>
                                        <td class=" text-center">
                                            <b>{{ $item->transferStatus }}</b>
                                        </td>
                                        <td>
                                            {{ $item->transferReasons }}
                                        </td>
                                        <td>
                                            {{ $item->first_name }} - {{ $item->last_name }}

                                        </td>
                                        <td class=" text-center">

                                            @if ($item->transferStatus == 'UNDER_REVIEW')
                                                @if ($item->transferredBy != Auth::user()->user_uuid)
                                                    <a data-toggle="modal" data-target="#approve{{ $item->bookingID }}"
                                                        class=" btn btn-primary">Approve Transfer</a>
                                                @else
                                                    <span class="label label-warning">Transfer Under Review</span>
                                                @endif
                                            @elseif ($item->transferStatus == 'APPROVED')
                                                <span class="label label-success">Transfer Approved</span>
                                            @endif


                                        </td>
                                    </tr>

                                    {{-- ADVICE NOTE --}}
                                    <div id="approve{{ $item->bookingID }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">APPROVE TRANSFER</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('booking_transfer.update', ['booking_transfer' => $item->bookingID]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="source" value="2">
                                                        <div class=" form-group col-md-12">
                                                            <label>Transfer reasons <i
                                                                    class=" text-danger">*</i></label>
                                                            <p class=" text-bold">
                                                                {{ $item->transferReasons }}
                                                            </p>
                                                        </div>
                                                        <div class=" form-group col-md-12">
                                                            <label> Select your Decision <i
                                                                    class=" text-danger">*</i></label>
                                                            <select name="transferStatus" class=" form-control">
                                                                <option value="">Select Decision . . . .</option>
                                                                <option value="APPROVED">APPROVED</option>
                                                            </select>
                                                        </div>


                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-secondary">Close</button>
                                                            <button type="submit" class="btn btn-success ml-20">Approve
                                                                Transfer</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- ADVICE NOTE --}}


                                    {{-- ADVICE NOTE --}}
                                    <div id="transfer{{ $item->bookingID }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">TRANSFER BOOKING INFORMATION</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('booking_transfer.update', ['booking_transfer' => $item->bookingID]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class=" form-group col-md-12">
                                                            <label>To New Booth <i class=" text-danger">*</i></label>
                                                            <select name="boothId" class=" form-control">
                                                                <option value="">Select Booth . . . .</option>
                                                                {{-- @foreach ($booths as $booth)
                                                                <option value="{{ $booth->boothID }}">
                                                                    {{ $booth->boothNo }}</option>
                                                            @endforeach --}}
                                                            </select>
                                                        </div>
                                                        <div class=" form-group col-md-12">
                                                            <label>Transfer reasons <i
                                                                    class=" text-danger">*</i></label>
                                                            <textarea name="transferReasons" class=" form-control" required>{{ $item->transferReasons }}</textarea>
                                                        </div>

                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-secondary">Close</button>
                                                            <button type="submit" class="btn btn-success ml-20"
                                                                @if ($item->transferStatus > 1) disabled @endif>Transfer
                                                                Exhibitor</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- ADVICE NOTE --}}
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_multiselect.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
