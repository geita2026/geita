<style>
    .zoom {
        padding: 2%;
        transition: transform .5s;
        font-size: 20px;
    }

    .zoom:hover {
        -ms-transform: scale(1.1);
        /* IE 9 */
        -webkit-transform: scale(1.1);
        /* Safari 3-8 */
        transform: scale(1.1);
    }

    .systemName {
        font-size: 2vmax;
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
                <li><a href="{{ route('startApp') }}"><i class=" icon-grid2 position-left"></i> Start App</a></li>
                <li class="active">Start App Dashboard</li>
            </ul>
            <ul class="breadcrumb-elements">
                {{-- @include('layouts.help') --}}
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">

        <div class=" alert alert-info" style="font-size: 0.9vmax">
            This system has two modules, <a href="{{ route('dashboard') }}">Exhibition</a> and <a
                href="{{ route('permitDashboard') }}">e-Permit</a>.<br>
            <i class=" icon-arrow-right13 ml-20"></i> <b>Exhibition</b> runs the Geita Mineral Exhibition.<br>
            <i class=" icon-arrow-right13 ml-20"></i> <b>e-Permit</b> involves managing the provision of Trade Promotion
            Event Permits.
        </div>

        <div class="col-md-4 form-group zoom">
            <a href="{{ route('dashboard') }}">
                <div type="submit" @if (session('moduleNo') == 1) style="border-bottom: 10px solid black;" @endif
                    class="btn btn-block btn-float btn-float-lg  text-bold border-primary bg-info"
                    style="border-top: 10px solid green;border-radius:5%;color:black">
                    <span class="systemName">EXHIBITION</span>
                    <hr>
                    <div style="text-align: left; font-size:90%">
                        <p class="text-justify">
                            GeMEMIS - Geita Mineral Exhibition Management Information System handles exhibitor registration,
                            applications, space allocation on the floor plan, invoicing,
                            payment and check-in for the Geita Mineral Exhibition &mdash; the gathering of the
                            mining value chain in Geita: small-scale miners, dealers and processors, equipment
                            suppliers, and the financial and technical services beside them.
                        </p>
                    </div>
                </div>
            </a>
        </div>

        @if (config('global.PERMIT_STATUS') == 1)
            <div class="col-md-4 form-group zoom">
                <a href="{{ route('permitDashboard') }}">
                    <button type="submit"
                        @if (session('moduleNo') == 2) style="border-bottom: 10px solid black;" @endif
                        class="btn btn-block btn-float btn-float-lg  text-bold border-primary bg-info"
                        style="border-top: 10px solid green;border-radius:5%;color:black">
                        <span class="systemName">e-Permit</span>
                        <hr>
                        <div style="text-align: left;">
                            <p class=" text-capitalize text-justify">{{ config('global.MODULE_TWO_DESCRIPTION') }}</p>
                        </div>
                    </button>
                </a>
            </div>
        @endif




    </div>
    </div>
</x-app-layout>
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/c3/c3.min.js') }}"></script>
