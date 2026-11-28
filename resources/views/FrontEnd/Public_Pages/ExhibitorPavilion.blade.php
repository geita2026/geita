<!doctype html>
<html lang="en">
<head>
    <title>{{ $company->companyName }} | {{ config('app.name') }}</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
</head>
<body>
    @include('FrontEnd.Public_Pages.Layouts.PageLoader')
    <header class="header">
        @include('FrontEnd.Public_Pages.Layouts.TopHeader')
        @include('FrontEnd.Public_Pages.Layouts.Header')
    </header>

    <main class="container py-5">
        <a class="d-inline-block mb-3" href="{{ route('discover_exhibitor.index') }}">Back to exhibitors</a>
        <h1>{{ $company->companyName }}</h1>
        @if ($participation?->welcome_word)
            <p>{{ $participation->welcome_word }}</p>
        @endif

        <section class="my-4" aria-labelledby="exhibitor-locations">
            <h2 id="exhibitor-locations">Where to find this exhibitor</h2>
            <ul class="list-group">
                @forelse ($locations as $location)
                    <li class="list-group-item d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <span>
                            <strong>{{ $location->pavilionName }}</strong>
                            @if ($location->boothNo)
                                <span class="d-block">Booth {{ $location->boothNo }}</span>
                            @elseif (!$location->boothID)
                                <span class="d-block">Whole pavilion</span>
                            @endif
                        </span>
                        <a class="btn btn-outline-primary" href="{{ route('exhibition_space_booking.edit', $location->pavilionID) }}#{{ $location->boothID ? 'floor-plan-booth-' . $location->boothID : 'pavilion-floor-plan' }}">View floor plan</a>
                    </li>
                @empty
                    <li class="list-group-item">A pavilion location has yet to be published.</li>
                @endforelse
            </ul>
        </section>

        @if ($participation?->pavilion_descriptions || $website || $myProducts->isNotEmpty())
            <section class="my-4" aria-labelledby="exhibitor-about">
                <h2 id="exhibitor-about">About the exhibitor</h2>
                @if ($participation?->pavilion_descriptions)
                    <p>{{ $participation->pavilion_descriptions }}</p>
                @endif
                @if ($website && in_array(parse_url($website, PHP_URL_SCHEME), ['http', 'https'], true))
                    <p><a href="{{ $website }}">Exhibitor website</a></p>
                @endif
                @if ($myProducts->isNotEmpty())
                    <ul>
                        @foreach ($myProducts as $product)
                            <li>{{ $product->service_name }}</li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endif
    </main>

    @include('FrontEnd.Public_Pages.Layouts.Footer')
</body>
</html>
