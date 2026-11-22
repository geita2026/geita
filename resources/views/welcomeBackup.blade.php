<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="keywords" content="GeMEMIS, Geita, Digital Management, Exhibitions, Trade, Mineral Products">
    <meta name="description" content="GeMEMIS - Geita Digital Management and Information System">

    <title>GeMEMIS | Digital Management Platform</title>

    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')

    <style>
        :root {
            --gold: #c9a227;
            --gold-light: #e5ca72;
            --black: #090909;
            --dark: #151515;
            --text: #292929;
            --muted: #707070;
            --light: #f7f7f7;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
            background: var(--white);
        }

        a {
            transition: .3s ease;
        }

        /* HERO */
        .hero {
            min-height: 650px;
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
            background: var(--black);
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            transform: scale(1.02);
            transition: background-image 1s ease-in-out;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                100deg,
                rgba(0, 0, 0, .94),
                rgba(0, 0, 0, .78),
                rgba(0, 0, 0, .40)
            );
            border-bottom: 5px solid var(--gold);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1200px;
            padding: 100px 25px;
            margin: auto;
            color: var(--white);
        }

        .brand-label {
            display: inline-block;
            background: var(--gold);
            color: var(--black);
            padding: 11px 22px;
            border-radius: 3px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 24px;
        }

        .hero-content h1 {
            font-size: clamp(38px, 5vw, 62px);
            font-weight: 800;
            max-width: 850px;
            line-height: 1.15;
            margin-bottom: 25px;
            color: var(--white);
        }

        .hero-content p {
            font-size: 18px;
            line-height: 1.8;
            max-width: 720px;
            color: #eeeeee;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        /* BUTTONS */
        .btn-main {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            background: var(--gold);
            color: var(--black) !important;
            padding: 15px 27px;
            border: 2px solid var(--gold);
            border-radius: 5px;
            text-decoration: none !important;
            font-weight: 700;
            transition: .3s ease;
        }

        .btn-main:hover {
            background: var(--gold-light);
            border-color: var(--gold-light);
            color: var(--black) !important;
            transform: translateY(-3px);
        }

        .btn-outline {
            background: transparent;
            color: var(--white) !important;
            border: 2px solid var(--gold);
        }

        .btn-outline:hover {
            background: var(--gold);
            color: var(--black) !important;
        }

        .btn-login {
            background: var(--black);
            color: var(--gold) !important;
            border: 2px solid var(--gold);
            padding: 15px 32px;
        }

        .btn-login:hover {
            background: var(--gold);
            color: var(--black) !important;
        }

        /* SECTION */
        .section {
            padding: 85px 20px;
        }

        .section-light {
            background: var(--light);
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            color: var(--black);
            font-size: 34px;
            font-weight: 800;
            padding-bottom: 17px;
        }

        .section-title h2:after {
            content: "";
            display: block;
            width: 75px;
            height: 4px;
            background: var(--gold);
            margin: 15px auto 0;
        }

        .section-title p {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        /* SERVICE CARDS */
        .service-link {
            display: block;
            height: 100%;
            text-decoration: none !important;
        }

        .service-card {
            background: var(--white);
            padding: 38px 28px;
            height: 100%;
            min-height: 260px;
            border-radius: 9px;
            box-shadow: 0 8px 30px rgba(0,0,0,.07);
            border-bottom: 4px solid var(--gold);
            transition: .3s ease;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,.14);
        }

        .service-card i {
            display: inline-block;
            font-size: 40px;
            color: var(--gold);
            margin-bottom: 22px;
        }

        .service-card h4 {
            color: var(--black);
            font-weight: 700;
            margin-bottom: 15px;
        }

        .service-card p {
            color: #657386;
            line-height: 1.8;
            margin-bottom: 0;
        }

        .service-more {
            display: inline-block;
            color: #9b7812;
            font-weight: 700;
            margin-top: 20px;
        }

        /* INFORMATION BANNER */
        .information-banner {
            background: linear-gradient(120deg, #090909, #262626);
            color: var(--white);
            border-top: 4px solid var(--gold);
            border-bottom: 4px solid var(--gold);
            text-align: center;
            padding: 65px 20px;
        }

        .information-banner h2 {
            color: var(--gold);
            font-weight: 800;
            margin-bottom: 18px;
        }

        .information-banner p {
            color: #eeeeee;
            max-width: 780px;
            margin: 0 auto 25px;
            line-height: 1.8;
        }

        /* SPONSORS */
        .sponsor {
            margin-bottom: 20px;
        }

        .sponsor img {
            background: var(--white);
            padding: 12px;
            border: 1px solid #e4e4e4;
            border-bottom: 3px solid var(--gold);
            border-radius: 6px;
            max-height: 90px;
            max-width: 100%;
            object-fit: contain;
            transition: .3s;
        }

        .sponsor img:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,.12);
        }

        /* RESPONSIVE */
        @media(max-width: 768px) {
            .hero {
                min-height: 570px;
            }

            .hero-content {
                padding: 70px 22px;
            }

            .hero-content h1 {
                font-size: 38px;
            }

            .hero-content p {
                font-size: 16px;
            }

            .section {
                padding: 60px 15px;
            }

            .section-title h2 {
                font-size: 28px;
            }

            .hero-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .hero-actions a {
                width: 100%;
            }

            .service-card {
                min-height: auto;
            }
        }
    </style>
