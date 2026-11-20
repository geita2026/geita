<x-app-layout>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
@include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class=" icon-calendar22 position-left"></i>OFF FAIR EVENTS SCHEDULES</a></li>
            <li class="active">View full callendar</li>
        </ul>

        <ul class="breadcrumb-elements">
           @include('layouts.help')
        </ul>
    </div>
    </div>
<!-- /page header -->


<!-- Content area -->
<div class="content">
    <!-- Event colors -->
    <div class="panel panel-flat">
        <div class=" table-header">
            <h5 class=" text-center p-20">OFF FAIR BOOKING SCHEDULERS</h5>

        </div>

        <div class="panel-body">
            <p class="content-group">
               <form action="{{ route('off_fair_booking_schedule.index') }}" method="POST">
                @csrf
                @method('GET')
                <div class=" row form-group">
                    <div class=" form-group col-md-10">
                        <select name="space_id" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select department">
                            <option> - - - - Select Hall - - - -</option>
                            @foreach ($spaces as $data)
                            <option value="{{ $data->space_id }}" class=" text-uppercase">{{ $data->space_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class=" form-group col-md-2">
                        <button class=" btn btn-primary btn-ladda btn-ladda-spinner">View Calendar <i class=" icon-calendar22"></i></button>
                    </div>
                   </div>
               </form>
            </p>

            <div class=" form-group col-md-12">
                @php $i=0; @endphp
                @foreach ($space_name as $item)
                @php $i++; @endphp
                @endforeach
                @if($i>0)
                <h4 class=" alert-success text-uppercase text-center p-10">Booking Calendar for <b>{{ $item->space_name }}</b> HALL</h4>
                @else
                <h4 class=" alert-danger p-10"> Spacify Hall or space to View its Booking Calendar</h4>
                @endif
            </div>

            <div class="fullcalendar-event-colors">

            </div>
        </div>
    </div>
    <!-- /event colors -->
</div>
</x-app-layout>
{{-- <script type="text/javascript" src="{{ asset('assets/js/pages/extra_fullcalendar.js') }}"></script> --}}

<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/moment/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/fullcalendar/fullcalendar.min.js') }}"></script>


<script>

$(function() {

// Add events
// Event colors

var eventColors = [

    @foreach ($events as $value)
    {

        //color logics
        @if ($value->start_date>=date('Y-m-d'))
        @php $color='green'; @endphp
        @elseif ($value->start_date<=date('Y-m-d'))
        @php $color='#97a3b8'; @endphp
        @endif
        //end color logics

        @php
        $start=$value->start_date;
        $end=$value->end_date;
         @endphp
        title: 'BOOKED BY:  {{ $value->customer_name }}',
        url: "{{ route('Off-Fair-Advice-Notes.show',['Off_Fair_Advice_Note'=>$value->advice_note_id]) }}",
        start: '{{ $start }}',
        end: '{{ $end }}',
        color: '{{ $color }}'
    },
    @endforeach

];


// Initialization
// ------------------------------

// Event colors
$('.fullcalendar-event-colors').fullCalendar({
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
    },
    defaultDate: '{{ date("Y-m-d") }}',
    editable: true,
    events: eventColors
});



});
</script>
