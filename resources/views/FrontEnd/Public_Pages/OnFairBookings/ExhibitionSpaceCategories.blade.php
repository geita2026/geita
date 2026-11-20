<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <!-- Meta Tags -->
    <!-- Title -->
    <title>{{ env('APP_NAME') }} | Space Categories</title>
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
    @include('FrontEnd.Public_Pages.Layouts.NewsButton')

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
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('images/BackGround.jpg') }})">
        <div class="container">
            <div class="bread-inner">
                <div class="row">
                    <div class="col-12">
                        <h2 class=" text-uppercase">Exhibition Space Booking</h2>
                    </div>
                </div>
                <br><br>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <section class=" col-md-12">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 ">
                        <p style=" text-align:justify;padding:1%" >
                            <b>Say goodbye to the hassle of manual bookings! With our Online Space Booking System, you
                                can:</b><br>
                            ✅ Explore Available Spaces – View layouts and choose your perfect space.<br>
                            ✅ Instant Reservations – Secure your space with just a few clicks.<br>
                            ✅ 24/7 Access – Book anytime, anywhere, at your convenience.<br>
                            ✅ Transparent Pricing – See all costs upfront with no hidden fees.<br>
                            🌟 Don’t Miss Out! Secure your exhibition space today by paing your bill and make your mark
                            at the event!
                        </p>

                    
                </div>
            </div>



            <div class="row  col-md-12" style="width: 100%">
                @foreach ($spaceCategories as $item)
                    <div class="col-lg-4 col-md-6 col-12 zoom"
                        style="border: 5px solid rgba(116, 203, 230, 0.233);border-radius:1%">
                        <!-- Single Blog -->
                        <div class="single-news">
                            <div class="news-body p-15">
                                <div class="news-content">
                                    <h2 class=" text-uppercase">{{ $item->category_name }}</h2>
                                    <p>
                                        @if ($item->cost_one_msq >= 1)
                                            <b>Cost: </b> per 1 M<sup>2</sup> / Unit =
                                            {{ number_format($item->cost_one_msq, 2) }} {{ $item->type_of_currency }}
                                        @else
                                            <b>Cost: </b> Depends on Type of stand / Booth
                                        @endif
                                    </p>
                                    <p class="text p-1">{{ $item->category_descriptions }}</p>
                                    <a href="{{ route('exhibition_space_booking.show', ['exhibition_space_booking' => $item->category_id]) }}"
                                        class=" btn btn-primary btn-block text-white">View Spaces <i
                                            class=" fa fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Blog -->
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>
