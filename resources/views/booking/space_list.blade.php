<x-app-layout>
    <title>{{ config('app.name') }} | {{ $category->category_name }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/exhibition-booking.css') }}">
    @include('booking.navigation', ['bookingCategory' => ['id' => $category->category_id, 'name' => $category->category_name]])

    <div class="content">
        <div class="panel panel-white">
            <div class="panel-body exhibition-booking">
                <header class="booking-heading">
                    <div>
                        @if ($event)
                            <p class="text-muted">{{ $event->event_name }}</p>
                        @endif
                        <h1>{{ $category->category_name }}</h1>
                        @if ($category->category_descriptions)
                            <p>{{ $category->category_descriptions }}</p>
                        @endif
                    </div>
                    <a class="btn btn-default" href="{{ route('booking.index') }}">All categories</a>
                </header>

                @unless ($bookingOpen)
                    <p class="alert alert-info">Space booking is closed. Availability is shown for reference.</p>
                @endunless

                <div class="booking-grid">
                    @forelse ($spaces as $space)
                        @php
                            $totalBooths = (int) ($boothCounts[$space->pavilionID]->total ?? 0);
                            $availableBooths = (int) ($boothCounts[$space->pavilionID]->available ?? 0);
                            $reserved = $space->space_status === 'RESERVED';
                            $booked = $heldPavilions->has($space->pavilionID)
                                || ($totalBooths > 0 && $availableBooths === 0);
                            $available = $space->space_status === 'UNRESERVED' && !$booked;
                        @endphp
                        <article class="booking-option">
                            <h2>{{ $space->pavilionName }}</h2>
                            <p><strong>{{ number_format($space->pavilion_size, 2) }} m²</strong>
                                · {{ $totalBooths > 0 ? 'Individual booths' : 'Whole space' }}</p>
                            @if ($space->pavilionDescription)
                                <p>{{ $space->pavilionDescription }}</p>
                            @endif
                            <p>
                                <span class="booking-status {{ $available ? 'is-available' : '' }}">
                                    @if ($available && $totalBooths > 0)
                                        {{ number_format($availableBooths) }} of {{ number_format($totalBooths) }} booths available
                                    @elseif ($available)
                                        Available
                                    @elseif ($reserved)
                                        Reserved
                                    @else
                                        {{ $booked ? 'Fully booked' : 'Unavailable' }}
                                    @endif
                                </span>
                            </p>
                            @if ($bookingOpen && ($available || Auth::user()->account_type == 1))
                                <a class="btn {{ $available ? 'btn-primary' : 'btn-default' }}"
                                    href="{{ route('booking.show', $space->pavilionID) }}"
                                    aria-label="{{ $available ? 'Choose' : 'View' }} {{ $space->pavilionName }}">
                                    {{ $available ? ($totalBooths > 0 ? 'Choose booths' : 'View space') : 'View details' }}
                                    <i class="icon-arrow-right13" aria-hidden="true"></i>
                                </a>
                            @endif
                        </article>
                    @empty
                        <p class="alert alert-info">No spaces are listed in this category. Choose another category to keep browsing.</p>
                    @endforelse
                </div>

                @if ($pavilionCategories->isNotEmpty())
                    <nav class="booking-related" aria-labelledby="other-categories">
                        <h2 id="other-categories">Other categories</h2>
                        <div class="booking-actions">
                            @foreach ($pavilionCategories as $other)
                                <a class="btn btn-default" href="{{ route('event-booking-spaces.show', $other->category_id) }}">
                                    {{ $other->category_name }}
                                </a>
                            @endforeach
                        </div>
                    </nav>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