</head>

<body>



<!-- HERO SECTION -->
<section class="hero">

    <div class="hero-bg" id="heroBg"></div>
    <div class="hero-overlay"></div>

    <div class="hero-content">

        <div class="brand-label">
            GEMEMIS DIGITAL PLATFORM
        </div>

        <h1>
            Welcome to GeMEMIS
        </h1>

        <p>
            Geita Digital Management and Information System.
            An integrated digital platform designed to improve
            information access, exhibition services, mineral product
            visibility and efficient service delivery.
        </p>

        <div class="hero-actions">

            <a href="{{ route('login') }}" class="btn-main btn-login">
                <i class="fa fa-sign-in"></i>
                Login to GeMEMIS
            </a>

            <a href=" " class="btn-main">
                <i class="fa fa-calendar"></i>
                Explore Digital Services
            </a>

            <a href="" class="btn-main btn-outline">
                <i class="fa fa-info-circle"></i>
                Learn More
            </a>

        </div>

        <p style="margin-top:30px;font-size:14px;">
            A digital platform supporting accessible information
            and efficient exhibition services.
        </p>

    </div>
</section>

<!-- DIGITAL SERVICES -->
<section class="section section-light">

    <div class="container">

        <div class="section-title">
            <h2>Our Digital Services</h2>
            
        </div>

        <div class="row g-4">

            <!-- EXHIBITION SPACE BOOKING -->
            <div class="col-lg-4 col-md-6">
                
            </div>

            <!-- MINERAL PRODUCTS -->
            <div class="col-lg-4 col-md-6">
                
            </div>

            <!-- INFORMATION CENTRE -->
            <div class="col-lg-4 col-md-6">
                
            </div>

            <!-- EXHIBITION INFORMATION -->
            <div class="col-lg-4 col-md-6">
                
                 
            </div>

            <!-- EXHIBITORS -->
            <div class="col-lg-4 col-md-6">
               
            </div>

            <!-- PROGRAMMES -->
            <div class="col-lg-4 col-md-6">
                
            </div>

        </div>

    </div>
</section>

<!-- LOGIN INFORMATION BANNER -->
<section class="information-banner">

    <div class="container">

        <h2>Access Your GeMEMIS Account</h2>

        <p>
            Log in to access the digital services available to your account.
            Registered users can continue to the platform through the secure
            login page.
        </p>

        <a href="{{ route('login') }}" class="btn-main">
            <i class="fa fa-lock"></i>
            Login Now
        </a>

        <a href="{{ route('registration') }}" class="btn-main btn-outline">
            <i class="fa fa-user-plus"></i>
            Create Account
        </a>

    </div>

</section>

<!-- SPONSORS AND PARTNERS -->
<section class="section">

    <div class="container">

        <div class="section-title">

            <h2>Our Sponsors and Partners</h2>

            <p>
                Recognising the sponsors and institutional partners
                supporting our activities.
            </p>

        </div>

        <div class="row g-4 justify-content-center">

            @foreach($sponsors as $value)

                <div class="col-lg-2 col-md-3 col-6 sponsor text-center">

                    <img
                        src="{{ asset('VirtualImages/'.$value->sponsor_logo) }}"
                        alt="Sponsor Logo"
                        loading="lazy"
                    >

                </div>

            @endforeach

        </div>

    </div>

</section>

@include('FrontEnd.Public_Pages.Layouts.Footer')

<!-- HERO SLIDING IMAGES -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const images = @json($slidings->pluck('slidingImage')->values());

        let currentIndex = 0;
        const heroBg = document.getElementById('heroBg');

        if (heroBg && images.length > 0) {

            function showImage() {
                heroBg.style.backgroundImage =
                    'url("' + images[currentIndex] + '")';
            }

            showImage();

            if (images.length > 1) {
                setInterval(function () {
                    currentIndex = (currentIndex + 1) % images.length;
                    showImage();
                }, 6000);
            }
        }
    });
</script>

</body>
</html>
