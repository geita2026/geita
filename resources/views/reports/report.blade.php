

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
    <title>{{ env('APP_NAME') }} | Reports</title>
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
           @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="#"><i class=" icon-graph position-left"></i> REPORTS</a></li>
           <li class="active">View all reports</li>
       </ul>

       <ul class="breadcrumb-elements">
        @include('layouts.help')
       </ul>
   </div>
   </div>
<!-- /page header -->

<!-- Content area -->
<div class=" content">
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title text-bold"> REPORT DASHBOARD</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>
        <div class=" panel-body table-responsive">

            <div class=" alert-success p-20" style="font-size:10px">
                   <li>Reports In details Can viewed by clicking the button labled (<b>View In Details</b>) located on the top of each specific virtual representation</li>
            </div>


            <div class=" p-20">

                @can('View Booking Progress Reports')
            <div class="row zoom text-center mb-20" style="border:3px solid rgb(169, 169, 187); border-radius:0.5%">
                <div class=" form-group">
                    <h4 class=" text-bold text-monospace" style=" border-bottom:1px dotted rgb(20, 206, 206);">CURRENT EXHIBITION SPACE BOOKING STATUS</h4>
                </div>
                {{--  pie char  --}}
             <div class=" form-group col-md-6 ">
                <a href="{{ route('bookedUnbookedSpaces.index') }}">
                    <div class="display-inline-block" id="booking"></div>
                </a>
                </div>
                <div class=" form-group col-md-5 btn btn-default">
                    <table class=" table table-bordered table-striped">
                        <thead>
                            <tr class=" alert-info">
                                <th class=" text-bold text-center">DESCRIPTION</th>
                                <th class="text-bold text-center">SPACE SIZE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class=" text-bold">TOTAL SPACE: </td>
                                <td class=" alert-link">{{ number_format($size)}} M<sup>2</sup></td>
                            </tr>

                            <tr>
                                <td class=" text-bold">BOOKED SPACE:</td>
                                <td class=" alert-success">{{ number_format($booked)}} M<sup>2</sup></td>
                            </tr>
                            <tr>
                                <td class=" text-bold">FREE SPACE:</td>
                                <td class=" alert-warning">{{ number_format($size-$booked)}} M<sup>2</sup></td>
                            </tr>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{--  end pie chart  --}}
            @endcan




            @can('View All Exhibitor Reports')
            <div class="row zoom text-center mt-20" style="border:3px solid rgb(51, 51, 58); border-radius:0.5%">
                <div class=" form-group">
                    <h4 class=" text-bold text-monospace" style=" border-bottom:1px dotted rgb(20, 206, 206);">CURRENT EXHIBITORS PARTICIPATION STATUS</h4>
                </div>

                <div class=" form-group col-md-6 ml-20 btn btn-default">
                    <table class=" table table-bordered table-striped">
                        <thead>
                            <tr class=" alert-primary">
                                <th class=" text-bold">DESCRIPTIONS</th>
                                <th class=" text-bold">EXHIBITORS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                               <tr>
                                <th class=" text-bold">CONFIRMED PARTICIPATIONS:</th>
                                <td class=" alert-link">{{ number_format($companies)}}</td>

                               </tr>
                               <tr>
                                <th class=" text-bold">NEW EXHIBITORS:</th>
                                <td class=" alert-success">{{ number_format($exhibitors)}}</td>

                            </tr>
                            <tr>
                                <th class=" text-bold">OLD EXHIBITORS:</th>
                                <td class=" alert-warning">{{ number_format($companies-$exhibitors)}}</td>

                            </tr>



                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class=" form-group col-md-4">
                    <a href="{{ route('exhibitors.index') }}" class="">
                    <div class="display-inline-block" id="exhibitors"></div>
                </a>
                </div>

            </div>
            {{--  end pie chart  --}}
            @endcan



            @can('View All Advice Notes')
            <div class="row zoom text-center mt-20" style="border:3px solid rgb(51, 51, 58); border-radius:0.5%">
                <div class=" form-group">
                    <h4 class=" text-bold text-monospace" style=" border-bottom:1px dotted rgb(20, 206, 206);">CURRENT INVOICES(ADVICE NOTES) STATUS</h4>
                </div>
                {{--  pie char  --}}
             <div class=" form-group col-md-6">
                <a href="{{ route('adviceNote-reports.index') }}">
                    <div class="display-inline-block" id="adviceNote"></div>
                </a>
            </div>
            <div class=" col-md-5 btn btn-default">
                <table class=" table table-bordered table-striped">
                    <thead>
                        <tr class=" alert-primary">
                            <th class=" text-bold text-center">DESCRIPTIONS</th>
                            <th class=" text-bold text-center">NUMBER</th>
                        </tr>
                    </thead>
                    <tbody>

                            <tr>
                                <th class=" text-uppercase text-bold">Generated INVOICES</th>
                                <td class=" alert-link text-right">{{ number_format($adviceNoteCount)}}</td>
                            </tr>
                            <tr>
                                <th class=" text-uppercase text-bold">ATTENDED INVOICES</th>
                                <td class=" alert-link text-right">{{ number_format($attended)}}</td>
                            </tr>
                            <tr>
                                <th class=" text-uppercase text-bold">PENDING INVOICES</th>
                                <td class=" alert-link text-right">{{ number_format($pending)}}</td>
                            </tr>

                            <tr>
                                <th class=" text-bold text-uppercase">Canceled Invoices</th>
                                <td class=" alert-danger text-right">{{ number_format($canceled)}}</td>
                            </tr>


                    </tbody>
                </table>
            </div>

            </div>
            {{--  end pie chart  --}}
            @endcan
           </button>
            </div>




        </div>
    </div>
    <!-- /content area -->
