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
    <title>{{ env('APP_NAME') }} | Available Spaces</title>
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
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('images/Exhibitor2.jpeg') }})">
        <div class="container">
            <div class="bread-inner">

                <div class="row">
                    <div class="col-12">
                        <h2>{{ $category->category_name }}</h2>
                        <ul class="bread-list">
                            <li><a href="{{ route('index') }}">Home</a></li>
                            <li><i class="icofont-simple-right"></i></li>
                            <li class="active">available {{ $category->category_name }}</li>
                        </ul>
                    </div>
                </div>


                <br><br>


            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->


    <section class="blog section" id="blog">
        <div class="card-body">
            <div class=" col-12">
                Select Other from list
            </div>
            <div class=" row text-center">
                @foreach ($categories as $data)
                    <a href="{{ route('online_booking.show', ['online_booking' => $data->space_category_id]) }}"
                        class=" btn btn-primary text-white ml-1">{{ $data->category_name }}</a>
                @endforeach
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2>Available {{ $category->category_name }} for Booking</h2>
                        <img src="{{ $category->categoryPoster ?? asset('images/Logo.png') }}" height="100"
                            width="200">
                        <p style=" text-align:left">
                            {{ $category->category_descriptions }}
                        </p>
                        <p>
                        <form action="{{ route('online_booking.create') }}" method="POST">
                            @csrf
                            @method('GET')
                            <div class=" row">

                                <div class=" col-md-12 ">
                                    <h5 class="text-bold text-primary">Check Space Availability for your Event Dates
                                        before start Booking!</h5>
                                </div>
                                <input type="hidden" name="spaceCategory" value="{{ $category->space_category_id }}">
                                <div class=" col-md-5">
                                    <label>Start Date<i class="text-danger">*</i></label>
                                    <input type="date" required name="start_date" class=" form-control">
                                </div>
                                <div class=" col-md-5">
                                    <label>End Date<i class="text-danger">*</i></label>
                                    <input type="date" required name="end_date" class=" form-control">
                                </div>
                                <div class=" col-md-2 text-right">
                                    <label>.</label>
                                    <button type="submit" class=" btn btn-rounded btn-primary">Check
                                        Availablity</button>
                                </div>
                            </div>
                        </form>
                        </p>

                    </div>
                </div>
            </div>

            <div class="row">
                @php $i=0; @endphp
                @foreach ($spaces as $item)
                    <div class="col-lg-4 col-md-6 col-12 zoom">
                        <!-- Single Blog -->
                        <div class="single-news">
                            <div class="news-head">
                                <img src="{{ $item->spaceImage ?? asset('images/logo.jpg') }}" alt="#">
                            </div>
                            <div class="news-body">
                                <div class="news-content">
                                    <div class="date">Cost: {{ number_format($item->space_cost, 2) }} TZS</div>
                                    <h2><a href="blog-single.html">{{ $item->space_name }}</a></h2>
                                    <p class="text">{{ $item->space_descriptions }}</p>
                                    @php
                                        $i = 0;
                                    @endphp
                                    @foreach ($bookedSpace as $data)
                                        @if ($data->space_id == $item->space_id)
                                            <label class=" btn bg-danger btn-block">Booked From {{ date('M d, Y',strtotime($data->start_date)) }}
                                                <i class=" fa fa-arrow-right"></i> {{ date('M d, Y',strtotime($data->end_date)) }}</label>
                                            @php
                                                $i++;
                                            @endphp
                                        @endif
                                    @endforeach

                                    @if ($i == 0)
                                        @auth
                                            <a href="{{ route('off-fair-space-booking.show', ['off_fair_space_booking' => $item->space_id]) }}"
                                                class=" btn bg-success btn-block text-white">Book Now</a>
                                        @else
                                            <a href="{{ route('off-fair-space-booking.show', ['off_fair_space_booking' => $item->space_id]) }}"
                                                class=" btn bg-success btn-block text-white">Login To Book</a><br><br>
                                            Don't Have account ?<a href="{{ route('registration') }}"
                                                class=" text-primary">
                                                Create Account</a>
                                        @endauth
                                    @endif

                                </div>
                            </div>
                        </div>
                        <!-- End Single Blog -->
                    </div>
                    @php $i++; @endphp
                @endforeach
                @if ($i == 0)
                    <div class=" alert alert-info">
                        All Space under <b>{{ $category->category_name }}</b> category, have been booked please select
                        another category from this !
                        @foreach ($categories as $data)
                            <a href="{{ route('online_booking.show', ['online_booking' => $data->space_category_id]) }}"
                                class="ml-1 text-primary"><b>{{ $data->category_name }}</b></a>,
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </section>

    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>
