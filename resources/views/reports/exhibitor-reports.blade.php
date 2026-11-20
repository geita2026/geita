<x-app-layout>
    <title>{{ env('APP_NAME') }} | Space-Reports</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Home</a></li>
                <li class="active">View Exhibitors Report</li>
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
                        <b>An exhibitor report</b>  Is a summary list of participants with their sectors
                        who participated in Trade Promotion Events.
                    </p>
                </div>
                <div class=" row col-md-12 p-20 form-group" style="border:1px solid rgb(219, 219, 223);">
                    {{-- serach data --}}
                    <form action="{{ route('exhibitors.index') }}" method="POST">
                        @csrf
                        @method('GET')
                        <div class=" row col-md-12">

                            <div class=" col-md-6 form-group">
                                <label>Sector Name<i class=" text-danger">*</i></label>
                                <select name="sectorName" class=" form-control" required>
                                    <option value=""> - - - Select Sector - - - </option>
                                    <option value="1"> ALL SECTORS </option>
                                    @foreach ($sectors as $item)
                                        <option value="{{ $item->sectorID }}" class=" text-uppercase">
                                            {{ $item->sectorName }}</option>
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
                        @if (session('exhibitorData'))
                            <form action="{{ route('exhibitors.store') }}" method="POST">
                                @csrf
                                <div class=" text-right">
                                    <button type="submit" class=" btn btn-default text-primary">Export Excel <i
                                            class="icon-download7"></i></button>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="col-md-12">
                        <div class=" form-group">
                            <table class="  table table-bordered  table-responsive table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-bold text-center">Company Name</th>
                                        <th class=" text-bold text-center">Company Email</th>
                                        <th class=" text-bold text-center">Company Phone</th>
                                        <th class=" text-bold text-center">Company Sector</th>
                                        <th class=" text-bold text-center">Participation Form</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data1 as $key=>$item)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td> {{ $item->companyName }} </td>
                                            <td>{{ $item->companyEmail }}</td>
                                            <td>{{ $item->companyPhone }}</td>
                                            <td>{{ $item->sectorName }}</td>
                                            
                                            <td class=" text-center">
                                                <a href="{{ route('participation_form',['participationId'=>$item->participation_id]) }}" class=" btn btn-primary">Download <i class=" icon-file-download"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
    
                                </tbody>
                            </table>
                        </div>
                        <div class=" form-group text-right">
                            {{ $data1->links() }}
                        </div>

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