</div>
<!-- /main content -->
</x-app-layout>

{{--  <script type="text/javascript" src="{{ asset('assets/js/charts/c3/c3_bars_pies.js') }}"></script>  --}}
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/c3/c3.min.js') }}"></script>

<script>
    /* ------------------------------------------------------------------------------
 *
 *  # C3.js - bars and pies
 *
 *  Demo setup of bars, pies and chart combinations
 *
 *  Version: 1.0
 *  Latest update: August 1, 2015
 *
 * ---------------------------------------------------------------------------- */

$(function() {

    //booking status
    var pie_chart = c3.generate({
        bindto: '#booking',
        size: { width: 500 },
        color: {
            pattern: ['#3F51B5', '#FF9800', '#4CAF50', '#00BCD4', '#F44336']
        },
        data: {
            columns: [
                ['Booked Space', {{ $booked }}],
                ['Free Space (Un booked)', {{ $size-$booked }}],
            ],
            type: 'pie'
        }
    });
    //end booking status


    //exhibitors
    var pie_chart = c3.generate({
        bindto: '#exhibitors',
        size: { width: 500 },
        color: {
            pattern: ['#4CAF50', '#F44336', '#4CAF50', '#00BCD4', '#F44336']
        },
        data: {
            columns: [
                ['Confirmed For Participation', {{$exhibitors }}],
                ['Not Confirmed For Participation', {{ $companies-$exhibitors }}],
            ],
            type: 'pie'
        }
    });

    //end exhibitors


      //exhibitors
      var pie_chart = c3.generate({
        bindto: '#adviceNote',
        size: { width: 500 },
        color: {
            pattern: ['#00FF00', '#00BCD4', '#FFA500', '#FF0000']
        },
        data: {
            columns: [
                ['Attended Advice Note', {{ $attended }}],
                ['Pending Advice Note', {{ $pending }}],
                ['Canceled Advice Note', {{ $canceled }}],
            ],
            type: 'pie'
        }
    });

    //end exhibito
    // ------------------------------
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
