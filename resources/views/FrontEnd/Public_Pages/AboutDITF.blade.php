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
    <title>Exhibitor List</title>
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
                        <h2>About sabasaba</h2>
                        <ul class="bread-list">
                            <li><a href="{{ route('index') }}">Home</a></li>
                            <li><i class="icofont-simple-right"></i></li>
                            <li class="active">About sabasaba</li>
                        </ul>
                    </div>
                </div>
                <br><br>

                
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->


    <!-- Start Why choose -->
		<section class="why-choose section" >
			<div class="card-body">
				<div class="row">
					<div class="col-lg-12">
						<div class="section-title">
							<h2>{{ $event->event_name }} (SABASABA)</h2>
							<img src="{{ asset('images/ditflogo.jpg') }}" alt="#">
                           
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6 col-12">
						<!-- Start Choose Left -->
						<div class="choose-left">
							<h3>Who We Are</h3>
                            <p style="text-align: justify;">
                                The Dar es Salaam International Trade Fair (DITF) also known as <q>SabaSaba</q>
                                takes
                                place annually on the 28<sup>th</sup> of June at the <a
                                    href="{{ route('ditf_map') }}">Mwalimu J.K.Nyerere Trade Fair Grounds</a>.
                                It is located along Kilwa Road, 8 km south east of Dar es Salaam in Tanzania.
                                The Dar es Salaam International Trade Fair is an annual major promotional event
                                organised by <a href="https://www.tantrade.go.tz/" target="_newtab">Tanzania
                                    Trade Development Authority (TanTrade)</a>. Tanzania Trade Development
                                Authority (TanTrade) is a
                                government institution, which was established under the Act No. 4 of 2009 that
                                repealed the Board of External Trade Act No. 5 of 1978 and the Board of Internal
                                Trade Act No. 15 of 1973. The first Trade fair was held in 1963. It was organised by an expert from UKin the
                                Ministry of Trade and Cooperative Unions by the name Mr. Lucas, with the assistance
                                of Mr. Mashamba who was an official of the Ministry.
                            </p>

                            <p>
                                The Tanzania Trade Development Authority (TanTrade), dedicated to establishing global business
                                partnership through organizing and managing international and specialised trade
                                fairs, solo exhibitions, product and market research, prospect development, Trade
                                missions, buyer-seller meetings and contact marketing programmes. TanTrade gives out
                                regular trade information and offers consultancy services to producers, exporters
                                and importers to enable them to effectively participate in the global market place.
                            </p>
                            <p>
                                The Dar es Salaam International Trade Fair has established itself over the years
                                as the
                                shop window for Tanzanian products as well as the East, Central, and the
                                Southern
                                African Region. Supported by the services of the Dar es Salaam harbor which
                                serves
                                the region effectively, the fair acts as a one-stop center for reaching
                                countries such
                                as Kenya, Uganda, Rwanda, Burundi, Democratic Republic of Congo, Zambia, Malawi,
                                Zimbabwe and Botswana.

                                The fairs enjoy patronage of the Tanzanian business community both exhibit and
                                use it as a forum for business exchange.

                                Participation has ever been on the increase from a mere over 100 companies in
                                the
                                the late eighties to over 1,041 companies in 1999, In 2006 a total of 1,526
                                exhibitors
                                exhibited at the fair and came from overseas representing over 26 countries,
                                Tanzania inclusive. Currently, The exhibition is the largest exhibition in
                                Africa that collects
                                more than 3,500 local companies, 200 foreign companies, and 26 countries have
                                attended the 2023 exhibition.
                            </p>
                            
							
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
										<div class="waves wave-1"></div>
										<div class="waves wave-2"></div>
										<div class="waves wave-3"></div>
									</div>
								</div>
								<!--/ End Video Animation -->
								<a href="https://www.youtube.com/watch?v=RFVXy6CRVR4" class="video video-popup mfp-iframe"><i class="fa fa-play"></i></a>
							</div>
						</div>
						<!-- End Choose Rights -->
					</div>
				</div>
			</div>
		</section>
		<!--/ End Why choose -->

        <div class=" card">
            <div class=" card-header">
                <h4 class=" text-uppercase text-center"><b class=" text-primary">{{ $event->event_name }}</b> EXHIBITOR SECTORS / CATEGORIES {{ date('Y') }}</h4>
            </div>
            <div class=" card-body">
                <div class="row">

                    @foreach ($sectors as $item)
                    <div class="col-lg-6">
                        <ul class="list">
                            <style>
                                .borderBellow:hover{
                                    border-bottom: 1px solid blue;
                                    color: blue;
                                }
                            </style>
                            <li title=" Click to view exhibitors under this category"><i class="fa fa-caret-right"></i><a href="{{ route('discover_exhibitor.edit',['discover_exhibitor'=>$item->sectorID]) }}" class="  borderBellow">{{ $item->sectorName }}</a></li>
                        </ul>
                    </div>
                        @endforeach
                </div>
            </div>
        </div>

    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>
