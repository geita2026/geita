<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="keywords" content="GeMEMIS, Geita, Digital Management, Exhibitions, Trade">
    <meta name="description" content="GeMEMIS Digital Management Platform">

    <title>GeMEMIS | Digital Management Platform</title>

    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')

    <style>
        :root {
            --blue: #102b50;
            --green: #159447;
            --dark: #091a30;
            --light: #f4f7fb;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #263449;
            background: var(--white);
        }

        .hero {
            min-height: 620px;
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
            background: var(--blue);
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background-position: center;
            background-size: cover;
            transition: background-image 1s ease-in-out;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                100deg,
                rgba(5, 22, 45, .94),
                rgba(10, 39, 69, .78),
                rgba(10, 39, 69, .30)
            );
        }

        .hero-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1200px;
            padding: 90px 25px;
            margin: auto;
            color: white;
        }

        .hero-content h1 {
            font-size: clamp(36px, 5vw, 58px);
            font-weight: 800;
            max-width: 850px;
            line-height: 1.2;
            margin-bottom: 25px;
        }

        .hero-content p {
            font-size: 19px;
            line-height: 1.8;
            max-width: 700px;
            color: #e5edf7;
        }

        .brand-label {
            display: inline-block;
            background: var(--green);
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 22px;
        }

        .btn-main {
            display: inline-block;
            background: var(--green);
            color: white !important;
            padding: 15px 28px;
            border-radius: 7px;
            text-decoration: none !important;
            font-weight: bold;
            margin-top: 20px;
            margin-right: 10px;
            transition: .3s;
        }

        .btn-main:hover {
            background: #10783a;
            transform: translateY(-3px);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid white;
        }

        .btn-outline:hover {
            background: white;
            color: var(--blue) !important;
        }

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
            color: var(--blue);
            font-size: 34px;
            font-weight: 800;
        }

        .section-title p {
            color: #697789;
            font-size: 16px;
        }

        .service-card {
            background: white;
            padding: 35px 25px;
            height: 100%;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,.06);
            border-bottom: 4px solid var(--green);
            transition: .3s;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,.12);
        }

        .service-card i {
            font-size: 38px;
            color: var(--green);
            margin-bottom: 22px;
        }

        .service-card h4 {
            color: var(--blue);
            font-weight: 700;
            margin-bottom: 15px;
        }

        .service-card p {
            color: #657386;
            line-height: 1.7;
        }

        .facts {
            background: linear-gradient(120deg, var(--blue), var(--dark));
            color: white;
            text-align: center;
        }

        .fact span {
            display: block;
            font-size: 42px;
            font-weight: 800;
            color: #55d68a;
        }

        .fact p {
            font-size: 17px;
        }

        .sponsor img {
            background: white;
            padding: 12px;
            border-radius: 12px;
            max-height: 90px;
            max-width: 100%;
            object-fit: contain;
        }

        .sponsor {
            margin-bottom: 20px;
        }

        @media(max-width: 768px) {
            .hero {
                min-height: 550px;
            }

            .hero-content {
                padding: 65px 22px;
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
        }
    </style>
</head>

<body>

@include('FrontEnd.Public_Pages.Layouts.TopHeader')
@include('FrontEnd.Public_Pages.Layouts.Header')

<!-- GE MEMIS HERO -->
<section class="hero">

    <div class="hero-bg" id="heroBg"></div>
    <div class="hero-overlay"></div>

    <div class="hero-content">

        <div class="brand-label">
            GeMEMIS DIGITAL PLATFORM
        </div>

        <h1>
            Welcome to GeMEMIS
        </h1>

        <p>
            A modern digital management platform designed to
            support efficient service delivery, digital operations,
            innovation and transparent management.
        </p>

        <a href="{{ route('exhibition_space_booking.index') }}"
           class="btn-main">
            Explore Digital Services
        </a>

        <a href="https://www.youtube.com/watch?v=ue9IJAJrBLc"
           class="btn-main btn-outline"
           target="_blank"
           rel="noopener noreferrer">
            Watch How It Works
        </a>

        <p style="margin-top:30px;font-size:15px;">
            Supporting events including the
            50th Dar es Salaam International Trade Fair.
        </p>

    </div>
</section>

<!-- DIGITAL SERVICES -->
<section class="section section-light">

    <div class="container">

        <div class="section-title">
            <h2>GeMEMIS Digital Services</h2>
            <p>
                Access GeMEMIS digital services through one integrated platform.
            </p>
        </div>

        <div class="row g-4">

            <a href="{{ route('exhibition_space_booking.index') }}"
               class="col-lg-4 col-md-6 text-decoration-none">

                <div class="service-card">

                    <i class="fa fa-link"></i>

                    <h4>Online Exhibition Space Booking</h4>

                    <p>
                        Access exhibition space booking services digitally with
                        convenient access to available services.
                    </p>

                </div>
            </a>

            <a href="{{ route('/') }}"
               class="col-lg-4 col-md-6 text-decoration-none">

                <div class="service-card">

                    <i class="fa fa-file-text"></i>

                    <h4>E-Permit Application</h4>

                    <p>
                        Apply for trade permits digitally
                        through streamlined processing.
                    </p>

                </div>
            </a>

            <div class="col-lg-4 col-md-6">

                <div class="service-card">

                    <i class="fa fa-globe"></i>

                    <h4>GeMEMIS Information Centre</h4>

                    <p>
                        Access GeMEMIS information and
                        digital services from one platform.
                    </p>

                </div>
            </div>

        </div>

    </div>
</section>

<!-- PLATFORM STATISTICS -->
<section class="section facts">

    <div class="container">

        <div class="row">

            <div class="col-lg-4 fact">
                <span>550,000+</span>
                <p>Visitors</p>
            </div>

            <div class="col-lg-4 fact">
                <span>3,500+</span>
                <p>Exhibitors</p>
            </div>

            <div class="col-lg-4 fact">
                <span>7,200+</span>
                <p>Products</p>
            </div>

        </div>

    </div>
</section>

<!-- SPONSORS: ORIGINAL DATABASE DATA PRESERVED -->
<section class="section">

    <div class="container">

        <div class="section-title">

            <h2>Our Sponsors and Partners</h2>

            <p>
                Recognising our sponsors and institutional partners.
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

<!-- ORIGINAL SLIDING DATA PRESERVED -->
<script>

    const images = @json($slidings->pluck('slidingImage')->values());

    let index = 0;

    const heroBg = document.getElementById('heroBg');

    if (heroBg && images.length > 0) {

        heroBg.style.backgroundImage =
            `url("${images[0]}")`;

        if (images.length > 1) {

            setInterval(function () {

                index = (index + 1) % images.length;

                heroBg.style.backgroundImage =
                    `url("${images[index]}")`;

            }, 6000);

        }

    }

</script>

</body>
</html>
