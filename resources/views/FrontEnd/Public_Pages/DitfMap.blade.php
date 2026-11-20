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
    <title>DITF MAP</title>
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
                        <h2>Sabasaba Map</h2>
                        <ul class="bread-list">
                            <li><a href="{{ route('index') }}">Home</a></li>
                            <li><i class="icofont-simple-right"></i></li>
                            <li class="active">Sabasaba Map</li>
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
                        <h2>{{ $event->event_name }} (SABASABA) Map</h2>
                        <img src="{{ asset('images/ditflogo.jpg') }}" alt="#">
                        <p class=" text-justify">
                            <b>Mwl Nyerere International Fair Grounds </b> Is a large exhibition and trade fair venue
                            located in Dar es Salaam, Tanzania. It is named after Mwalimu Julius Kambarage Nyerere, the
                            first President of Tanzania, widely regarded as the founding father of the nation. The
                            fairgrounds are used for hosting various local and international events, including trade
                            fairs, exhibitions, conferences, and cultural festivals.
                        </p>
                        <p><b>Key Features and Functions of Dar es salaam International Trade Fair Ground:-</b></p>
                        <p class=" text-justify">
                            <b>Trade Exhibitions:</b> The venue is best known for hosting the annual Dar es Salaam
                            International Trade Fair (DITF), also referred to as the Sabasaba Trade Fair. This event
                            attracts exhibitors from Tanzania, Africa, and other international countries, showcasing a
                            variety of products, services, and innovations.<br>

                            <b>Cultural and Social Events:</b> Apart from trade shows, the fairgrounds are also used for
                            cultural exhibitions, food fairs, and social events, making it a hub for networking,
                            business development, and cultural exchange.<br>

                            <b>Business Hub:</b> The fairgrounds provide opportunities for businesses, especially SMEs (Small
                            and Medium-sized Enterprises), to showcase their products and connect with larger markets.
                            It plays a crucial role in promoting commerce, tourism, and investment in Tanzania.<br>

                            <b>Strategic Location:</b> Situated in the country's largest city and commercial capital, Dar es
                            Salaam, the venue is easily accessible for both local and international visitors. Its
                            location near the port and other major transport hubs makes it a strategic site for
                            international trade activities.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-12">
                    <!-- Start Choose Left -->
                    <div class="choose-left">
                        <h3>Mwl. Nyerere International Trade Fair Ground [Sabasaba]</h3>
                        <p style="text-align: justify;font-size:2vmin">
                            The Mwalimu J.K.Nyerere Trade Fair Grounds,It is located along Kilwa Road, 8 km
                            south east of Dar es Salaam in Tanzania.
                            The Trade Fair Grounds which is also known as Mwl. J.K.Nyerere Trade Fair Grounds
                            were inaugurated in 1962, one year after Tanzania gained her independence on 9
                            December 1961. <a href="#" class=" text-info">View in Google Map <i
                                    class=" icofont icofont-location-pin"></i></a>
                        </p>
                        <p style="text-align: center">
                            <img src="{{ asset('images/DITFMAP.png') }}" style="width: 100%; height:90%; width:90%">
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
