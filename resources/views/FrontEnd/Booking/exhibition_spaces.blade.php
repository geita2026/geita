
<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booking</title>
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
            <li><a href="{{ route('booking.index') }}"><i class="icon-stack-check position-left"></i>BOOKING</a></li>
            <li class="active">View space available for booking</li>
        </ul>
        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>

 </div>
 {{--  <!-- /page header -->  --}}

 {{--  <!-- Content area -->  --}}
 <div class="content">
     <div class="panel panel-white">
         <div class="panel-heading">
             <h4 class="panel-title text-bold text-uppercase">
                @foreach ($spaces as $value) @endforeach
                <b class=" text-primary">{{$value->category_name}}</b>  Space List
                 </h4>
             <div class="heading-elements">
                 <ul class="icons-list">
                     <li><a data-action="collapse"></a></li>
                     <li><a data-action="reload"></a></li>
                     <li><a data-action="close"></a></li>
                 </ul>
             </div>
         </div>

         <div class=" panel-body">
             {{--  in submission error messages  --}}
             @if ($errors->any())
             @foreach ($errors->all() as $error)
             <span class=" text-danger alert">{{ $error }},</span>
             @endforeach
             @endif
             {{--  //end error messages  --}}




             <div class=" row">
                <div class=" form-group col-md-3">
                    <a href="{{ route('space-categories.show',['space_category'=>session('event_id')]) }}" class=" btn btn-default text-primary"><i class=" icon-arrow-left12"></i> Back</a>
                </div>

                <div class=" col-md-6">
                    <input type="search" class=" form-control input-lg" placeholder="Search space by space Name" id="search-space">
                </div>
             </div>


             @foreach ($spaces as $data)
             <a href="{{ route('customer_spaces.show',['customer_space'=>$data->pavilionID]) }}">
                <div class="col-md-3 form-group zoom">
                <button class="btn btn-block btn-float btn-float-lg border-success text-uppercase text-bold" href="#"  style=" text-align:left" title="{{ $data->pavilionDescription }}"  data-popup="tooltip-custom">
                    <b>{{ $data->pavilionName }}</b><br>
                    <small style=" font-size:80%">Space Size: {{ number_format($data->pavilion_size,2) }} M<sup>2</sup></small><br>
                    <small style=" font-size:80%">Booking Status: @if($data->bookingStatus==1) <b class=" text-warning">BOOKED</b> @else <b class="text-success">FREE SPACE</b> @endif</small>
                </button>
                </div>
            </a>
            @endforeach
        </div>
  {{-- end booking logics --}}

 </div>
 <!-- /main content -->
 </div>
 </div>
 </x-app-layout>


 <script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/widgets.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/effects.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/plugins/extensions/mousewheel.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/globalize.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/cultures/globalize.culture.de-DE.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/cultures/globalize.culture.ja-JP.js') }}"></script>

 <script>
      var projects = [
        @foreach ($spaces as $data)
        {
         companyID:"{{ $data->pavilionID }}",
         label: "{{ $data->pavilionName }} - {{ $data->pavilion_size }} unit / Msq - {{ $data->category_name }}",
         desc: "<i>{{ $data->pavilionDescription }}</i>",
        },
        @endforeach
     ];
      // Initialize autocomplete
      $("#search-space").autocomplete({
         minLength: 0,
         source: projects,
         focus: function( event, ui ) {
             $("#search-space").val(ui.item.label);
             return false;
         },
         a: function( event, ui ) {
             $("#search-space").val(ui.item.label);
             return false;
         }
     })
     .autocomplete("instance")._renderItem = function(ul, item) {
         return $("<li class=' bg-teal-400'>").append('<a href="/booking/'+ item.companyID +'" class="btn border-white" style="text-align:left;">' +  "<span style='font-size:130%' class='text-uppercase text-bold'>"+ item.label+ '</span>' + '<span class="text-size-small"> <br>' + item.desc +'</span></li>' + '</a>').appendTo(ul);
     };
 </script>
