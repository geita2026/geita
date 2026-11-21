<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="Site keywords here">
    <meta name="description" content="">
    <meta name='copyright' content=''>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>{{ env('APP_NAME') }} | Available Spaces</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
    <style>
        .hover-container {
            position: relative;
            display: inline-block;
        }

        .hover-message {
            visibility: hidden;
            background-color: rgba(241, 216, 75, 0.733);
            color: red;
            border: 1px solid red;
            text-align: center;
            border-radius: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            /* Adjust position above the element */
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            width: 100%;
        }

        .hover-container:hover .hover-message {
            visibility: visible;
            opacity: 1;
        }

        #flowPlan {
            border: 1px solid #ccc;
            overflow-y: auto;
            position: relative;
            background-color: #f4f4f4;
            cursor: url('https://cdn-icons-png.flaticon.com/512/1142/1142965.png'), auto;
            /* Custom Cursor */
        }
    </style>
</head>

<body>

    <!-- Preloader -->
    @include('FrontEnd.Public_Pages.Layouts.PageLoader')
    <!-- End Preloader -->


    <!-- Get Pro Button -->

    <!-- Header Area -->
    <header class="header">
        <!-- Topbar -->
        @include('FrontEnd.Public_Pages.Layouts.TopHeader')
        <!-- End Topbar -->
        <!-- Header Inner -->
        @include('FrontEnd.Public_Pages.Layouts.Header')
        <!--/ End Header Inner -->
    </header>
    <!-- End Header Area -->

    <!-- Breadcrumbs -->
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('images/geita/geita-mine.jpg') }})">
        <div class="container">
            <div class="bread-inner">

                <div class="row">
                    <div class="col-12">
                        <h2>{{ $pavilion->pavilionName }}</h2>
                        <ul class="bread-list">
                            <li><a href="#">{{ $pavilion->pavilionDescription }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <section class="blog section" id="blog">
        <div class=" col-md-12">
            <div class="card-body">

                @auth
                    <div class=" col-md-12 p-20">
                        <a class=" btn btn-primary text-white" href="{{ route('booking.show', ['booking' => $pavilion->pavilionID]) }}">Continue with Booking <i class="icon icon-arrow-right13"></i></a>
                    </div>
                @else
                    <div class="col-md-12 p-20">
                        Please <a href="{{ route('login') }}" class=" text-primary">click here</a> to login or Don't Have
                        account ?<a href="{{ route('registration') }}" class=" text-primary">
                            Create Account</a> to be able to book spaces of your interest.
                    </div>
                @endauth

                <br>
                <div class=" col-md-12 table-responsive">
                    @if (strtoupper($pavilion->pavilionName) == 'KARUME HALL')
                        @include('space.FloorPlans.KarumeHall')
                    @elseif (strtoupper($pavilion->pavilionName) == 'MANYARA PAVILION' ||
                            strtoupper($pavilion->pavilionName) == 'MIKUMI PAVILION')
                        @include('space.FloorPlans.40StandPavilions')
                    @elseif (strtoupper($pavilion->pavilionName) == 'KILIMANJARO HALL')
                        @include('space.FloorPlans.KilimanjaroHall')
                    @else
                        @include('space.FloorPlans.FourTierFloorMap')
                    @endif


                </div>

            </div>
        </div>
    </section>

    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>

<script>
    // Accessing the container and items inside it
    const container = document.getElementById('flowPlan');
    const items = container.querySelectorAll('.item');

    // Add hover effect to items
    items.forEach(item => {
        item.addEventListener('mouseenter', function() {
            item.classList.add('hovered');
        });
        item.addEventListener('mouseleave', function() {
            item.classList.remove('hovered');
        });

        // Add click event to each item for navigation or interaction
        item.addEventListener('click', function() {
            item.classList.toggle('highlight');
            alert('You clicked on ' + item.textContent);
        });
    });
</script>
