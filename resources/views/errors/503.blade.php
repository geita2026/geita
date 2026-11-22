<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Booking Countdown â€“ 50th Dar es Salaam International Trade Fair</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/png">

    <!-- Core CSS -->
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet">

    <style>
        body.login-container {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            background: radial-gradient(circle at top, #0b1e3b, #043d3a, #021f1f);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wrapper {
            width: 100%;
            padding: 20px;
            text-align: center;
        }

        .card-box {
            background: rgba(255, 255, 255, 0.98);
            max-width: 900px;
            margin: auto;
            border-radius: 20px;
            padding: 55px 50px;
            box-shadow: 0 30px 65px rgba(0,0,0,.35);
        }

        .logo img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        /* JUBILEE LOGO */
        .jubilee-logo img {
            width: 160px;
            max-width: 100%;
            margin: 10px auto 25px;
            display: block;
        }

        .title {
            font-size: 30px;
            font-weight: 700;
            color: #0b1e3b;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 20px;
            font-weight: 600;
            color: #b8961e;
            margin-bottom: 30px;
        }

        .text {
            font-size: 16px;
            color: #555;
            margin-bottom: 15px;
            line-height: 1.7;
        }

        .highlight {
            color: #b8961e;
            font-weight: 600;
        }

        .info-box {
            background: #f6f9fa;
            border-radius: 14px;
            padding: 30px;
            margin: 35px 0;
            text-align: left;
        }

        .info-box h5 {
            font-weight: 600;
            margin-bottom: 15px;
            color: #0b1e3b;
        }

        .info-box ul {
            padding-left: 20px;
            margin: 0;
        }

        .info-box li {
            margin-bottom: 12px;
            font-size: 15px;
            color: #444;
        }

        .countdown {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 35px 0;
            flex-wrap: wrap;
        }

        .countdown-box {
            background: #f4f7f8;
            border-radius: 12px;
            padding: 18px 20px;
            min-width: 90px;
        }

        .countdown-value {
            font-size: 30px;
            font-weight: 700;
            color: #0b1e3b;
        }

        .countdown-label {
            font-size: 12px;
            color: #777;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .contact {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #eee;
        }

        .contact a {
            font-size: 16px;
            color: #0b1e3b;
            font-weight: 600;
            text-decoration: none;
        }

        .contact i {
            margin-right: 6px;
        }

        .footer {
            margin-top: 30px;
            color: #777;
        }

        @media (max-width: 576px) {
            .card-box {
                padding: 35px 25px;
            }
            .title {
                font-size: 24px;
            }
            .subtitle {
                font-size: 17px;
            }
            .jubilee-logo img {
                width: 130px;
            }
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>

<body class="login-container">

<div class="wrapper">
    <div class="card-box">

        <!-- TANTRADE LOGO -->
        <div class="logo">
            <img src="{{ asset('images/logo.jpg') }}" alt="TanTrade Logo">
        </div>

        <!-- 50TH JUBILEE LOGO -->
        <div class="jubilee-logo">
            <img src="{{ asset('images/50.jpeg') }}" alt="50th Sabasaba Golden Jubilee">
        </div>

        <div class="title">
            Booking Opens Soon
        </div>

        <div class="subtitle">
            50<sup>th</sup> Dar es Salaam International Trade Fair â€“ Golden Jubilee of Sabasaba
        </div>

        <p class="text">
            Welcome to the official booking portal for the
            <span class="highlight">50<sup>th</sup> Dar es Salaam International Trade Fair (DITF)</span>,
            commemorating <strong>50 Years of Sabasaba</strong>.
        </p>

        <p class="text">
            The exhibitor booking window will officially open shortly. We are completing final
            preparations to ensure a seamless, transparent, and reliable booking experience.
        </p>

        <div class="info-box">
            <h5>What to Expect When Booking Opens</h5>
            <ul>
                <li>Official opening of exhibitor bookings for the 50<sup>th</sup> DITF.</li>
                <li>Enhanced space allocation and reservation controls.</li>
                <li>Improved billing, invoicing, and payment workflows.</li>
                <li>Optimized performance for high booking demand.</li>
                <li>Accurate confirmations and reporting for exhibitors.</li>
            </ul>
        </div>

        <div class="countdown">
            <div class="countdown-box">
                <div class="countdown-value" id="days">0</div>
                <div class="countdown-label">Days</div>
            </div>
            <div class="countdown-box">
                <div class="countdown-value" id="hours">0</div>
                <div class="countdown-label">Hours</div>
            </div>
            <div class="countdown-box">
                <div class="countdown-value" id="minutes">0</div>
                <div class="countdown-label">Minutes</div>
            </div>
            <div class="countdown-box">
                <div class="countdown-value" id="seconds">0</div>
                <div class="countdown-label">Seconds</div>
            </div>
        </div>

        <p class="text">
            Time remaining until <strong>booking officially opens</strong>.
        </p>

        <div class="contact">
            <p class="text">For enquiries related to booking and registration:</p>
            <a href="mailto:ictsupport@tantrade.go.tz">
                <i class="icon-envelop"></i> ictsupport@tantrade.go.tz
            </a>
        </div>
    </div>

    <div class="footer text-center">
        @include('layouts.footer')
    </div>
</div>

<script>
    const bookingOpenTime = new Date("{{ config('global.MAINTENANCE_END_TIME') }}").getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = bookingOpenTime - now;

        if (distance <= 0) {
            document.querySelector('.countdown').innerHTML =
                '<p class="text">Booking is now opening. Please refresh the page.</p>';
            return;
        }

        document.getElementById('days').textContent =
            Math.floor(distance / (1000 * 60 * 60 * 24));
        document.getElementById('hours').textContent =
            Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        document.getElementById('minutes').textContent =
            Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        document.getElementById('seconds').textContent =
            Math.floor((distance % (1000 * 60)) / 1000);
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
</script>

</body>
</html>

