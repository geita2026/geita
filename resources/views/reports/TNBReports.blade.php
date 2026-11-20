<x-app-layout>
    <title>{{ env('APP_NAME') }} | TNB Reports</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Home</a></li>
                <li class="active">TNB Report</li>
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
                        <b>TNB Competition Reports</b> Display list of all Application submited for National Brand Mark
                        Competition
                    </p>
                </div>
                <div class=" row col-md-12 p-20 form-group" style="border:1px solid rgb(219, 219, 223);">
                    {{-- serach data --}}
                    <form action="{{ route('tnblc_report') }}" method="POST">
                        @csrf
                        @method('GET')
                        <input type="hidden" name="source" value="1">
                        <div class=" row col-md-12">
                            <div class=" col-md-2 form-group text-right">
                                <button class=" btn btn-info mt-20 btn-ladda btn-ladda-spinner">Generate Results</button>
                            </div>
                        </div>
                    </form>
                    {{-- end --}}
                </div>
                <!-- Main charts -->
                <div class="row">
                    <div class=" col-md-12 text-right">
                        @if (session('applicationData'))
                            <form action="{{ route('invoice-reports.store') }}" method="POST">
                                @csrf
                                <div class=" text-right">
                                    <button type="submit" class=" btn btn-default text-primary">Export Excel <i
                                            class="icon-download7"></i></button>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="col-md-12 form-group">
                        {{-- datatable-button-html5-columns --}}
                        <table class="table  table-responsive table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-bold text-center">Application Number</th>
                                    <th class=" text-bold text-center">Applicant Name</th>
                                    <th class=" text-bold text-center">Phone Number</th>
                                    {{-- <th class=" text-bold text-center">Description</th> --}}
                                    <th class=" text-bold text-center"> Submission Date</th>
                                    <th class=" text-bold text-center">Download</th>
                                    <th class=" text-bold text-center">Total Marks</th>
                                    <th class=" text-bold text-center">Average Marks</th>
                                    <th class=" text-bold text-center">Mark</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($applications as $key => $item)
                                    <tr>
                                        <td>{{ $item->competitionNumber }}</td>
                                        <td> {{ $item->fullName }} </td>
                                        <td>{{ $item->phoneNumber }} </td>
                                        {{-- <td style="font-size:8px">{{ $item->logoDescription }}</td> --}}
                                        <td>{{ date('M d, Y h:i:s', strtotime($item->submitedAt)) }}</td>

                                        
                                        <td>
                                            
                                            <a href="{{ $item->zippedFile }}">Download File <i
                                                    class=" icon icon-file-download"></i></a>
                                        </td>
                                        <td class=" text-center">
                                            @if (session('showMarks'))
                                                {{ number_format($item->totalMarks,2) }}
                                                @else
                                                <i class=" text-danger">Marking Is in progress</i>
                                            @endif
                                        </td>
                                        <td>
                                            @if (session('showMarks'))
                                                {{ number_format($item->averageMarks,2) }} %
                                                @else
                                                <i class=" text-danger">Marking Is in progress</i>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->status==1)
                                            <form action="{{ route('tnblc_report') }}" method="POST">
                                                @method('GET')
                                                <input type="hidden" name="competitionNumber" value="{{ $item->competitionNumber }}">

                                                <button type="submit" class=" btn btn-primary">Mark Application</button>
                                            </form>
                                            @elseif ($item->status==2)
                                            <button class=" btn btn-warning">Under Marking</button>
                                            @elseif ($item->status==3)
                                            <button class=" btn btn-success"> Marked <i class=" icon-check"></i></button>
                                            @else
                                            <a href="{{ route('send_notification',['applicantId'=>$item->competitionNumber]) }}" class=" btn btn-primary">Send Email</a>
                                            @endif
                                        </td>
                                       
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class=" form-group text-right col-md-12">
                        {{-- {{ $applications->links() }} --}}
                    </div>
                </div>
                <!-- /main charts -->
            </div>
        </div>
    </div>
</x-app-layout>
