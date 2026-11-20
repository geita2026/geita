<x-app-layout>
    <title>{{ env('APP_NAME') }} | Invoice Reports</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Home</a></li>
                <li class="active">View Invoice Report</li>
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
                        <b>Sponsorship Report</b> typically provides detailed information on the funds received through
                        sponsorships. This report tracks each sponsor's
                        contributions, which may include financial support, in-kind donations, or other resources
                    </p>
                </div>
                <div class=" row col-md-12 p-20 form-group" style="border:1px solid rgb(219, 219, 223);">
                    {{-- serach data --}}
                    <form action="{{ route('sponsorship_report') }}" method="POST">
                        @csrf
                        @method('GET')
                        <div class=" row col-md-12">
                            <div class=" col-md-8 form-group">
                                <label>Event Name<i class=" text-danger">*</i></label>
                                <select name="event" class=" form-control" required>
                                    <option value=""> - - - Select Event - - - </option>
                                    @foreach ($events as $item)
                                        <option value="{{ $item->event_id }}" class=" text-uppercase">
                                            {{ $item->event_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class=" col-md-2 form-group text-right">
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
                        @if (session('sponsorshipReport'))
                            <form action="{{ route('exportSponsoshipReport') }}" method="POST">
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
                                    <th>#</th>
                                    <th class="text-bold text-center">CompanyName#</th>
                                    <th class=" text-bold text-center">Booked Package</th>
                                    <th class=" text-bold text-center">Control Number</th>
                                    <th class=" text-bold text-center">Invoice Amount</th>
                                    <th class=" text-bold text-center">Amount Paid</th>
                                    <th class=" text-bold text-center">Ivoice Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->companyName }} ({{ $item->companyPhone }})</td>
                                        <td> {{ $item->package_name }} </td>
                                        <td>{{ $item->control_number }}</td>
                                        <td>{{ number_format($item->total_amount, 2) }}</td>
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
