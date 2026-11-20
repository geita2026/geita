<x-app-layout>
    <title>{{ env('APP_NAME') }} | Permit Reports</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Home</a></li>
                <li class="active">View Permit Reports</li>
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
                        <b>e-Permit Report</b>   Typically refers to a digital or electronic report generated within an e-permit system
                    </p>
                </div>
                <div class=" row col-md-12 p-20 form-group" style="border:1px solid rgb(219, 219, 223);">
                    {{-- serach data --}}
                    <form action="{{ route('permit_reports') }}" method="POST">
                        @csrf
                        @method('GET')
                        <div class=" row col-md-12">
                            <div class=" col-md-8 form-group">
                                <label>Type of Permit Application <i class=" text-danger">*</i></label>
                                <select name="applicationStatus" class=" form-control" required>
                                    <option value="">- - - Select Type of Permit Applications - - -</option>
                                    <option value="5">APROVED APPLICATIONS</option>
                                    <option value="6">ISSUED PERMITS</option> 
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
                        @if (session('permitData'))
                            <form action="{{ route('export_permit_reports') }}" method="POST">
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
                                    <th class="text-bold text-center">CompanyName</th>
                                    <th class=" text-bold text-center">Event Title</th>
                                    <th class=" text-bold text-center">Event Venue</th>
                                    <th class=" text-bold text-center">Invoice ControlNumber</th>
                                    <th class=" text-bold text-center">Invoice Amount</th>
                                    <th class=" text-bold text-center">Amount Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key=>$item)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td> {{ $item->companyName }} ({{ $item->companyPhone }})</td>
                                        <td>{{ $item->eventTitle }} ({{ $item->promotionEventTypeName }})</td>
                                        <td>{{ $item->venueAddress }}</td>
                                        <td>{{ $item->controlNumber }}</td>
                                        <td>{{ number_format($item->invoiceAmount, 2) }} {{ $item->currency }}</td>
                                        <td>{{ number_format($item->amountPaid, 2) }} {{ $item->currency }}</td>
                                        
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
