<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="GeMEMIS,Geita,mineral exhibition,madini,maonesho ya madini,exhibition space,space allocation,e-Permit,Tanzania">
    <meta name="description" content="GeMEMIS – the Geita Mineral Exhibition Management Information System. Register, apply, choose your space, pay and check in.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Geita Mineral Exhibition | GeMEMIS</title>

    <!-- CSS Designs & Golden Theme -->
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')

    <style>
        /* ========== HERO SECTION ========== */
        .hero-golden {
            position: relative;
            min-height: 92vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 60px 20px 80px;
        }

        .hero-bg-layer {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            animation: heroZoom 24s infinite alternate ease-in-out;
            transition: background-image 1.2s ease-in-out;
            filter: brightness(0.28) contrast(1.15);
            z-index: 1;
        }

        @keyframes heroZoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.08); }
        }

        .hero-scrim {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(1000px 500px at 50% 10%, rgba(255, 191, 0, 0.28), transparent 70%),
                linear-gradient(180deg, rgba(7, 5, 6, 0.75) 0%, rgba(7, 5, 6, 0.95) 100%);
            z-index: 2;
        }

        .hero-content-box {
            position: relative;
            z-index: 10;
            max-width: 960px;
            text-align: center;
            padding: 30px 15px;
        }

        .hero-kicker {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: #FFBF00;
            margin-bottom: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .hero-title {
            font-size: 2.8rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.18;
            margin-bottom: 18px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.8);
        }

        @media (min-width: 768px) {
            .hero-title {
                font-size: 4.2rem;
            }
        }

        .hero-title .gold-accent {
            background: linear-gradient(135deg, #FFE9A8 0%, #FFBF00 50%, #E5A900 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: rgba(255, 255, 255, 0.82);
            max-width: 720px;
            margin: 0 auto 32px;
            line-height: 1.6;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.7);
        }

        /* Countdown Units */
        .master-countdown {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin: 32px 0 28px;
        }

        .hero-cta-group {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        /* Service Cards */
        .service-brand-card {
            padding: 36px 28px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: left;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .service-brand-card:hover {
            transform: translateY(-6px);
        }

        .service-icon-box {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            background: rgba(255, 191, 0, 0.12);
            border: 1.5px solid rgba(255, 191, 0, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #FFBF00;
            margin-bottom: 22px;
            box-shadow: 0 0 20px rgba(255, 191, 0, 0.2);
        }

        .service-brand-card h4 {
            color: #ffffff;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .service-brand-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .service-arrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #FFBF00;
            font-weight: 600;
            font-size: 14px;
            transition: gap 0.2s ease;
        }

        .service-brand-card:hover .service-arrow {
            gap: 12px;
            color: #FFE9A8;
        }

        /* Facts Section */
        .fact-gold-box {
            text-align: center;
            padding: 30px 20px;
        }

        .fact-brand-number {
            font-size: 3rem;
            font-weight: 800;
            color: #FFBF00;
            line-height: 1;
            margin-bottom: 10px;
            text-shadow: 0 0 24px rgba(255, 191, 0, 0.45);
        }

        .fact-gold-label {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.75);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
        }

        /* Sponsor grid */
        .sponsor-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 191, 0, 0.2);
            border-radius: 16px;
            padding: 16px;
            height: 84px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
        }

        .sponsor-card:hover {
            background: rgba(255, 191, 0, 0.08);
            border-color: #FFBF00;
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(255, 191, 0, 0.2);
        }

        .sponsor-card img {
            max-height: 52px;
            max-width: 100%;
            object-fit: contain;
            filter: grayscale(0.2) contrast(1.1);
            transition: filter 0.25s ease;
        }

        .sponsor-card:hover img {
            filter: grayscale(0) contrast(1.15);
        }
    </style>
</head>

<body class="gememis-theme">

<!-- Preloader -->
@include('FrontEnd.Public_Pages.Layouts.PageLoader')

<!-- Header & Alerts -->
@include('FrontEnd.Public_Pages.Layouts.Header')
@include('FrontEnd.Public_Pages.Layouts.TopHeader')

