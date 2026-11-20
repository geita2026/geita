<x-app-layout>
    <title>{{ env('APP_NAME') }} | Space-Reports</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Home</a></li>
                <li class="active">View booked and unbooked space</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    <div class=" content">
        <div class="panel panel-white">
            <div class=" panel-body">
                <div class=" col-md-12">
                    <p class=" alert alert-info">
                        <i class=" icon-info22"></i>
                        <b>Pavilion / Space Booking Report</b> Is a detailed summary of reservations made for a pavilion or similar outdoor space (e.g., a park
                        pavilion, event gazebo, or outdoor venue). This type of report is useful for tracking, managing,
                        and analyzing the usage of pavilions by individuals, organizations, or groups for Exhibition
                        events.
                    </p>
                </div>
                <div class=" row col-md-12 p-20 form-group" style="border:1px solid rgb(219, 219, 223);">
                    {{-- serach data --}}
                    <form action="{{ route('space-booking-report.index') }}" method="POST">
                        @csrf
                        @method('GET')
                        <div class=" row col-md-12">

                            <div class=" col-md-4 form-group">
                                <label>Type of Pavilion / Pavilion Name <i class=" text-danger">*</i></label>
                                <select name="typeOfPavilions" class=" form-control" required>
                                    <option value="">- - - Select Type of Space - - -</option>
                                    @foreach ($pavilionCategories as $category)
                                    <option class=" text-uppercase" value="{{ $category->category_id }}">
                                     {{ $category->category_name }}</option>
                                    @endforeach
                                    <hr>
                                    @foreach ($spaces as $value)
                                        <option class=" text-uppercase" value="{{ $value->pavilionID }}">
                                            {{ $value->pavilionName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class=" col-md-4 form-group">
                                <label>Event Name<i class=" text-danger">*</i></label>
                                <select name="event" class=" form-control" required>
                                    <option value=""> - - - Select Event - - - </option>
                                    @foreach ($events as $item)
                                        <option value="{{ $item->event_id }}" class=" text-uppercase">
                                            {{ $item->event_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class=" col-md-4 form-group text-right">
                                <button class=" btn btn-info mt-20 btn-ladda btn-ladda-spinner">Load Data <i
                                        class=" icon-reload-alt"></i></button>
                            </div>
                        </div>
                    </form>
                    {{-- end --}}
                </div>
                <!-- Main charts -->
                <div class="row">
                    <div class=" col-md-12 text-right">
                        @if (session('data'))
                            <form action="{{ route('space-booking-report.store') }}" method="POST">
                                @csrf
                                <div class=" text-right">
                                    <button type="submit" class=" btn btn-default text-primary">Export Excel <i
                                            class="icon-download7"></i></button>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="col-md-12">
                        <table class="  table table-bordered  table-responsive table-striped">
                            <thead>
                                <tr>
                                    <th class="text-bold text-center">Booked Space</th>
                                    {{-- <th class=" text-bold text-center">Space Category</th> --}}
                                    <th class=" text-bold text-center">Space Size</th>
                                    <th class=" text-bold text-center">Booked By</th>
                                    <th class=" text-bold text-center">Company Email</th>
                                    <th class=" text-bold text-center">Company Phone</th>
                                    <th class=" text-bold text-center">Control Number</th>
                                    <th class=" text-bold text-center">Invoice Amount</th>
                                    <th class=" text-bold text-center">Amount Paid</th>
                                    <th class=" text-bold text-center">Ivoice Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td> {{ $item->spaceName }} - {{ $item->partitionName }}</td>
                                        {{-- <td>{{ $item->category_name }}</td> --}}
                                        <td>{{ number_format($item->spaceSize, 2) }}</td>
                                        <td>{{ $item->registrationID }} - {{ $item->companyName }}</td>
                                        <td>{{$item->companyEmail}}</td>
                                        <td>{{$item->companyPhone}}</td>
                                        <td>{{ $item->control_number }}</td>
                                        <td>{{ number_format($item->total_amount, 2) }} {{ $item->currency }}</td>
                                        <td>{{ number_format($item->amount_paid, 2) }} {{ $item->currency }}</td>
                                        <td class=" text-center">
                                            @if ($item->amount_paid >= $item->total_amount)
                                                <label class=" label label-success">PAID</label>
                                            @elseif (date('Ymd') > date('Ymd', strtotime($item->advice_note_deadline)))
                                                <label class=" label label-danger">EXPIRED</label>
                                            @elseif (date('Ymd') <= date('Ymd', strtotime($item->advice_note_deadline)))
                                                <label class=" label label-default">UNPAID</label>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        <div class=" text-right text-muted">Requested on: {{ date('M d, Y h:i:s') }}</div>

                    </div>


                </div>
                <!-- /main charts -->



            </div>


        </div>
        <!-- /dashboard content -->

    </div>

    </div>
    </div>
</x-app-layout>

<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/c3/c3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/moment/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/daterangepicker.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/picker_date.js') }}"></script>
