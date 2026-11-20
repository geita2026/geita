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

            @can('Review Applications')
                @php
                    $unsubmited = 0;
                    $submited = 0;
                    $approved = 0;
                    $permitIssued = 0;
                    $underReview = 0;
                @endphp
                @foreach ($applications as $data)
                    @if ($data->applicationStatus == 1)
                        @php $unsubmited++;  @endphp
                    @elseif ($data->applicationStatus == 2)
                        @php $submited++;  @endphp
                    @elseif ($data->applicationStatus > 2 && $data->applicationStatus < 5)
                        @php $underReview++;  @endphp
                    @elseif ($data->applicationStatus == 5)
                        @php $approved++;  @endphp
                    @elseif ($data->applicationStatus == 6)
                        @php $permitIssued++;  @endphp
                    @endif
                @endforeach
                @php $totalPermit=$permitIssued+$approved+$submited+$unsubmited;  
                if ($totalPermit==0) {
                    $totalPermit=0.000000000001;
                }
                @endphp
                <div class="col-lg-12">
                    <!-- Quick stats boxes -->
                    <div class="row">
                        <a>
                            <div class="col-lg-4 zoom" onclick=" event.preventDefault();document.getElementById('unsubmited').submit();">
                                <!-- Members online -->
                                <div class="panel bg-danger">
                                    <div class="panel-body">
                                        <div class="heading-elements">
                                            <span
                                                class="heading-text badge bg-teal-800">{{ number_format(($unsubmited / $totalPermit) * 100, 2) }}
                                                %</span>
                                        </div>
                                        <h3 class="no-margin">{{ number_format($unsubmited) }}</h3>
                                        UNSUBMITTED
                                    </div>
                                </div>
                                <!-- /members online -->
                            </div>
                        </a>
                        <a class="col-lg-4 zoom" onclick=" event.preventDefault();document.getElementById('submited').submit();">
                            <!-- Current server load -->
                            <div class="panel bg-pink-400">
                                <div class="panel-body">
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li class="dropdown label label-rounded">
                                                {{ number_format(($submited / $totalPermit) * 100, 2) }} %
                                            </li>
                                        </ul>
                                    </div>

                                    <h3 class="no-margin">{{ number_format($submited) }}</h3>
                                    UNATTENDED
                                </div>
                            </div>
                            <!-- /current server load -->

                        </a>

                        <a class="col-lg-4 zoom" onclick=" event.preventDefault();document.getElementById('approved').submit();">
                            <!-- Today's revenue -->
                            <div class="panel bg-primary-800">
                                <div class="panel-body">
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li>{{ number_format(($approved / $totalPermit) * 100, 2) }} %</li>
                                        </ul>
                                    </div>

                                    <h3 class="no-margin">{{ number_format($approved) }}</h3>
                                    APPROVED
                                </div>


                            </div>
                            <!-- /today's revenue -->

                        </a>

                        <a class="col-lg-4 zoom">
                            <!-- Today's revenue -->
                            <div class="panel bg-primary-800">
                                <div class="panel-body">
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li>{{ number_format(($underReview / $totalPermit) * 100, 2) }} %</li>
                                        </ul>
                                    </div>

                                    <h3 class="no-margin">{{ number_format($underReview) }}</h3>
                                    UNDER REVIEW
                                </div>


                            </div>
                            <!-- /today's revenue -->

                        </a>

                        <a class="col-lg-4 zoom" onclick=" event.preventDefault();document.getElementById('issuedPermit').submit();">
                            <!-- Today's revenue -->
                            <div class="panel bg-success-800">
                                <div class="panel-body">
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li>{{ number_format(($permitIssued / $totalPermit) * 100, 2) }} %</li>
                                        </ul>
                                    </div>

                                    <h3 class="no-margin">{{ number_format($permitIssued) }}</h3>
                                    ISSUED PERMIT
                                </div>


                            </div>
                            <!-- /today's revenue -->

                        </a>
                    </div>
                </div>

                <form action="{{ route('review_applications.index') }}" method="post" id="unsubmited">@csrf
                    @method('GET')
                    <input type="hidden" name="applicationStatus" value="1">
                </form>

                <form action="{{ route('review_applications.index') }}" method="post" id="submited">@csrf
                    @method('GET')
                    <input type="hidden" name="applicationStatus" value="2">
                </form>

                <form action="{{ route('review_applications.index') }}" method="post" id="approved">@csrf
                    @method('GET')
                    <input type="hidden" name="applicationStatus" value="5">
                </form>
                <form action="{{ route('review_applications.index') }}" method="post" id="issuedPermit">@csrf
                    @method('GET')
                    <input type="hidden" name="applicationStatus" value="6">
                </form>

            @endcan

            @can('View My Application')
                <div class="col-lg-12">
                    <!-- Daily sales -->
                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h4 class="panel-title text-bold">MY TRADE PROMOTION EVENTS PERMITS ISSUED -
                                {{ date('Y') }}</h4>
                            <div class="heading-elements">
                                <span class="heading-text">Up to: <span
                                        class="text-bold text-danger-600 position-right">{{ date('M d, Y') }}</span></span>
                                <ul class="icons-list">
                                    <li class="dropdown text-muted">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                                class="icon-cog3"></i> <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="{{ route('permitDashboard') }}"><i class="icon-sync"></i> Update
                                                    data</a></li>
                                            <li class="divider"></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>


                        <div class="table-responsive">
                            <table class="table text-nowrap table-bordered">
                                <thead>
                                    <tr class=" bg-info">
                                        <th class=" text-bold">Permit Number</th>
                                        <th class=" text-bold">Event Title</th>
                                        <th>Expire Date</th>
                                        <th class=" text-bold">Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permits as $item)
                                        <tr>
                                            <td>
                                                <div class="media-body">
                                                    <div class="media-heading">
                                                        <a href="{{ route('apply_permit.edit', ['apply_permit' => $item->applicationId]) }}"
                                                            class="letter-icon-title">{{ $item->permitNo }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $item->eventTitle }}</td>
                                            <td>{{ date('l M d, Y', strtotime($item->endDate)) }}</td>
                                            <td class=" text-success">
                                                <a href="{{ route('apply_permit.edit', ['apply_permit' => $item->applicationId]) }}"
                                                    class=" btn btn-rounded btn-default text-primary"> Download <i
                                                        class=" icon-file-download2"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /daily sales -->
                </div>
            @endcan

            <div class=" col-md-12">
                <!-- Latest posts -->
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h4 class="panel-title">Types of Trade Promotion Events Available</h4>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                                <li><a data-action="reload"></a></li>
                                <li><a data-action="close"></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            @foreach ($promotionEventTypes as $value)
                                <div class="col-lg-6">
                                    <ul class="media-list content-group">
                                        <li class="media stack-media-on-mobile">
                                            <div class="media-left">
                                                <div class="thumb">
                                                    <a href="#">
                                                        @if ($value->eventTypePoster != null)
                                                            <img src="{{ $value->eventTypePoster }}"
                                                                class="img-responsive img-rounded media-preview"
                                                                alt="">
                                                            <span class="zoom-image"><i class="icon-play3"></i></span>
                                                        @else
                                                            <img src="{{ asset('assets/images/placeholder.jpg') }}"
                                                                class="img-responsive img-rounded media-preview"
                                                                alt="">
                                                            <span class="zoom-image"><i class="icon-play3"></i></span>
                                                        @endif

                                                    </a>
                                                </div>
                                            </div>

                                            <div class="media-body">
                                                <h6 class="media-heading"><a
                                                        href="#">{{ $value->promotionEventTypeName }}</a></h6>
                                                <ul class="list-inline list-inline-separate text-muted mb-5">
                                                    <li><i class=" icon-coin-dollar position-left"></i>
                                                        {{ number_format($value->eventTypeAmountUsd, 2) }} USD (For
                                                        International Exhibition)</li><br>
                                                    <li><i class=" icon-coin-dollar position-left"></i>
                                                        {{ number_format($value->eventTypeAmountTzs, 2) }} TZS (For
                                                        Local Exhibition)</li>
                                                </ul>
                                                {{ $value->promotionEventTypeDescription }}
                                            </div>
                                        </li>


                                    </ul>
                                </div>
                            @endforeach


                        </div>
                    </div>
                </div>
                <!-- /latest posts -->
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
