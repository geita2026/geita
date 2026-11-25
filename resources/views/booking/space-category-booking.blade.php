<x-app-layout>
    <title>{{ config('app.name') }} | Space booking</title>
    <link rel="stylesheet" href="{{ asset('assets/css/exhibition-booking.css') }}">
    @include('booking.navigation')

    <div class="content">
        <div class="panel panel-white">
            <div class="panel-body exhibition-booking">
                <header class="booking-heading">
                    <div>
                        @if (!empty($event?->event_name) || !empty($event_name))
                            <p class="text-muted">{{ $event->event_name ?? $event_name }}</p>
                        @endif
                        <h1>Choose a space category</h1>
                    </div>
                </header>

                @if (!($bookingOpen ?? true))
                    <p class="alert alert-info">Space booking is closed. Contact the exhibition organizer for updates.</p>
                @elseif ($space_categories->isEmpty())
                    <p class="alert alert-info">No space categories are available for this exhibition yet.</p>
                @else
                    @cannot('BOOK_SPACE')
                        <p class="alert alert-info">Your account can view categories. Contact the exhibition organizer for booking access.</p>
                    @endcannot
                    <div class="booking-grid">
                        @foreach ($space_categories as $item)
                            <article class="booking-option">
                                <h2>{{ $item->category_name }}</h2>
                                @if ($item->category_descriptions)
                                    <p>{{ $item->category_descriptions }}</p>
                                @endif
                                @if ($item->cost_one_msq !== null && $item->type_of_currency)
                                    <p class="booking-rate"><strong>{{ $item->type_of_currency }} {{ number_format($item->cost_one_msq, 2) }}</strong> / m²</p>
                                @endif
                                @can('BOOK_SPACE')
                                    <a href="{{ route('event-booking-spaces.show', $item->category_id) }}" class="btn btn-default"
                                        aria-label="View spaces in {{ $item->category_name }}">
                                        View spaces <i class="icon-arrow-right13" aria-hidden="true"></i>
                                    </a>
                                @endcan
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
