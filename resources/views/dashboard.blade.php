
 <style>
    .zoom{
        padding: 2%;
        transition: transform .5s;
        font-size: 20px;
    }
    .zoom:hover {
      -ms-transform: scale(1.1); /* IE 9 */
      -webkit-transform: scale(1.1); /* Safari 3-8 */
      transform: scale(1.1);
    }
    </style>

<x-app-layout>
    <title>{{ env('APP_NAME') }} | Home</title>
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
           @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i> Home</a></li>
           <li class="active">Dashboard</li>
       </ul>

       <ul class="breadcrumb-elements">
        @include('layouts.help')
       </ul>
   </div>
   </div>
<!-- /page header -->

<!-- Content area -->
<div class="content">

    {{-- Internal User access dashboard I --}}
    @if (Auth::user()->account_type==1)
    @include('Dashboards.InternalUser')
    {{-- Internal User access dashboard I--}}
    
    {{-- for external user access Dashboard II --}}
    @elseif (Auth::user()->account_type==2)
    @include('Dashboards.ExternalUser')
    {{-- for external user --}}

    @endif
</div>
</div>


<!-- /primary modal -->

</x-app-layout>


<script>
$(function () {

    // Generate chart
    var bar_chart = c3.generate({
        bindto: '#exhibitor_chart2',
        size: { height: 400,
        width:950, },
        data: {
            columns: [

                @foreach ($events as $event)
                @php $total_Exhibitor=0; @endphp
                  @foreach ($exhibitors as $exhibitor)
                    @if ($exhibitor->exhibitor_event==$event->event_id)
                        @php $total_Exhibitor++; @endphp
                    @endif
                  @endforeach

                ['{{ $event->event_name }}', {{ $total_Exhibitor }}],
                @endforeach
            ],

            type: 'bar'
        },
        color: {
            pattern: ['#2196F3', '#FF9800', '#4CAF50']
        },
        bar: {
            width: {
                ratio: 0.35
            }
        },
        grid: {
            y: {
                show: true,
            }
        },


    });

    // Resize chart on sidebar width change
    $(".sidebar-control").on('click', function() {
        pie_chart.resize();
        donut_chart.resize();
        bar_chart.resize();
        bar_stacked_chart.resize();
        combined_chart.resize();
        scatter_chart.resize();
    });
});

</script>

<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/c3/c3.min.js') }}"></script>





