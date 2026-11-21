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
    <title>Event permits | GeMEMIS</title>
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
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('ePermitFiles/ePermit.jpeg') }})">
        <div class="container">
            <div class="bread-inner">

                <div class="row">
                    <div class="col-12">
                        <h2>Trade Promotion Event Permits</h2>
                        <ul class="bread-list">
                            <li><a href="{{ route('/') }}">Home</a></li>
                            <li><i class="icofont-simple-right"></i></li>
                            <li class="active">Trade Promotion Event Permits</li>
                        </ul>
                    </div>
                </div>
                <br><br>


            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->



    <!-- Start Blog Area -->
    <section class="blog section" id="blog">
        <div class="card-body">
            <div class="row">

                <div class="col-lg-12">
                    <div class="section-title">
                        <h2><a href="#">Trade Promotion Event Permit</a> <i class=" fa fa-star-o"></i> <a class=" text-primary" href="{{ route('permitDashboard') }}">Apply Now</a></h2>
                        <p class='text-justify'>
                            is an official authorization required by businesses or event organizers to legally host or
                            participate / Organize trade promotion events, such as trade shows, exhibitions, fairs, or
                            sales
                            promotions. The permit ensures that the event complies with local regulations, including
                            safety, business conduct, and consumer protection laws.
                        </p>
                        <p class=" text-justify">
                            This might be an International Promotion Events (This includes Foreigners Only or Both
                            Foreigner and Local Participants) or Local Trade Promotion Event (This includes Local
                            Participants Only)
                        </p>
                        
                        <p class="text-justify">
                            You are required to hold a permit from the Exhibition Secretariat in order to organise the following
                            Event;
                        </p>
                    </div>
                </div>
            </div>
        
            <div class="row">
                <br>
                @foreach ($eventTypes as $item)
                    
                        <div class="col-lg-4 col-md-6 col-12">
                            <!-- Single Blog -->
                            <div class="single-news">
                                <div class="news-head">
                                    <img src="{{ $item->eventTypePoster }}" alt="#">
                                </div>
                                <div class="news-body">
                                    <div class="news-content">
                                        <a href="#">
                                            <h2>{{ $item->promotionEventTypeName }}</h2>
                                            <p class="text">
                                                {{ $item->promotionEventTypeDescription }} 
                                            </p>
                                        </a>
                                        <br>
                                        @auth()
                                        <a href="{{ route('permitDashboard') }}" class=" btn btn-block btn-primary text-white">Apply Now</a>
                                        @else
                                        <a href="{{ route('permitDashboard') }}" class=" btn btn-block btn-primary text-white">Login to Apply</a><br><br>
                                        Don't Have Account ? <a href="{{ route('registration') }}" class=" text-center text-primary"> Create Account</a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                            <!-- End Single Blog -->
                        </div>
                    
                @endforeach 
            </div>
            <br><br>
            <p class=" col-md-12 form-group" >
              <b class=" text-danger">NOTE!</b>  Section 5(1) of Tanzania Trade Development Authority Act 2009. Require every orgnizer of any
                        Kind of Trade Promotion Event as listed above To have Permit issued by
                        Tanzania Trade Development Authority in order to ensure that trade-related events, such as
                        exhibitions, fairs, trade shows, and sales promotions, are conducted legally, safely, and in
                        compliance with local and International laws and regulations.
            </p>
        </div>
    </section>
    <!-- End Blog Area -->
    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>
