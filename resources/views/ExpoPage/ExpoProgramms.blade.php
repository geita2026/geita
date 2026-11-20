<!doctype html>

<head>
    <!-- Title -->
    <title>Expo2025</title>
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
    <!-- Header Area -->
    <header class="header">
        <!-- Header Inner -->
        @include('ExpoPage.Layouts.ExpoHeader')
        <!--/ End Header Inner -->
    </header>
    <!-- End Header Area -->

    <!-- Breadcrumbs -->
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('ExpoFolder/Expobackground.png') }})">
        <div class="container">
            <div class="bread-inner">

                <div class="row">
                    <div class="col-12">
                        <h2>EXPO2025 OSAKA JAPAN</h2>
                    </div>
                </div>
                <br><br>


            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <br><br>
    <div class=" card-body col-md-12">
        <div class="col-lg-12">




            <div class="row">
                <div class="col-lg-4 col-12">
                    <img src="{{ asset('ExpoFolder/map-base-center.png') }}" style="height: 99%;width:100%">
                </div>

                <div class="col-lg-8 col-12">
                    <h3>KUHUSU EXPO</h3>
                    <p class=" text-justify">
                        <b>EXPO</b> Ni Maonesho makubwa ya Dunia ambayo yanalenga kuunganisha Dunia pamoja ili kutafuta
                        ufumbuzi
                        wa changamoto zinazoikabili dunia (solutions to universal challenges of our time).
                        Changamoto hizo huainishwa na Kauli mbiu ya <b>EXPO</b> husika na Dunia nzima huungana na
                        Waandaaji wa
                        <b>EXPO</b>
                        kutafuta/kutoa ufumbuzi kwa mfumo wa Maonesho.<br> <b>EXPO</b> hufanyika kwa muda wa Miezi
                        mitatu (3) hadi sita (6) na huratibiwa na Shirikisho la Maonesho
                        Duniani (Bureau of International Exposition-BIE)
                    </p>


                    <p>
                    <h3 class=" text-uppercase">
                        Kuchagua nchi itakayokuwa mwenyeji wa EXPO
                    </h3>
                    </p>
                    <p class=" text-justify">
                        Nchi Wanachama wa BIE hupiga kura na kuchagua nchi ambayo itakuwa mwenyeji.
                        <b>TanTrade</b> hupiga kura kwa niaba ya Jamhuri ya Muungano wa Tanzania kutokana na kuwa
                        mwanachama wa BIE.<br>
                        Mwaka 2020 nchi ya Japan ilipata kura nyingi na kutangazwa kuwa mwenyeji wa Expo 2025 Osaka,
                        Japan.
                    </p>
                    <p class=" text-justify">
                        <b>Tanzania</b> imekuwa ikishiriki katika EXPO toka mwaka 1967
                        <b>BET/TanTrade</b> imefanikiwa kuratibu ushiriki wa Tanzania katika EXPO mbalimbali:-<br>
                        <div class=" row">
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 1967 Montreal, Canada
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 1992 Seville, Spain
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 1993 Taejoin, Korea Kusini
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 1998 Lisbon, Portugal
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 1999 Kunming, China
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 2000 Hannover, Germany
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 2003 Rostock, Germany
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 2005 Aichi, Japan
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 2008 Zaragoza, Spain
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 2010 Shanghai, China
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 2012 Yeuso, Korea
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 2015 Milan, Italia
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 2020 Dubai, United Arab Emirates
                        </div>
                        <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 2023 Doha, Qatar
                        </div>
                         <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 2025 Osaka, Japan- Maandalizi yanaendelea
                         </div>
                         <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 2027 Belgrade, Serbia
                         </div>
                         <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> Expo 2027 Yokohama, Japan
                         </div>
                         <div class=" col-md-3">
                            <i class=" icofont-arrow-right"></i> EXPO 2030 Riyadh, Saudi Arabia
                         </div>
                        </div>
                    </p>
                </div>

            </div>

            <!--/ End Why choose -->
            <!-- Footer Area -->
            @include('FrontEnd.Public_Pages.Layouts.Footer')
            <!--/ End Footer Area -->
</body>

</html>
