<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booking</title>

    <style>
        .booking-header {
            margin-bottom: 25px;
        }

        .booking-title {
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1f2937;
            text-align: center;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 22px;
        }

        .category-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 22px;
            height: 100%;
            box-shadow: 0 6px 18px rgba(0,0,0,.06);
            border: 1px solid #e5e7eb;
            transition: all .25s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .category-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0,0,0,.08);
        }

        .category-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #ffffff;
            margin-bottom: 14px;
        }

        .category-name {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #111827;
            margin-bottom: 8px;
        }

        .category-desc {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.6;
            flex-grow: 1;
        }

        .category-action {
            margin-top: 18px;
            font-size: 13px;
            font-weight: 600;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .empty-state {
            background: #fff1f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
        }
    </style>

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>

        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li>
                    <a href="{{ route('booking.index') }}">
                        <i class="icon-stack-check position-left"></i> Booking
                    </a>
                </li>
                <li class="active">Available Spaces</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="content">
        <div class="panel panel-white">
            <div class="panel-body">

                {{-- HEADER --}}
                <div class="booking-header">
                    <div class="booking-title">
                        {{ $event->event_name ?? '' }} – Registered Space Categories
                    </div>
                </div>

                {{-- CATEGORY GRID --}}
                <div class="category-content">
                    @if (isset($bookingOpen) && !$bookingOpen)
                        <div class="empty-state">Booking is not open. Contact the exhibition organizer for updates.</div>
                    @elseif ($space_categories->count() > 0)
                        <div class="category-grid">
                            @foreach ($space_categories as $item)
                                <a href="{{ route('event-booking-spaces.show', ['event_booking_space' => $item->category_id]) }}"
                                   class="text-decoration-none text-dark">

                                    <div class="category-card">

                                        <div>
                                            <div class="category-icon {{ $item->background_color }}">
                                                <i class="{{ $item->category_icon }}"></i>
                                            </div>

                                            <div class="category-name">
                                                {{ $item->category_name }}
                                            </div>

                                            <div class="category-desc">
                                                {{ $item->category_descriptions }}
                                            </div>
                                        </div>

                                        <div class="category-action">
                                            View & Book →
                                        </div>

                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            No space categories are registered for this event, or booking is not yet open.
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
