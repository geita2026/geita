<div class="page-header">
    <div class="page-header-content">
        @include('layouts.heading-elements')
    </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="icon-home position-left" aria-hidden="true"></i>Home</a></li>
            @if (isset($bookingCategory))
                <li><a href="{{ route('booking.index') }}">Space booking</a></li>
                @if (isset($bookingPavilion))
                    @if (filled($bookingCategory['id']))
                        <li><a href="{{ route('event-booking-spaces.show', $bookingCategory['id']) }}">{{ $bookingCategory['name'] }}</a></li>
                    @endif
                    <li class="active" aria-current="page">{{ $bookingPavilion }}</li>
                @else
                    <li class="active" aria-current="page">{{ $bookingCategory['name'] }}</li>
                @endif
            @else
                <li class="active" aria-current="page">Space booking</li>
            @endif
        </ul>
        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>
</div>