<!-- ================= HERO SECTION ================= -->
<section class="hero-golden">
    <div class="hero-bg-layer" id="heroBg"></div>
    <div class="hero-scrim"></div>

    <div class="hero-content-box">
        <!-- Main Title -->
        <h1 class="hero-title">
            <span class="gold-accent">Geita</span>
            <span>· Mineral Exhibition</span>
        </h1>

        <p class="hero-subtitle">
            The whole exhibition, in one place. Register, apply, take your space on the floor plan, pay, check in.
        </p>

        <!-- Live countdown to the exhibition -->
        <div class="master-countdown">
            <div class="brand-pill min-w-[85px] py-3 px-3 text-center">
                <div class="text-3xl md:text-4xl font-bold leading-none tabular text-[#FFBF00]" id="cdDays">--</div>
                <div class="text-[11px] uppercase tracking-widest text-white/60 mt-1">Days</div>
            </div>
            <div class="brand-pill min-w-[85px] py-3 px-3 text-center">
                <div class="text-3xl md:text-4xl font-bold leading-none tabular text-[#FFBF00]" id="cdHours">--</div>
                <div class="text-[11px] uppercase tracking-widest text-white/60 mt-1">Hours</div>
            </div>
            <div class="brand-pill min-w-[85px] py-3 px-3 text-center">
                <div class="text-3xl md:text-4xl font-bold leading-none tabular text-[#FFBF00]" id="cdMins">--</div>
                <div class="text-[11px] uppercase tracking-widest text-white/60 mt-1">Mins</div>
            </div>
            <div class="brand-pill min-w-[85px] py-3 px-3 text-center">
                <div class="text-3xl md:text-4xl font-bold leading-none tabular text-[#FFBF00]" id="cdSecs">--</div>
                <div class="text-[11px] uppercase tracking-widest text-white/60 mt-1">Secs</div>
            </div>
        </div>

        <p class="text-sm text-white/70 mb-4" id="countdownMessage">
            Countdown to the next Geita Mineral Exhibition · Geita, Tanzania
        </p>

        <!-- Primary Call to Actions -->
        <div class="hero-cta-group">
            <a href="{{ route('exhibition_space_booking.index') }}" class="btn-brand">
                <i class="fa fa-th-large"></i> Book Exhibition Space
            </a>

            <a href="{{ route('registration') }}" class="btn-brand-outline">
                <i class="fa fa-user-plus"></i> Create an Account
            </a>

            <a href="{{ route('login') }}" class="btn-brand-outline">
                <i class="fa fa-lock"></i> Sign In
            </a>
        </div>
    </div>
</section>

<!-- ================= DIGITAL TRADE SERVICES ================= -->
<section class="golden-section py-5">
    <div class="container">
        <div class="golden-section-title">
            <p class="text-warning text-uppercase font-semibold tracking-wider mb-2" style="font-size:13px;">Services</p>
            <h2>Built for the exhibition</h2>
            <p>Everything an exhibitor needs, online. From the first application to the morning the doors open.</p>
        </div>

        <div class="row g-4">
            <!-- 1. Space Booking -->
            <div class="col-lg-4 col-md-6">
                <div class="brand-card service-brand-card">
                    <div>
                        <div class="service-icon-box">
                            <i class="fa fa-th"></i>
                        </div>
                        <h4>Space, on the floor plan</h4>
                        <p>Halls, covered stands, open plots. See what is free, see what it costs, take it.</p>
                    </div>
                    <div>
                        <a href="{{ route('exhibition_space_booking.index') }}" class="service-arrow">
                            Book Your Booth Now <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. E-Permit Application -->
            <div class="col-lg-4 col-md-6">
                <div class="brand-card service-brand-card">
                    <div>
                        <div class="service-icon-box">
                            <i class="fa fa-file-text-o"></i>
                        </div>
                        <h4>Permits</h4>
                        <p>Apply for the permits an event needs. Follow each one from submission to approval.</p>
                    </div>
                    <div>
                        <a href="{{ route('/') }}" class="service-arrow">
                            Apply for a Permit <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Sponsorship Packages -->
            <div class="col-lg-4 col-md-6">
                <div class="brand-card service-brand-card">
                    <div>
                        <div class="service-icon-box">
                            <i class="fa fa-trophy"></i>
                        </div>
                        <h4>Sponsorship</h4>
                        <p>Your name across the grounds and the programme. Choose the tier that fits.</p>
                    </div>
                    <div>
                        <a href="{{ route('sponsorship_package_groups') }}" class="service-arrow">
                            Explore Packages <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 4. Discover Exhibitors -->
            <div class="col-lg-4 col-md-6">
                <div class="brand-card service-brand-card">
                    <div>
                        <div class="service-icon-box">
                            <i class="fa fa-users"></i>
                        </div>
                        <h4>Exhibitors</h4>
                        <p>Miners, dealers, processors, suppliers, financiers. Find who you came to meet.</p>
                    </div>
                    <div>
                        <a href="{{ route('discover_exhibitor.index') }}" class="service-arrow">
                            Browse Directory <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 5. Fair Ground Map -->
            <div class="col-lg-4 col-md-6">
                <div class="brand-card service-brand-card">
                    <div>
                        <div class="service-icon-box">
                            <i class="fa fa-map-marker"></i>
                        </div>
                        <h4>The grounds</h4>
                        <p>Gates, halls, pavilions, plots and stages. The whole site, laid out.</p>
                    </div>
                    <div>
                        <a href="{{ route('ditf_map') }}" class="service-arrow">
                            View Site Map <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 6. Virtual Exhibition -->
            <div class="col-lg-4 col-md-6">
                <div class="brand-card service-brand-card">
                    <div>
                        <div class="service-icon-box">
                            <i class="fa fa-globe"></i>
                        </div>
                        <h4>All year round</h4>
                        <p>The minerals, equipment and services on show — and a direct line to the people behind them.</p>
                    </div>
                    <div>
                        <a href="{{ route('exhibited_products.index') }}" class="service-arrow">
                            Enter Virtual Exhibition <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= FUN FACTS / KEY MILESTONES ================= -->
