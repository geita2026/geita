<!doctype html>
<html class="no-js" lang="en">
<head>
    <title>Terms of participation | GeMEMIS</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
    <style>
        .criteria-card {
            height: 100%;
        }

        .criteria-lang {
            font-size: 12px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #FFBF00;
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
        }

        .criteria-card h3 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .criteria-card > p.lead-line {
            color: rgba(255, 255, 255, 0.65);
            font-size: 14px;
            margin-bottom: 22px;
        }

        ol.criteria-list {
            list-style: none;
            counter-reset: criteria;
            padding: 0;
            margin: 0;
        }

        ol.criteria-list > li {
            counter-increment: criteria;
            position: relative;
            padding: 0 0 0 46px;
            margin-bottom: 16px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 14.5px;
            line-height: 1.65;
        }

        ol.criteria-list > li::before {
            content: counter(criteria, decimal-leading-zero);
            position: absolute;
            left: 0;
            top: 1px;
            width: 32px;
            height: 32px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: rgba(255, 191, 0, 0.12);
            border: 1px solid rgba(255, 191, 0, 0.35);
            color: #FFBF00;
            font-size: 12.5px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        .terms-note {
            border-left: 3px solid #FFBF00;
            padding: 16px 20px;
            background: rgba(255, 191, 0, 0.06);
            border-radius: 0 14px 14px 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            line-height: 1.7;
        }

        .terms-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            margin-top: 34px;
        }
    </style>
</head>

<body class="gememis-theme">

@include('FrontEnd.Public_Pages.Layouts.PageLoader')

<header class="header">
    @include('FrontEnd.Public_Pages.Layouts.TopHeader')
    @include('FrontEnd.Public_Pages.Layouts.Header')
</header>

<!-- Breadcrumbs -->
<div class="breadcrumbs overlay" style="background-image: url({{ asset('images/geita/geita-mine.jpg') }})">
    <div class="container">
        <div class="bread-inner">
            <div class="row">
                <div class="col-12">
                    <h2>Terms of Participation</h2>
                    <ul class="bread-list">
                        <li><a href="{{ route('index') }}">Home</a></li>
                        <li><i class="icofont-simple-right"></i></li>
                        <li class="active">Terms of Participation</li>
                    </ul>
                </div>
            </div>
            <br><br>
        </div>
    </div>
</div>
<!-- End Breadcrumbs -->

<section class="py-5">
    <div class="container">

        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="terms-note">
                    <i class="fa fa-info-circle text-warning me-2"></i>
                    Read, understand and accept these terms of participation before you apply for exhibition space at
                    the Geita Mineral Exhibition. Applying through GeMEMIS means you accept them.
                    <br>
                    <span style="color:rgba(255,255,255,0.6);">
                        Soma, elewa na ukubali masharti haya ya ushiriki kabla ya kuomba nafasi ya maonesho.
                        Kuwasilisha maombi kupitia GeMEMIS ni kukubali masharti haya.
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-center">

            <!-- Swahili -->
            <div class="col-lg-6">
                <div class="brand-card p-4 p-md-5 criteria-card">
                    <span class="criteria-lang">Kiswahili</span>
                    <h3>Vigezo vya jumla</h3>
                    <p class="lead-line">Mshiriki anatakiwa:</p>

                    <ol class="criteria-list">
                        <li>Kuwa na usajili halali wa taasisi, kampuni, biashara au kikundi pale unapohitajika.</li>
                        <li>Kuwa na leseni au kibali cha biashara kulingana na aina ya shughuli.</li>
                        <li>Kuwa na bidhaa, huduma, teknolojia au bunifu zinazohusiana na sekta ya madini au huduma zinazoiwezesha sekta hiyo.</li>
                        <li>Kuwasilisha maombi kupitia utaratibu rasmi wa waandaaji.</li>
                        <li>Kukubali na kuzingatia masharti ya ushiriki.</li>
                        <li>Kulipia ada ya ushiriki kulingana na viwango vilivyowekwa, isipokuwa pale anapopewa msamaha rasmi.</li>
                        <li>Kuwa na wawakilishi wenye uelewa wa bidhaa au huduma watakazozionesha.</li>
                        <li>Kuzingatia masharti ya afya, usalama na utunzaji wa mazingira.</li>
                        <li>Kutoa taarifa sahihi kuhusu taasisi na bidhaa zake.</li>
                        <li>Kushiriki katika shughuli za maonesho na kutoa taarifa za tathmini pale zitakapohitajika.</li>
                    </ol>
                </div>
            </div>

            <!-- English -->
            <div class="col-lg-6">
                <div class="brand-card p-4 p-md-5 criteria-card">
                    <span class="criteria-lang">English</span>
                    <h3>General criteria</h3>
                    <p class="lead-line">A participant is required to:</p>

                    <ol class="criteria-list">
                        <li>Possess valid registration for their institution, company, business or group, where applicable.</li>
                        <li>Hold a business licence or permit appropriate to the nature of their activities.</li>
                        <li>Offer products, services, technologies or innovations related to the mining sector, or services that support this sector.</li>
                        <li>Submit an application through the organisers&rsquo; official procedure.</li>
                        <li>Accept and adhere to the terms and conditions of participation.</li>
                        <li>Pay the participation fee according to the established rates, unless formally granted an exemption.</li>
                        <li>Have representatives who are knowledgeable about the products or services being showcased.</li>
                        <li>Comply with health, safety and environmental protection requirements.</li>
                        <li>Provide accurate information regarding their institution and its products.</li>
                        <li>Participate in exhibition activities and provide evaluation feedback when requested.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="terms-actions">
            <a href="{{ url()->previous() }}" class="btn-brand-outline">
                <i class="fa fa-arrow-left"></i> Go Back
            </a>
            <a href="{{ route('exhibition_space_booking.index') }}" class="btn-brand">
                <i class="fa fa-th-large"></i> Book Exhibition Space
            </a>
        </div>

    </div>
</section>

@include('FrontEnd.Public_Pages.Layouts.Footer')

</body>
</html>
