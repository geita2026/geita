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
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('images/geita/geita-town.jpg') }})">
        <div class="container">
            <div class="bread-inner">
                <div class="row">
                    <div class="col-12">
                        <h2 class=" text-uppercase">Sponsorship Package Groups</h2>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <section class=" col-md-12">
        <div class=" card-body">
            <div class="row" style="padding: 3%">
                <div class=" col-md-12">
                   The Geita Mineral Exhibition brings together the whole of Tanzania&rsquo;s mining value chain &mdash; small-scale miners, dealers and processors, equipment suppliers, financiers and the agencies that regulate the sector. Sponsorship is what makes the exhibition possible, and it puts your brand in front of the people who decide where minerals are bought, processed and financed. Each package group below sets out what the sponsorship covers and where your brand appears across the grounds and the programme.
                    <br> 
                    <h4> What’s in it for you?</h4>

                    <b>Brand Visibility:</b> Brand Visibility: Showcase your brand to thousands of visitors, media outlets and exhibitors
                    <b> Networking Opportunities:</b> Engage with potential customers, partners and industry stakeholders.
                    <b> Market Insights:</b> Gain valuable insights into market trends and customer preferences. 
                    <b> Community Impact:</b> Contribute to the economic growth and development of your Nation
                    <br><br>
                    <h5> Select a package group below suitable for your corporate visibility</h5>
                </div>
                @forelse($spaceCategories as $item)
                {{-- @foreach ($spaceCategories as $item) --}}
                    <div class="col-lg-12 col-md-6 col-12 zoom ml-3 mt-3"
                        style="background-color: {{ $item->backgroundColor }};color:{{ $item->textColor }}">
                        <!-- Single Blog -->
                        <div class="single-news">
                            <div class="news-body p-15">
                                <div class="news-content">
                                    <a href="{{ route('sponsorship_package_list', ['packageId' => $item->packageGroupId]) }}"
                                        class=" " style="color: {{ $item->textColor }}">
                                        <h1 class=" text-uppercase">{{ $item->packageGroupName }}</h1>
                                        <div class="p-1 text-justify">{{ $item->packageGroupDescription }} <b
                                                class="text-white btn">View packages . . . <i
                                                    class=" fa fa-arrow-circle-right"></i></b>
                                        </div>

                                    </a>
                                </div>
                                <br>
                            </div>
                        </div>
                        <!-- End Single Blog -->
                    </div>
                {{-- @endforeach --}}
                @empty
                <p>No records found.</p>
                @endforelse
            </div>
            <br><br>
        </div>
    </section>

    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>
