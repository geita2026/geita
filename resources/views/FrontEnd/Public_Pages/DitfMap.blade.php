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
    <title>Site map | GeMEMIS</title>
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

    <!-- Breadcrumbs -->
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('images/geita/geita-mine.jpg') }})">
        <div class="container">
            <div class="bread-inner">

                <div class="row">
                    <div class="col-12">
                        <h2>Site map</h2>
                        <ul class="bread-list">
                            <li><a href="{{ route('index') }}">Home</a></li>
                            <li><i class="icofont-simple-right"></i></li>
                            <li class="active">Site map</li>
                        </ul>
                    </div>
                </div>
                <br><br>


            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->


    <!-- Start Why choose -->
    <section class="why-choose section">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2>{{ $event->event_name }} &mdash; Site Map</h2>
                        <img src="{{ asset('images/coat-of-arms.svg') }}" alt="#">
                        <p class=" text-justify">
                            The <b>Geita Mineral Exhibition Grounds</b> sit in Geita, in the north-west of Tanzania on
                            the southern shore of Lake Victoria, at the centre of the Lake Victoria goldfields. The
                            grounds are laid out as halls, covered stands and open outdoor plots, so that everything
                            from a bench of cut and polished stones to a loader or a crushing plant can be shown in
                            the right kind of space.
                        </p>
                        <p><b>What you will find on the grounds:</b></p>
                        <p class=" text-justify">
                            <b>Exhibition halls:</b> enclosed stands for mineral dealers and processors, assay and
                            laboratory services, banks, insurers and government agencies.<br>

                            <b>Outdoor plots:</b> open ground for mining and processing equipment, vehicles and
                            machinery that cannot be shown indoors.<br>

                            <b>Small-scale miners&rsquo; pavilion:</b> stands set aside for small-scale miners and their
                            associations, and for the women entrepreneurs and local businesses of Geita and the
                            neighbouring districts.<br>

                            <b>Meeting and demonstration areas:</b> space for buyer&ndash;seller meetings, technical
                            demonstrations, training sessions and the official programme.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-12">
                    <!-- Start Choose Left -->
                    <div class="choose-left">
                        <h3>Finding the grounds</h3>
                        <p style="text-align: justify;font-size:2vmin">
                            The exhibition grounds are in Geita, Geita Region, Tanzania. The plan below shows the
                            halls, pavilions, outdoor plots, gates and amenities; the numbered spaces on it are the
                            ones you choose from when you apply through GeMEMIS.
                            <a href="#" class=" text-info">View in Google Map <i
                                    class=" icofont icofont-location-pin"></i></a>
                        </p>
                        <p style="text-align: center">
                            {{-- Replace public/images/DITFMAP.png with the Geita exhibition grounds plan. --}}
                            <img src="{{ asset('images/DITFMAP.png') }}" alt="Plan of the exhibition grounds" style="width: 100%; height:90%; width:90%">
                        </p>
                    </div>
                    <!-- End Choose Left -->
                </div>


            </div>
        </div>
    </section>
    <!--/ End Why choose -->



    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>
