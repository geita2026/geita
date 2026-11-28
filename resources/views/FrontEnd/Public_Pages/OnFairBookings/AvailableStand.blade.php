<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ config('app.name') }} | {{ $pavilion->pavilionName }}</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
    <link rel="stylesheet" href="{{ asset('assets/css/exhibition-booking.css') }}">
</head>

<body>
    @include('FrontEnd.Public_Pages.Layouts.PageLoader')

    <header class="header">
        @include('FrontEnd.Public_Pages.Layouts.TopHeader')
        @include('FrontEnd.Public_Pages.Layouts.Header')
    </header>

    <div class="breadcrumbs overlay" style="background-image: url({{ asset('images/geita/geita-mine.jpg') }})">
        <div class="container">
            <div class="bread-inner">
                <div class="row">
                    <div class="col-12">
                        <h2>{{ $pavilion->pavilionName }}</h2>
                        @if ($pavilion->pavilionDescription)
                            <ul class="bread-list">
                                <li>{{ $pavilion->pavilionDescription }}</li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="blog section" id="blog">
        <div class="container">
            <div class="card-body">
                @auth
                    <div class="mb-4">
                        <a class="btn btn-primary text-white" href="{{ route('booking.show', ['booking' => $pavilion->pavilionID]) }}">
                            Continue with Booking <i class="icon icon-arrow-right13"></i>
                        </a>
                    </div>
                @else
                    <div class="mb-4">
                        Please <a href="{{ route('login') }}" class="text-primary">sign in</a> or
                        <a href="{{ route('registration') }}" class="text-primary">create an account</a>
                        to select and book available booths in this pavilion.
                    </div>
                @endauth

                <div class="pavilion-blueprint-section">
                    @include('space.FloorPlans.PavilionBlueprint', [
                        'interactiveBookingMap' => false,
                    ])
                </div>
            </div>
        </div>
    </section>

    @include('FrontEnd.Public_Pages.Layouts.Footer')
</body>

</html>
