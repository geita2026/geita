<!doctype html>

<head>
    <!-- Title -->
    <title>Expo2025</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
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
                        <h2>TANZANIA IN EXPO2025 OSAKA JAPAN</h2>
                    </div>
                    <br><br>
                   
                </div>
                <br><br>
                <div class="row">
                    <div class=" col-md-6">
                         <a href="{{ route('expo_registration') }}" class=" text-white" style="font-size: 20px">For Tanzania who want to participate in Expo2025 osaka japan <b>Click here to register</b> <i class=" fa fa-arrow-circle-right"></i></a>
                    </div>
                    
                    <div class=" col-md-6">
                        <a href="{{ route('join_tanzania') }}" class=" text-white" style="font-size: 20px">Companies interested in investing or collaborating with Tanzania at Expo 2025 in Osaka, Japan, <b>can register here.</b> <i class=" fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
    <div class=" card-body col-md-12 "> 

        
        <!--/ End Why choose -->
        <!-- Footer Area -->
        @include('FrontEnd.Public_Pages.Layouts.Footer')
        <!--/ End Footer Area -->
</body>

</html>
<script>
    const animatedDiv = document.getElementById('animatedDiv');

    animatedDiv.addEventListener('touchstart', () => {
        animatedDiv.classList.add('animate');
    });

    animatedDiv.addEventListener('animationend', () => {
        animatedDiv.classList.remove('animate');
    });
</script>

