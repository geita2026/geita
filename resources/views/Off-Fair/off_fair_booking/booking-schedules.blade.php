
<x-app-layout>
    <title>{{ env('APP_NAME') }} | Chalender</title>
   <div class="page-header">
       <div class="page-header-content">
           @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="{{ route('Off-Fair.index') }}"><i class="icon-home2 position-left"></i>Off-Fair</a></li>
           <li class="active">Off-Fair Dashboard</li>
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
        <div class="panel-heading">
            <h6 class="panel-title text-bold">OFF FAIR SPACE BOOKING SCHEDULES</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload" onclick="updateDiv()"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>

<div class=" panel-body table-responsive">
     {{-- validation messages --}}
     @if ($errors->any())
     @foreach ($errors->all() as $error)
     <span class="alert text-danger">{{ $error }} ,</span>
     @endforeach
     @endif
     {{-- //validation messages --}}

        <form action="{{ route('check-booking-status.show',['check_booking_status']) }}" method="GET">
            @csrf
            <div class=" row form-group">
                <h1>Check Free Spaces </h1>
                <div class=" col-md-4">
                    <label>From Date</label>
                    <input type="date" name="from_date" class=" form-control">
                </div>
                <div class=" col-md-4">
                    <label>To date</label>
                    <input type="date" name="to_date" class=" form-control">
                </div>
                <div class=" col-md-4 mt-20">
                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner">Load Data <i class=" icon-reload-alt"></i></button>
                </div>
            </div>
        </form>

        <div class=" form-group">
            <table class=" table table-bordered">
                <thead>
                    <tr class=" text-uppercase">
                        {{-- <th class=" text-bold text-center">#</th> --}}
                        <th class=" text-bold text-center">SPACE BOOKING SCHEDULER</th>
                    </tr>
                </thead>
                <tbody>
                    @php  $i=1; @endphp
                    @foreach ($spaces as $item)
                    <tr>
                        <td >
                           <a data-toggle="modal" data-target="#schedule{{ $item->space_id }}" class=" text-bold text-uppercase" style="font-size:110%"> {{ $item->space_name }} <i class=" icon-calendar2"></i></a>
                            {{-- /loaded data from database --}}
                            <div id="schedule{{ $item->space_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-full">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <h4 class="modal-title text-bold text-center text-uppercase"><q>{{ $item->space_name }}</q> OFF FAIR SPACE BOOKING CALENDER</h5>
                                            </div>
                                            <div class="modal-body">

                                                <div class=" form-group">
                                                    @foreach ($booked_spaces as $data)
                                                @if ($item->space_id==$data->off_fair_space_id)
                                                @php
                                                $duration1=strtotime($data->end_date)-strtotime($data->start_date);
                                                $duration=abs(round($duration1 / 86400));
                                                if($duration>30)
                                                {
                                                    $duration=number_format($duration/30)." Months";
                                                }
                                                @endphp
                                                <div class=" col-md-4" style="font-size:20px">
                                                    <i class=" icon-arrow-right14"></i>
                                                    <a class=" text-warning text-bold" data-popup="tooltip-custom">{{ date('d-m-Y',strtotime($data->start_date))  }} To {{ date('d-m-Y',strtotime($data->end_date)) }} ({{ $duration }})
                                                    </a>
                                                </div>
                                                @endif
                                                @endforeach
                                                </div>

                                            </div>
                                                <div class=" modal-footer">
                                                    <div class="  form-group text-right">
                                                        <button class=" btn btn-warning" data-dismiss="modal"> Close</button>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                    {{-- // new booking modal --}}
                                </td>
                            </tr>
                            @php  $i++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /content area -->
    <!-- /main content -->
</x-app-layout>
