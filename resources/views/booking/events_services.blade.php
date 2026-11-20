<title>{{ env('APP_NAME') }} | Service Booking</title>

<x-app-layout>
    <style>
        .zoom{
            padding: 2%;
            transition: transform .5s;
            font-size: 20px;
        }
        .zoom:hover {
          -ms-transform: scale(1.2); /* IE 9 */
          -webkit-transform: scale(1.2); /* Safari 3-8 */
          transform: scale(1.2);
        }
        </style>
   {{-- page header --}}
    <div class="page-header">
       <div class="page-header-content">
           @include('layouts.heading-elements')
       </div>
       <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="icon-stack-check position-left"></i>SERVICE BOOKING</a></li>
            <li class="active">View space available for booking</li>
        </ul>
        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>
</div>
{{--  <!-- Content area -->  --}}
<div class="content">
    <div class="panel panel-white">
        <div class=" panel-body">

            <div class=" form-group table-header p-20">
                <h3 class="panel-title text-bold text-uppercase text-center">EXHIBITION SERVICES BOOKING</h3>
            </div>

            <div class="category-content">
                <div class="row row-condensed">
                    @foreach ($events as $event)
                    {{-- Booking --}}
                        <a href="{{ route('book-services.show',['book_service'=>$event->event_id]) }}">
                            <div class="col-md-4 form-group zoom">
                                <input type="hidden" name="event_id" value="{{ $event->event_id }}">
                                <button type="submit" class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary" style="border:5px solid rgb(69, 150, 182);"> <i class=" icon-home"></i> <span>{{ $event->event_name }}</span>
                                    <hr>
                                <div style="text-align: left">
                                <b class=" icon-calendar"> {{ date('d-m-Y',strtotime($event->event_start_date)) }} - {{ date('d-m-Y',strtotime($event->event_end_date)) }}</b>
                                <b class="icon-location4 mt-20" style="font-size: 90%">{{ $event->event_location }}.</b>
                                </div>
                                </button>
                            </div>
                        </a>
                    {{-- /Booking --}}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
 <!-- /content area -->
</div>
<!-- /main content -->
</x-app-layout>
