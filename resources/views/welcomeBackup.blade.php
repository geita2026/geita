<!doctype html>
<html lang="en">
<head>
    <title>Geita Mineral Exhibition | GeMEMIS</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-public-entry.css') }}">
</head>
<body class="geita-home">
    @include('FrontEnd.Public_Pages.Layouts.Header')
    @include('FrontEnd.Public_Pages.Layouts.TopHeader')

    <main>
        <section class="geita-intro" aria-labelledby="exhibition-title">
            <div class="container geita-intro-layout">
                <div>
                    <p class="geita-location">Geita, Tanzania</p>
                    <h1 id="exhibition-title">Geita Mineral Exhibition</h1>
                    <p>Register as an exhibitor, book exhibition space, and manage your application and payments through GeMEMIS.</p>
                    <div class="geita-intro-actions">
                        <a class="geita-primary" href="{{ route('exhibition_space_booking.index') }}">Book exhibition space</a>
                        <a class="geita-link" href="{{ route('registration') }}">Create an account</a>
                        <a class="geita-link" href="{{ route('login') }}">Sign in</a>
                    </div>
                </div>
                <img class="geita-intro-image" src="{{ asset('images/geita/geita-mine.jpg') }}" alt="Mining site in Geita">
            </div>
        </section>

        @php
            $services = [
                ['Exhibition space', 'Browse halls, stands and open plots.', 'exhibition_space_booking.index'],
                ['Sponsorship', 'View available sponsorship packages.', 'sponsorship_package_groups'],
                ['Exhibitor directory', 'Find participating companies and organisations.', 'discover_exhibitor.index'],
                ['Map of Geita', 'Explore Geita and get directions.', 'ditf_map'],
                ['Virtual exhibition', 'Browse exhibited minerals, equipment and services.', 'exhibited_products.index'],
            ];
        @endphp
        <section class="container geita-home-section" aria-labelledby="exhibition-services">
            <h2 id="exhibition-services">Exhibition services</h2>
            <ul class="geita-service-list">
                @foreach ($services as [$label, $description, $route])
                    <li>
                        <a href="{{ route($route) }}">{{ $label }}</a>
                        <p>{{ $description }}</p>
                    </li>
                @endforeach
            </ul>
        </section>

        @if ($sponsors->isNotEmpty())
            <section class="container geita-home-section" aria-labelledby="exhibition-sponsors">
                <h2 id="exhibition-sponsors">Exhibition sponsors</h2>
                <ul class="geita-sponsor-list">
                    @foreach ($sponsors as $sponsor)
                        <li>
                            <img src="{{ asset('VirtualImages/'.$sponsor->sponsor_logo) }}"
                                alt="{{ $sponsor->sponsor_name ?? 'Exhibition sponsor' }}" loading="lazy"
                                onerror="this.onerror=null; this.src='{{ asset('images/coat-of-arms.svg') }}'">
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </main>

    @include('FrontEnd.Public_Pages.Layouts.Footer', ['simpleFooter' => true])
</body>
</html>
