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
                        <h2>Online Space Booking</h2>
                        <ul class="bread-list">
                            <li><a href="{{ route('index') }}">Home</a></li>
                            <li><i class="icofont-simple-right"></i></li>
                            <li class="active">Online Space Booking</li>
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
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2>The Geita Mineral Exhibition Grounds</h2>
                        <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Court of Arms of the United Republic of Tanzania" height="110">
                        <p style=" text-align:left">
                            The Geita Mineral Exhibition Grounds are a multi-purpose facility in Geita, Geita Region,
                            on the southern shore of Lake Victoria. They host the mineral exhibitions and the events
                            around them: trade and equipment exhibitions, buyer&ndash;seller meetings, technical
                            demonstrations, conferences and workshops, training for small-scale miners, and the
                            official programme. Halls, covered stands and open outdoor plots are booked through
                            GeMEMIS, and what you see on the floor plan is what is actually free.

                        </p>

                    </div>
                </div>
            </div>

            <div class=" col-md-12 p-10">
                <h1>List of Spaces Categories Available for Booking</h1>
            </div> <br><br>

            <div class="row">
                @foreach ($spaceCategories as $item)
                <div class="col-lg-4 col-md-6 col-12 zoom">
                    <!-- Single Blog -->
                    <div class="single-news">
                        <div class="news-head">
                            <img src="{{ $item->categoryPoster ?? asset('images/logo.jpg') }}" alt="#">
                        </div>
                        <div class="news-body">
                            <div class="news-content">
                                <div class="date">{{ $item->populationLimit ?? 'Null' }}</div>
                                <h2><a href="blog-single.html">{{ $item->category_name }}</a></h2>
                                <p class="text">{{ $item->category_descriptions }}</p>
                                <a href="{{ route('online_booking.show',['online_booking'=>$item->space_category_id]) }}" class=" btn btn-primary btn-block text-white">View Spaces <i class=" fa fa-arrow-right"></i></a>
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
