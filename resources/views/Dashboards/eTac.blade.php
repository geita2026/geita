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
    <title>{{ config('global.MODULE_TWO') }} | Dashboard</title>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permitDashboard') }}"><i class="icon-home2 position-left"></i> ePermit</a></li>
                <li class="active">Dashboard</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <div class="content">
        {{-- @include('company.NewCompanyRegistration') --}}
        <!-- Dashboard content -->
        <div class="row">


            <div class="col-lg-12">
                <!-- Quick stats boxes -->
                <div class="row">
                    <a>
                        <div class="col-lg-4 zoom"
                             onclick=" event.preventDefault();document.getElementById('unsubmited').submit();">
                            <!-- Members online -->
                            <div class="panel bg-danger">
                                <div class="panel-body">

                                    <h3 class="no-margin">{{ number_format($TotalSales) }} TZS</h3>
                                    Total Today Gate Entrance Collections
                                </div>
                            </div>
                            <!-- /members online -->
                        </div>
                    </a>
                    <a class="col-lg-4 zoom"
                       onclick=" event.preventDefault();document.getElementById('submited').submit();">
                        <!-- Current server load -->
                        <div class="panel bg-pink-400">
                            <div class="panel-body">

                                <h3 class="no-margin">{{ number_format($totalOnlineTickets) }}</h3>
                                Total Toay Amount Collected for Online Tickets
                            </div>
                        </div>
                        <!-- /current server load -->
                    </a>
                </div>
            </div>
        </div>
        <!-- /dashboard content -->
    </div>
    </div>
</x-app-layout>
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/c3/c3.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/pages/wizard_steps.js') }}"></script>
<script>
    function submitForm() {
        document.getElementById("myForm").submit(); // Submits the form
    }
</script>
