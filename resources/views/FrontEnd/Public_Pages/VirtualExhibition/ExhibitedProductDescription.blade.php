<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | VirtualTour</title>

    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">

<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>

<style>
    .grabbing {
        cursor: -webkit-grabbing;
        cursor: grabbing;
    }

    #myVideo {
        position: fixed;
        right: 0;
        bottom: 0;
        min-width: 100%;
        min-height: 100%;
    }
</style>
@include('WelcomePageLayouts.welcomeCss')

<body>
    <video muted autoplay loop id="myVideo">
        <source src="{{ URL::asset('Advertisements/17-04-2023 17:08:39.mp4') }}" type="video/mp4"
            style="width: 100%; height:100%">
        Your browser does not support the video tag.
    </video>



    <!-- Page container -->
    <div class="page-container ">
        <!-- Page content -->
        <div class="page-content">
            <!-- Main content -->
            <div class="content-wrapper">

                <!-- HEADER MENU-->
                @include('WelcomePageLayouts.welcomeHeaderMenu')
                <!-- END HEADER MENU-->

                <div class=" panel table-responsive">
                    <div class=" panel-body">

                        {{-- <div class=" form-group  col-md-12">
                            <a href="{{ route('discover_exhibitor.show',['discover_exhibitor'=>session('companyID')]) }}" class=" btn btn-default text-primary"><i
                                    class=" icon-arrow-left12"></i> Back</a>
                        </div> --}}

                        <div class=" col-md-12 form-group p-5">
                            <p style="font-size: 2vmin">
                                <b>TanTrade</b> catalyses collaboration, stimulates innovation and creates tangible
                                opportunities for all, Preview All products And obtain products of your interest, Then
                                contact exhibitor for more information.
                            </p>
                        </div>

                        <div class="row  col-md-12 form-group">
                            @php $j=0; @endphp
                            @foreach ($myProducts as $item)
                                <div class="col-lg-3 col-md-6">
                                    <div class="thumbnail no-padding zoom">
                                        <div class="thumb">
                                            <img src="{{ asset('VirtualImages/' . $item->service_image_name) }}"
                                                alt="" class=" img-fluid">
                                            <div class="caption-overflow">
                                                <span>
                                                    <a href="{{ asset('VirtualImages/' . $item->service_image_name) }}"
                                                        class="btn bg-success-400 btn-icon btn-xs"
                                                        data-popup="lightbox"><i class=" icon-eye"></i></a>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="caption" style="text-align: justify">
                                                .....
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-9 zoom">
                                    <p class=" ">
                                        <div class="caption" style="text-align: justify">
                                            <h6 class=" text-bold no-margin">{{ $item->service_name }}</h6>
                                            <p class="display-block">
                                                {{ $item->service_descriptions }}</p>
                                            
                                        </div>

                                    </p>
                                    <p> <b>Exhibited By: </b>{{ $item->companyName }}</p>
                                                
                                                @if ($item->event_status==1)
                                                <a href="{{ route('discover_exhibitor.show', ['discover_exhibitor' => $item->exhibitor_id]) }}"
                                                    class="btn-default btn-block btn text-primary">View My Stand <i class=" icon-pin"></i>
                                                </a>
                                                @endif
                                            </p>
                                            <p><i class="  icon-sphere"></i> <a href="{{ $item->website }}"
                                                    target="_newtag">{{ $item->website }}</a>
                                            </p>
                                </div>

                                @php $j++; @endphp
                            @endforeach

                            @if ($j == 0)
                                <div class=" alert alert-warning">
                                    <h3>No product or service Posted Now. Note: event will start on Jun 28 - July 2024.
                                    </h3>
                                </div>
                            @endif
                        </div>



                    </div>
                </div>

            </div>
            <!-- /search results -->
        </div>
        <!-- /main content -->
    </div>
    <!-- /page content -->
    @include('layouts.footer')
    </div>
    <!-- /page container -->
</body>

</html>
@include('FrontEnd.Public_Pages.Layouts.scripts')

<script type="text/javascript" src="{{ asset('assets/js/pages/components_popups.js') }}"></script>

