<!doctype html>
<html lang="en">
<head>
    <title>{{ $category->category_name }} | {{ config('app.name') }}</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
    <link rel="stylesheet" href="{{ asset('assets/css/exhibition-booking.css') }}">
</head>
<body>
    @include('FrontEnd.Public_Pages.Layouts.PageLoader')
    <header class="header">
        @include('FrontEnd.Public_Pages.Layouts.TopHeader')
        @include('FrontEnd.Public_Pages.Layouts.Header')
    </header>

    <main class="container py-5 pavilion-listing">
        <nav aria-label="Breadcrumb" class="mb-3">
            <a href="{{ route('exhibition_space_booking.index') }}">Exhibition spaces</a>
        </nav>

        <header class="mb-4">
            <p class="text-muted mb-1">{{ $category->event_name }}</p>
            <h1>{{ $category->category_name }}</h1>
            @if ($category->category_descriptions)
                <p class="mt-2">{{ $category->category_descriptions }}</p>
            @endif
        </header>

        <div class="pavilion-listing-grid">
            @forelse ($spaces as $space)
                @php
                    $pavilion = $space['pavilion'];
                    $registered = $space['registeredBoothCount'];
                    $available = $space['availableBoothCount'];
                    $mapUrl = route('exhibition_space_booking.edit', $pavilion->pavilionID) . '#pavilion-floor-plan';
                @endphp
                <article class="pavilion-listing-card">
                    <h2><a href="{{ $mapUrl }}">{{ $pavilion->pavilionName }}</a></h2>

                    <div class="pavilion-listing-availability{{ $space['canBook'] ? ' is-open' : '' }}">
                        @if ($space['hasBooths'])
                            <strong>{{ number_format($available) }}</strong>
                            {{ $available === 1 ? 'booth available' : 'booths available' }}
                            @if ($registered > 0)
                                <span>of {{ number_format($registered) }} registered</span>
                            @else
                                <span>Booths have yet to be listed</span>
                            @endif
                        @else
                            <strong>{{ $space['canBook'] ? 'Whole pavilion available' : 'Whole pavilion booked' }}</strong>
                        @endif
                    </div>

                    @if ($pavilion->pavilion_size)
                        <p>{{ number_format($pavilion->pavilion_size, 2) }} m²</p>
                    @endif
                    @if (!$space['hasBooths'] && $category->cost_one_msq !== null && $category->type_of_currency)
                        <p>{{ $category->type_of_currency }} {{ number_format($category->cost_one_msq, 2) }} / m²</p>
                    @endif
                    @if ($pavilion->pavilionDescription)
                        <p>{{ $pavilion->pavilionDescription }}</p>
                    @endif

                    <div class="pavilion-listing-actions">
                        @if ($space['canBook'])
                            <a class="btn btn-primary" href="{{ route('booking.show', $pavilion->pavilionID) }}">
                                {{ $space['hasBooths'] ? 'Book booths' : 'Book pavilion' }}
                            </a>
                        @endif
                        <a class="btn btn-outline-secondary" href="{{ $mapUrl }}">View floor plan<span class="visually-hidden"> for {{ $pavilion->pavilionName }}</span></a>
                    </div>
                </article>
            @empty
                <p>No pavilions have been published for this category.</p>
            @endforelse
        </div>

        @if ($spaceCategories->isNotEmpty())
            <nav aria-label="Other space categories" class="mt-4">
                <h2 class="h5">Other space categories</h2>
                <ul class="list-inline">
                    @foreach ($spaceCategories as $otherCategory)
                        <li class="list-inline-item">
                            <a href="{{ route('exhibition_space_booking.show', $otherCategory->category_id) }}">{{ $otherCategory->category_name }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        @endif
    </main>

    @include('FrontEnd.Public_Pages.Layouts.Footer')
</body>
</html>
