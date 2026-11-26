<!doctype html>
<html lang="en">
<head>
    <title>Map of Geita | GeMEMIS</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-public-entry.css') }}">
</head>
<body class="geita-home">
    @include('FrontEnd.Public_Pages.Layouts.Header')
    @include('FrontEnd.Public_Pages.Layouts.TopHeader')

    <main class="container geita-home-section" aria-labelledby="geita-map-title">
        <h1 id="geita-map-title" class="h2 mb-3">Map of Geita</h1>
        <p class="mb-3">
            <a class="geita-link" href="https://www.google.com/maps/search/?api=1&amp;query=Geita%2C%20Tanzania">
                Open Geita in Google Maps
            </a>
        </p>
        <iframe class="geita-map-frame" title="Interactive map of Geita, Tanzania"
            src="https://maps.google.com/maps?q=Geita%2C%20Tanzania&amp;z=13&amp;output=embed"
            width="1000" height="480" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
    </main>

    @include('FrontEnd.Public_Pages.Layouts.Footer', ['simpleFooter' => true])
</body>
</html>
