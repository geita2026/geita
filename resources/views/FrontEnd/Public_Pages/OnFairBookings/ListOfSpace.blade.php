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
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('images/Exhibitor2.jpeg') }})">
        <div class="container">
            <div class="bread-inner">

                <div class="row">
                    <div class="col-12">
                        <h2 class=" text-uppercase">AVAILABLE {{ $category->category_name }}</h2>
                        <ul class="bread-list">
                            <li><a href="#"> {{ $category->category_descriptions }}</a></li>

                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <div class=" col-md-12">
        <section class="blog section" id="blog">
            <div class="card-body">
                <div class="row">
                    @php $b=0; @endphp
                    @foreach ($spaces as $item)
                        <div class="col-lg-4 col-md-6 col-12 ">
                            <!-- Single Blog -->
                            <div class="single-news">

                                <div class="news-body">
                                    <div class="news-content">

                                        {{-- Check for spaces with booths --}}
                                        @php
                                            $a = 0; // For booked booth of whole space
                                        @endphp
                                        @foreach ($bookedBooths as $booth)
                                            @if ($booth->pavilionID == $item->pavilionID)
                                                @php $a++; @endphp
                                            @endif
                                        @endforeach
                                        {{-- End checking --}}
                                        @php
                                            $i = 0;
                                        @endphp
                                        @foreach ($bookedPavilions as $status)
                                            @if ($status->pavilionID == $item->pavilionID)
                                                @php $i++; @endphp
                                            @endif
                                        @endforeach

                                       


                                        <h2><a href="#">{{ $item->pavilionName }}</a></h2>
                                        <div>
                                            @if ($item->cost_one_msq != 0)
                                                Cost per 1 M<sup>2</sup> / Unit =
                                                {{ number_format($item->cost_one_msq, 2) }}
                                                {{ $item->type_of_currency }}
                                            @else
                                               <b>Cost:</b> Depends on type of Stand.
                                            @endif
                                        </div>
                                        <p class="text">{{ $item->space_descriptions }}</p>

                                        @if ($item->numberOfPartitions > 0)
                                        @if ($item->numberOfPartitions == $a)
                                        <label class=" btn btn-danger">The pavilion is Full booked 100%</label>
                                        @else
                                            @if ($i == 0)
                                                <a href="{{ route('booking.show', ['booking' => $item->pavilionID]) }}"
                                                    class=" btn bg-success btn-block text-white">Login To
                                                    Book, {{ number_format((100-($a/$item->numberOfPartitions*100))) }}% of pavilion stands left</a>
                                            @else
                                                Space is already Booked
                                            @endif
                                        @endif
                                    @else
                                        @if ($i == 0)
                                            <a href="{{ route('booking.show', ['booking' => $item->pavilionID]) }}"
                                                class=" btn bg-success btn-block text-white">Login To
                                                Book</a>
                                        @else
                                            
                                                <label class=" btn btn-danger">Space is already booked</label>
                                            
                                        @endif
                                    @endif

                                    </div>
                                </div>
                            </div>
                            <!-- End Single Blog -->
                        </div>
                        @php $i++; @endphp
                    @endforeach
                    @if ($i == 0)
                        <div class=" alert alert-warning">
                            No Space Published under <b>{{ $category->category_name }}</b> category, please select
                            other
                            category from the list
                            @foreach ($spaceCategories as $data)
                                <a href="{{ route('exhibition_space_booking.show', ['exhibition_space_booking' => $data->category_id]) }}"
                                    class="ml-1 text-primary"><b>{{ $data->category_name }}</b></a>,
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </section>
    </div>

    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>
