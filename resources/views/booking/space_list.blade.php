<x-app-layout>
    <title>{{ env('APP_NAME') }} | Space Booking</title>

    <style>
        .space-card {
            border-radius: 12px;
            padding: 16px;
            transition: all .25s ease;
            cursor: pointer;
            min-height: 160px;
            box-shadow: 0 6px 18px rgba(0,0,0,.06);
        }

        .space-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0,0,0,.12);
        }

        .space-available {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-left: 6px solid #2e7d32;
        }

        .space-booked {
            background: linear-gradient(135deg, #eceff1, #cfd8dc);
            border-left: 6px solid #546e7a;
        }

        .space-progress {
            background: linear-gradient(135deg, #fffde7, #fff9c4);
            border-left: 6px solid #f9a825;
        }

        .space-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .space-meta {
            font-size: 13px;
            margin-top: 6px;
        }

        .space-status {
            margin-top: 12px;
            font-size: 13px;
            font-weight: 600;
        }

        .category-bar a {
            margin: 4px;
        }
    </style>

    <!-- ===================== PAGE HEADER ===================== -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>

        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="icon-home position-left"></i> Home
                    </a>
                </li>
                <li class="active">Space Booking</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    <!-- ===================== CONTENT ===================== -->
    <div class="content">
        <div class="panel panel-white">

            <!-- PANEL HEADER -->
            <div class="panel-heading text-center">
                @php $key = 0; @endphp
                @foreach ($spaces as $category)
                    @php $key++; @endphp
                @endforeach

                @if ($key == 0)
                    <h4 class="text-danger text-bold">
                        {{ $category->category_name ?? 'No' }} SPACES REGISTERED
                    </h4>
                @else
                    <h4 class="text-bold text-uppercase">
                        Spaces under {{ $category->category_name }} Category
                    </h4>
                    <p class="text-muted">
                        <span class="label bg-green">Available</span>
                        <span class="label bg-warning">Partially Booked</span>
                        <span class="label bg-slate">Fully Booked</span>
                    </p>
                @endif
            </div>

            <!-- PANEL BODY -->
            <div class="panel-body">

                <!-- CATEGORY FILTER -->
                <div class="category-bar mb-20">
                    <i class="icon-info3 text-info"></i>
                    You may view spaces from other categories:
                    <br><br>

                    @foreach ($pavilionCategories as $category)
                        <a href="{{ route('event-booking-spaces.show', ['event_booking_space' => $category->category_id]) }}"
                           class="btn btn-default text-primary btn-rounded">
                            {{ $category->category_name }}
                        </a>
                    @endforeach
                </div>

                <!-- SPACES GRID -->
                <div class="row">

                    @foreach ($spaces as $data)

                        {{-- Count booked booths --}}
                        @php $a = 0; @endphp
                        @foreach ($bookedBooths as $booth)
                            @if ($booth->pavilionID == $data->pavilionID)
                                @php $a++; @endphp
                            @endif
                        @endforeach

                        {{-- Count booked pavilion --}}
                        @php $i = 0; @endphp
                        @foreach ($bookedPavilions as $status)
                            @if ($status->pavilionID == $data->pavilionID)
                                @php $i++; @endphp
                            @endif
                        @endforeach

                        @php
                            $cardClass = 'space-available';
                            if ($data->numberOfPartitions > 0) {
                                if ($data->numberOfPartitions == $a) {
                                    $cardClass = 'space-booked';
                                } else {
                                    $cardClass = 'space-progress';
                                }
                            } else {
                                if ($i > 0) {
                                    $cardClass = 'space-booked';
                                }
                            }
                        @endphp

                        <div class="col-md-3 col-sm-6 mb-20">
                            <a href="@if ($i == 0 || Auth::user()->account_type == 1)
                                {{ route('booking.show', ['booking' => $data->pavilionID]) }}
                                @else # @endif"
                               title="{{ $data->pavilionDescription }}"
                               data-popup="tooltip-custom">

                                <div class="space-card {{ $cardClass }}">
                                    <div class="space-title">
                                        {{ $data->pavilionName }}
                                    </div>

                                    <div class="space-meta">
                                        Size:
                                        {{ number_format($data->pavilion_size, 2) }} m²
                                    </div>

                                    <div class="space-status">
                                        @if ($data->numberOfPartitions > 0)
                                            @if ($data->numberOfPartitions == $a)
                                                Status: BOOKED
                                            @else
                                                Status: {{ number_format(($a / $data->numberOfPartitions) * 100, 2) }}% BOOKED
                                            @endif
                                        @else
                                            @if ($i == 0)
                                                Status: AVAILABLE
                                            @else
                                                Status: BOOKED
                                            @endif
                                        @endif
                                    </div>
                                </div>

                            </a>
                        </div>

                    @endforeach

                </div>

                @if ($key == 0)
                    <div class="alert alert-danger mt-20">
                        No spaces are registered under this category.
                        Please explore other categories to find available exhibition spaces.
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
