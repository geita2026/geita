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

        <div class="alert gememis-module-notice">
            This system has two modules, <a href="{{ route('dashboard') }}">Exhibition</a> and <a
                href="{{ route('permitDashboard') }}">e-Permit</a>.<br>
            <i class=" icon-arrow-right13 ml-20"></i> <b>Exhibition</b> runs the Geita Mineral Exhibition.<br>
            <i class=" icon-arrow-right13 ml-20"></i> <b>e-Permit</b> involves managing the provision of Trade Promotion
            Event Permits.
        </div>

        <div class="row">
            <div class="col-sm-6 col-lg-4 form-group">
                <a class="gememis-module-card" href="{{ route('dashboard') }}">
                    <h2 class="h4 no-margin-top">Exhibition</h2>
                    <p class="no-margin-bottom">
                        Manage exhibitor registration, floor plans, space allocation, invoicing, payments, and check-in
                        for the Geita Mineral Exhibition.
                    </p>
                </a>
            </div>

            @if (config('global.PERMIT_STATUS') == 1)
                <div class="col-sm-6 col-lg-4 form-group">
                    <a class="gememis-module-card" href="{{ route('permitDashboard') }}">
                        <h2 class="h4 no-margin-top">e-Permit</h2>
                        <p class="no-margin-bottom">{{ config('global.MODULE_TWO_DESCRIPTION') }}</p>
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/c3/c3.min.js') }}"></script>
