<!doctype html>
<html lang="en">
<head>
    <title>Exhibition space booking | {{ config('app.name') }}</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-public-entry.css') }}">
</head>
<body class="geita-home">
    @include('FrontEnd.Public_Pages.Layouts.Header')
    @include('FrontEnd.Public_Pages.Layouts.TopHeader')

    <main class="container geita-home-section geita-booking" aria-labelledby="booking-title">
        <nav class="geita-booking-breadcrumb" aria-label="Breadcrumb">
            <a class="geita-link" href="{{ route('index') }}">Home</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">Space booking</span>
        </nav>

        <header class="geita-booking-header">
            <h1 id="booking-title">Exhibition space booking</h1>
            <p>Choose a category to see available spaces and prices.</p>
        </header>

        @if ($spaceCategories->isEmpty())
            <p class="geita-booking-empty">There are no exhibition space categories available right now.</p>
        @else
            <ul class="geita-booking-list" aria-label="Space categories">
                @foreach ($spaceCategories as $item)
                    <li class="geita-booking-category">
                        <div>
                            <h2>{{ $item->category_name }}</h2>
                            @if (filled($item->category_descriptions))
                                <p>{{ $item->category_descriptions }}</p>
                            @endif
                        </div>

                        <div class="geita-booking-rate">
                            @if ($item->cost_one_msq >= 1)
                                <strong>{{ number_format($item->cost_one_msq, 2) }} {{ $item->type_of_currency }}</strong>
                                <span> per m²</span>
                            @else
                                <span>Price varies by stand or booth</span>
                            @endif
                        </div>

                        <a class="geita-primary"
                            href="{{ route('exhibition_space_booking.show', ['exhibition_space_booking' => $item->category_id]) }}"
                            aria-label="View spaces in {{ $item->category_name }}">
                            View spaces
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </main>

    @include('FrontEnd.Public_Pages.Layouts.Footer', ['simpleFooter' => true])
</body>
</html>
