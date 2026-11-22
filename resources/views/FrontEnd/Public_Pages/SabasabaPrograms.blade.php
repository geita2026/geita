<!doctype html>

<head>
    <!-- Title -->
    <title>Exhibition Programs</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
    <style>
        .changeColor:hover {
            background-color: rgb(214, 212, 212);
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
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
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('images/Exhibitor2.jpeg') }})">
        <div class="container">
            <div class="bread-inner">

                <div class="row">
                    <div class="col-12">
                        <h2 class=" text-uppercase"> 49<sup>th</sup> Dar es salaam International Trade Fair (Sabasaba)
                            Programs</h2>
                    </div>
                </div>
                <br><br>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->


    <!-- Start Why choose -->
    <div class=" col-md-12">
        <section class="why-choose section">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <h5>
                            An exhibition event program plays a vital role in ensuring the success and smooth
                            operation of
                            an exhibition or similar event. It serves as a guide for attendees, organizers, and
                            participants, providing important information about the eventâ€™s structure, schedule, and
                            activities.Here are key reasons why an exhibition event program is important.
                        </h5>
                    </div>
                </div>
                <div class="row">
                    <div class=" col-md-12 p-3 text-right">
                        <a href="{{ route('preview_programs.show', ['preview_program' => 'DITF_Programs.pdf']) }}"
                            class=" btn btn-primary">Download Progrms <i
                                class=" fa fa-download"></i>
                        </a>
                    </div>
                    <div class="col-lg-12 col-12">

                        <!-- Start Choose Left -->
                        <div class="choose-left">

                            <div class="row">

                                @foreach ($programs as $key => $item)
                                    <div class="col-lg-4 col-md-6 col-12 ">
                                        <div class="single-news zoom" style="border: 1px solid rgb(230, 228, 228);">
                                            <div class="news-head text-center ">
                                                <img src="{{ $item->program_file ?? asset('images/Logo.png') }}"
                                                    alt="#" class=" w-100">
                                            </div>
                                            <div class="news-body p-3">
                                                <div class="news-content text-justify">
                                                    <div class=" text-uppercase"><b>{{ $item->program_title }}</b></div>
                                                    <div class="text text-muted text-justify">
                                                        {{ $item->program_description }}</div>
                                                    <hr>
                                                    <div class="date text-primary">
                                                        <i class=" fa fa-calendar"></i>
                                                        <b>{{ date('l M d, Y', strtotime($item->program_date)) }}</b>
                                                    </div><br>
                                                    <div class=" text-primary">
                                                        <i class=" fa fa-users"></i> <b>{{ $item->characters }}</b>
                                                    </div>
                                                    <br>
                                                    <div class=" text-primary">

                                                        @if (date('Ymd') > date('Ymd', strtotime($item->program_date)))
                                                            <label
                                                                class=" text-uppercase btn bg-success btn-block">Completed
                                                                Program</label>
                                                        @elseif (date('Ymd') == date('Ymd', strtotime($item->program_date)))
                                                            <label
                                                                class=" btn bg-warning btn-block text-uppercase">Ongoing
                                                                Program</label>
                                                        @elseif (date('Ymd') < date('Ymd', strtotime($item->program_date)))
                                                            <label
                                                                class=" btn btn-rounded text-uppercase btn-block">Scheduled
                                                                Program</label>
                                                        @endif

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Single Blog -->
                                    </div>
                                @endforeach

                            </div>


                        </div>
                        <!-- End Choose Left -->
                    </div>


                </div>
            </div>
        </section>
    </div>
    <!--/ End Why choose -->
    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>

