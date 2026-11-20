<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="Site keywords here">
    <meta name="description" content="">
    <meta name='copyright' content=''>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>Exhibitor Details</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
    <style>
        .changeColor:hover {
            background-color: rgb(214, 212, 212);
        }
    </style>
</head>

<body>

    <!-- Preloader -->
    @include('FrontEnd.Public_Pages.Layouts.PageLoader')
    <!-- End Preloader -->


    <!-- Get Pro Button -->

    <!-- Header Area -->
    <header class="header">
        <!-- Topbar -->
        @include('FrontEnd.Public_Pages.Layouts.TopHeader')
        <!-- End Topbar -->
        <!-- Header Inner -->
        @include('FrontEnd.Public_Pages.Layouts.Header')
        <!--/ End Header Inner -->
    </header>
    <!-- End Header Area -->

    <div class=" col-md-12">
        <section class="why-choose section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <h2 class=" text-uppercase">
								<img src="{{ asset('profile_images/' . $exhibitors->companyLogo) }}" alt="#" style="width: 50px; height: 50px;border-radius:20%">
								 {{ $exhibitors->companyName }}</h2>
                            <p>{{ $exhibitors->welcome_word }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <!-- Start Choose Left -->
                        <div class="choose-left">
                            <h3>Who We Are</h3>
                            <p>{{ $exhibitors->pavilion_descriptions }}</p>
							<p><b>Website: </b><a href="{{ $exhibitors->website }}" target="_newtab">{{ $exhibitors->website }}</a></p>

                            <div class="row">
                                <div class="col-lg-6">
                                    <ul class="list">
                                        @foreach ($myProducts as $item)
                                            <li><i class="fa fa-caret-right"></i>{{ $item->service_name }} </li>
                                        @endforeach
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <!-- End Choose Left -->
                    </div>
                    <div class="col-lg-6 col-12">
                        <!-- Start Choose Rights -->
                        <div class="choose-right">
                            <div class="video-image">
                                <!-- Video Animation -->
                                <div class="promo-video">
                                    <div class="waves-block">
                                        @php $i=1; @endphp
                                        @foreach ($myProducts as $data)
                                            <div class="waves wave-{{ $i }}"></div>
                                            @php $i++; @endphp
                                        @endforeach
                                    </div>
                                </div>
                                <!--/ End Video Animation -->
                                <a href="https://www.youtube.com/watch?v=or5-mQX2N40"
                                    class="video video-popup mfp-iframe"><i class="fa fa-play"></i></a>
                            </div>
                        </div>
                        <!-- End Choose Rights -->
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>