<section class="golden-section py-5">
    <div class="container">
        <div class="brand-card p-4 p-md-5">
            <div class="row g-4 align-items-center">
                <div class="col-6 col-lg-3 fact-gold-box">
                    <div class="fact-brand-number tabular">01</div>
                    <div class="fact-gold-label">Register</div>
                </div>
                <div class="col-6 col-lg-3 fact-gold-box">
                    <div class="fact-brand-number tabular">02</div>
                    <div class="fact-gold-label">Apply for Space</div>
                </div>
                <div class="col-6 col-lg-3 fact-gold-box">
                    <div class="fact-brand-number tabular">03</div>
                    <div class="fact-gold-label">Allocation &amp; Payment</div>
                </div>
                <div class="col-6 col-lg-3 fact-gold-box">
                    <div class="fact-brand-number tabular">04</div>
                    <div class="fact-gold-label">Check In</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= SPONSORS ================= -->
@if(isset($sponsors) && count($sponsors) > 0)
<section class="golden-section py-5">
    <div class="container">
        <div class="golden-section-title">
            <p class="text-warning text-uppercase font-semibold tracking-wider mb-2" style="font-size:13px;">Partners</p>
            <h2>Our sponsors</h2>
            <p>The organisations standing behind the exhibition.</p>
        </div>

        <div class="row g-3 justify-content-center">
            @foreach($sponsors as $value)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="sponsor-card">
                        <img src="{{ asset('VirtualImages/'.$value->sponsor_logo) }}" alt="Sponsor" loading="lazy" onerror="this.src='{{ asset('images/coat-of-arms.svg') }}'">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Footer -->
@include('FrontEnd.Public_Pages.Layouts.Footer')

<!-- ================= SCRIPTS ================= -->
<script>
    // 1. Background Slideshow
    // Geita photographs (public/images/geita). Drop replacements in that folder
    // and list them here to change what the hero shows.
    const images = [
        "{{ asset('images/geita/geita-mine.jpg') }}",
        "{{ asset('images/geita/geita-town.jpg') }}",
        "{{ asset('images/geita/geita-local-business.jpg') }}"
    ];

    let slideIdx = 0;
    const heroBg = document.getElementById('heroBg');
    if (heroBg && images.length > 0) {
        heroBg.style.backgroundImage = `url(${images[0]})`;
        if (images.length > 1) {
            setInterval(() => {
                slideIdx = (slideIdx + 1) % images.length;
                heroBg.style.backgroundImage = `url(${images[slideIdx]})`;
            }, 6000);
        }
    }

    // 2. Live countdown to the exhibition. Set targetDate to the opening day.
    const targetDate = new Date("2026-06-28T08:00:00+03:00").getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance > 0) {
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const pad = n => String(n).padStart(2, '0');
            const dEl = document.getElementById('cdDays');
            const hEl = document.getElementById('cdHours');
            const mEl = document.getElementById('cdMins');
            const sEl = document.getElementById('cdSecs');

            if (dEl) dEl.innerText = days;
            if (hEl) hEl.innerText = pad(hours);
            if (mEl) mEl.innerText = pad(minutes);
            if (sEl) sEl.innerText = pad(seconds);
        } else {
            const msgEl = document.getElementById('countdownMessage');
            if (msgEl) msgEl.innerHTML = '<span class="brand-chip">🔴 The Geita Mineral Exhibition Is Now Live!</span>';
        }
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);
</script>

</body>
</html>
