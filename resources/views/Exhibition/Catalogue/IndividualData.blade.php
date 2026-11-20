<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | Exhibitor Info</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    @include('WelcomePageLayouts.welcomeCss')
</head>

<body>
    <!-- Page container -->
    <div class="page-container">
        <!-- Page content -->
        <div class="page-content">

            <!-- Main content -->
            <div class="content-wrapper">
                <!-- HEADER MENU-->
                @include('WelcomePageLayouts.welcomeHeaderMenu')
                <!-- END HEADER MENU-->

                <div class=" panel  panel-white table-responsive">
                    <div class=" panel-body">

                        <div class=" form-group  col-md-12">
                            <a href="{{ route('discover_exhibitor.index') }}" class=" btn btn-default text-primary"><i
                                    class=" icon-arrow-left12"></i> Back</a>
                        </div>

                        <!-- Cover image -->


                        <div class=" card  col-md-12 form-group"
                            style="background-image: url('{{ asset('images/Banda.jpeg') }}'); background-color: rgba(4, 193, 250, 0.541);">

                            <div class=" col-md-12 zoom" style="background-color: rgba(156, 212, 208, 0.534);">
                                <div class=" card-header text-center">
                                    @if ($exhibitors->companyLogo)
                                        <img src="{{ asset('profile_images/' . $exhibitors->companyLogo) }}"
                                            class="img-circle" alt="" height="100" width="100"
                                            style="border:1px solid rgb(105, 177, 182);">
                                    @else
                                        <img src="{{ asset('assets/images/placeholder.jpg') }}" class="img-circle"
                                            alt="" height="100" width="100">
                                    @endif
                                    <br>
                                    <span style="font-size: 4vmin"
                                        class=" text-uppercase"><b>{{ $exhibitors->companyName }}</b></span><br>
                                    <span class=" text-muted" style="font-size: 2vmin;">{{ $exhibitors->sectorName }}</span>
                                    <br>
                                    <span class="font-size: 4vmin">
                                        {{ $exhibitors->pavilion_descriptions }}
                                    </span>
                                </div>
                                <div class=" card-body" style="font-size:3vmin">

                                    @php $i=0; @endphp
                                    <div class="">
                                        <b><i class=" icon-location4" style="font-size:3vmin"></i>My Stand: </b><br>

                                        @foreach ($myStand as $value)
                                            {{ $value->boothNo }},
                                        @endforeach

                                        @php $i++; @endphp
                                        [<b>{{ $value->pavilionName ?? '...' }}</b>]
                                    </div>
                                    @foreach ($myPavilion as $data)
                                        <div>& {{ $data->pavilionName }},</div>
                                    @endforeach

                                    <div class=" col-md-12">
                                        @php $i=1; @endphp
                                        <b>Participation Rating:</b><br>
                                        @while ($i <= $exhibitorCount)
                                            <i class=" icon-star-full2 text-white"></i>
                                            @php $i++; @endphp
                                        @endwhile
                                        <div>
                                            <b>Pavilion Visitors: </b><br> {{ number_format($visitorsCount) }}
                                        </div>
                                    </div>
                                    <div class=" col-md-12 text-center">
                                        <button class="myButton" style="border: 1px solid orange;"><a
                                                href="{{ route('ditf_map') }}">Visit {{ $exhibitors->companyName }} on
                                                map</a></button>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- SERVICES -->
                        <div class="row  col-md-12 form-group">
                            @php $j=0; @endphp
                            @foreach ($myProducts as $item)
                                <div class="col-lg-2 col-md-6">
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
                                            <h6 class=" text-bold no-margin">{{ $item->service_name }}</h6>

                                            <p class="display-block">
                                                {{ Illuminate\Support\Str::limit($item->service_descriptions, 100) }}
                                                <a
                                                    href="{{ route('exhibited_products.show', ['exhibited_product' => $item->virtual_service_id]) }}">view
                                                    more
                                                </a>
                                            </p>
                                            <p><i class="  icon-sphere"></i> <a href="{{ $exhibitors->website }}"
                                                    target="_newtag">{{ $exhibitors->website }}</a></p>
                                        </div>
                                    </div>
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
            <!-- /main content -->

        </div>
        <!-- /page content -->
        @include('layouts.footer')

    </div>
    <!-- /page container -->

</body>

</html>
