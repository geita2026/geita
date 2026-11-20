<!doctype html>
<html lang="en">
<head>
    <!-- ================= META ================= -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="Tantrade,DITF,SAMS,Vibari,e-Permit,Sabasaba">
    <meta name="description" content="Tanzania Trade Development Authority – Digital Trade Services">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tanzania Trade Development Authority | TanTrade</title>

    <!-- ================= CSS ================= -->
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')

    <style>
        /* ========== ROOT COLORS (ORG COLORS) ========== */
        :root {
            --blue: #0d6efd;
            --green: #198754;
            --black: #0b0f19;
            --gray: #6c757d;
        }

        body {
            margin: 0;
            font-family: "Inter", "Segoe UI", sans-serif;
            color: #fff;
            background: var(--black);
            overflow-x: hidden;
        }

        /* ========== HERO SECTION ========== */
        .hero {
            position: relative;
            height: 100vh;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            animation: zoom 20s infinite;
            transition: background-image 1.2s ease-in-out;
        }

        @keyframes zoom {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(120deg, rgba(13,110,253,.75), rgba(11,15,25,.85), rgba(25,135,84,.75));
        }

        .hero-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            animation: fadeUp 1.2s ease forwards;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-content h1 {
            font-size: 3.2rem;
            font-weight: 800;
        }

        .hero-content p {
            max-width: 780px;
            margin: 20px auto 35px;
            font-size: 1.1rem;
            color: #e0e0e0;
        }

        /* ========== BUTTONS ========== */
        .btn-main {
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
            background: var(--green);
            color: #fff;
            border: none;
            transition: .3s ease;
        }

        .btn-main:hover {
            background: var(--blue);
            transform: translateY(-3px);
        }

        /* ========== SERVICES ========== */
        .section {
            padding: 90px 0;
            background: #fff;
            color: #212529;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-weight: 800;
            color: var(--black);
        }

        .service-card {
            background: #fff;
            padding: 45px 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 45px rgba(0,0,0,.12);
            transition: .4s ease;
            height: 100%;
        }

        .service-card:hover {
            transform: translateY(-12px);
        }

        .service-card i {
            font-size: 48px;
            color: var(--blue);
            margin-bottom: 20px;
        }

        /* ========== FUN FACTS ========== */
        .facts {
            background: linear-gradient(120deg, var(--blue), var(--black), var(--green));
            color: #fff;
            text-align: center;
        }

        .fact span {
            font-size: 42px;
            font-weight: 800;
        }

        /* ========== SPONSORS ========== */
        .sponsor img {
            background: #fff;
            padding: 12px;
            border-radius: 12px;
            max-height: 70px;
        }
    </style>
</head>

<body>

<!-- ================= HEADER ================= -->
@include('FrontEnd.Public_Pages.Layouts.TopHeader')
@include('FrontEnd.Public_Pages.Layouts.Header')

<!-- ================= HERO (BACKGROUND SLIDESHOW) ================= -->
<section class="hero">
    <div class="hero-bg" id="heroBg"></div>
    <div class="hero-overlay"></div>

    <div class="hero-content">
        <div>
            <h1>50<sup>th</sup> Dar es Salaam International Trade Fair </h1>
            <p>
                A national digital platform for trade promotion, exhibitions,
                innovation, and international business engagement.
            </p>
            <a href="{{ route('exhibition_space_booking.index') }}" class="btn-main">
                Book Exhibition Space
            </a> 
    <p> </p> <p> </p> <p> </p><p> </p><p> </p><p> </p><p> </p><p> </p><p> </p><p> </p><p> </p><p> </p><p> </p> 
            <a href="https://www.youtube.com/watch?v=ue9IJAJrBLc" class="btn-main" target="_blank">
                How to book exhibition space
            </a> 

            
          
        </div>
    </div>
</section>

<!-- ================= SERVICES ================= -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Digital Trade Services</h2>
            <p>Secure, efficient and transparent access to TanTrade services</p>
        </div>

        <div class="row g-4">
            <a href="{{ route('exhibition_space_booking.index') }}" class="col-lg-4 text-decoration-none">
                <div class="service-card">
                    <i class="fa fa-link"></i>
                    <h4>Online Space Booking</h4>
                    <p>Book exhibition spaces digitally with real-time availability.</p>
                </div>
            </a>
            {{-- Online Ticket --}}
            <a href="{{ route('sabasaba_tickets') }}" class="col-lg-4 text-decoration-none">
                <div class="service-card">
                    <i class="fa fa-link"></i>
                    <h4>Online Ticketing</h4>
                    <p>Purchase and manage event tickets online.</p>
                </div>
            </a>
            {{-- E-Permit --}}
            <a href="{{ route('/') }}" class="col-lg-4 text-decoration-none">
                <div class="service-card">
                    <i class="fa fa-link"></i>
                    <h4>E-Permit Application</h4>
                    <p>Apply for trade permits digitally with streamlined processing.</p>
                </div>
            </a>
            
            
        </div>
    </div>
</section>

<!-- ================= FUN FACTS ================= -->
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

<!-- ================= SPONSORS ================= -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Our Sponsors</h2>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($sponsors as $value)
                <div class="col-lg-2 col-md-3 sponsor text-center">
                    <img src="{{ asset('VirtualImages/'.$value->sponsor_logo) }}">
                </div>
            @endforeach
        </div>
    </div>
</section>

@include('FrontEnd.Public_Pages.Layouts.Footer')

<!-- ================= BACKGROUND SLIDESHOW JS ================= -->
<script>
    const images = [
        @foreach($slidings as $slide)
            "{{ $slide->slidingImage }}",
        @endforeach
    ];

    let index = 0;
    const heroBg = document.getElementById('heroBg');
    heroBg.style.backgroundImage = `url(${images[0]})`;

    setInterval(() => {
        index = (index + 1) % images.length;
        heroBg.style.backgroundImage = `url(${images[index]})`;
    }, 6000);
</script>

</body>
</html>
