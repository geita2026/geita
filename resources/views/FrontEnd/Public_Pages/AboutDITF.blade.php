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
    <title>About the exhibition | GeMEMIS</title>
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
                        <h2>About the exhibition</h2>
                        <ul class="bread-list">
                            <li><a href="{{ route('index') }}">Home</a></li>
                            <li><i class="icofont-simple-right"></i></li>
                            <li class="active">About the exhibition</li>
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
							<h2>{{ $event->event_name }}</h2>
							<img src="{{ asset('images/coat-of-arms.svg') }}" alt="#">
                           
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6 col-12">
						<!-- Start Choose Left -->
						<div class="choose-left">
							<h3>Who We Are</h3>
                            <p style="text-align: justify;">
                                The Geita Mineral Exhibition brings Tanzania&rsquo;s mining sector together in Geita,
                                in the north-west of the country on the southern shore of Lake Victoria &mdash; the
                                heart of the Lake Victoria goldfields and home to one of the largest gold mines in
                                Africa. For several days the exhibition grounds become a single marketplace for the
                                people who dig, process, finance, equip and regulate the minerals that come out of
                                this ground.
                            </p>

                            <p style="text-align: justify;">
                                Exhibitors come from every part of the value chain: small-scale miners and their
                                associations, licensed mineral dealers and processors, equipment and consumables
                                suppliers, assay and laboratory services, banks and financial institutions,
                                insurers, training providers and the government agencies that license and regulate
                                the sector. Alongside them are women entrepreneurs and local businesses from Geita
                                and the neighbouring districts, for whom the exhibition is a chance to reach buyers
                                they would otherwise never meet.
                            </p>

                            <p style="text-align: justify;">
                                The exhibition exists to move the sector towards value addition &mdash; keeping more
                                of the worth of Tanzania&rsquo;s minerals in Tanzania through processing, refining and
                                local supply &mdash; and to put technical knowledge, finance and fair prices within
                                reach of the smallest operator on the ground.
                            </p>

                            <p style="text-align: justify;">
                                GeMEMIS, the Geita Mineral Exhibition Management Information System, is how the
                                exhibition runs. Exhibitors register, apply, take their space on the floor plan,
                                settle the invoice and check in. The plan you see is the grounds as they stand.
                            </p>
                            
							
						</div>
						<!-- End Choose Left -->
					</div>
					<div class="col-lg-6 col-12">
						<!-- Start Choose Rights -->
						<div class="choose-right">
							<div class="video-image">
								<img src="{{ asset('images/geita/geita-mine.jpg') }}" alt="The open pit at Geita, in the Lake Victoria goldfields" style="width:100%;height:auto;border-radius:12px;">
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
