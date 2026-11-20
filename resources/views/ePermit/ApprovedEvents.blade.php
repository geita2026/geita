<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <!-- Title -->
    <title>{{ env('APP_NAME') }} | Approved Events</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
    <style>
        .changeColor:hover {
            background-color: rgb(214, 212, 212);
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
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('ePermitFiles/ePermit.jpeg') }})">
        <div class="container">
            <div class="bread-inner">

                <div class="row">
                    <div class="col-12">
                        <h2>APPROVED TRADE PROMOTION EVENTS</h2>

                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->



    <div class=" col-md-12">
        <!-- Start Blog Area -->
        <section class="blog section" id="blog">
            <div class="card-body">
                <div class="row">

                    <div class="col-lg-12">
                        <div class="">
                            
                            <div class="text-justify">
                                Section 5(1) of the Tanzania Trade Development Authority Act 2009 mandates that all
                                organizers of trade promotion events, including Trader fairs, Trade Missions, and
                                Business Forums, obtain a permit from the Tanzania Trade Development Authority. This
                                requirement ensures that such events are conducted legally, safely, and in accordance
                                with local and international laws and regulations. Now bellow are approved Trade Promotion events.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row col-md-12 form-group">
                    <br>
                    @foreach ($approvedEvents as $item)
                        <div class="col-lg-4 col-md-6 col-12" style="padding: 0.5%">
                            <!-- Single Blog -->
                            <div class="single-news mt-20" style="border: 1px solid rgb(199, 198, 198);">
                                <div class="news-body">
                                    <div class="news-content">
                                        <a href="#">
                                            <h2>{{ $item->eventTitle }}</h2>
                                            <br>
                                            <div>
                                                <b>Category: </b>{{ $item->promotionEventTypeName }}
                                            </div>
                                            <br>
                                            <div>
                                                <i class=" fa fa-calendar text-primary"></i>
                                                {{ date('M d, Y', strtotime($item->startFrom)) }} <i
                                                    class=" fa fa-arrow-right"></i>
                                                {{ date('M d, Y', strtotime($item->endDate)) }}
                                            </div>
                                            <br>
                                            <div class="">
                                                @if ($item->startFrom>date('Y-m-d'))
                                                    <label class=" label bg-success text-uppercase p-1" style="color: white">EVENT SCHEDULED</label>
                                                    @else
                                                    <label class=" label bg-danger p-1 " style="color: white">EVENT COMPLETED</label>
                                                @endif

                                                <label></label>
                                            </div>
                                            <br>
                                            <div>
                                                <i class=" fa fa-map-marker text-bold text-primary"></i>
                                                {{ $item->venueAddress }}
                                            </div>
                                            <br>
                                            <p class="text">
                                                <b>Theme:</b> <q class=" text-primary">{{ substr($item->eventTheme,0,50) }}</q>
                                            </p>
                                            <br>
                                            <div class="text text-small text-justify text-capitalize" style="height: 200px;">
                                                <hr>
                                                {{ substr($item->eventObjectives, 0, 200) }} ...
                                            </div>
                                        </a>

                                        @if ($item->eventWebsite != 'https://')
                                            <div class=" text-center">
                                                <a href="{{ $item->eventWebsite }}" class=" text-primary" target="_newtab">Visit website for more information <i
                                                    class=" fa fa-arrow-right"></i></a>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <!-- End Single Blog -->
                        </div>
                    @endforeach
                </div>
                <div class="col-md-12 text-right">
                    {{ $approvedEvents->links() }}
                </div>
            </div>
        </section>
        <!-- End Blog Area -->
    </div>

    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>

<script>
    $(document).ready(function() {
        $('#search-select').select2({
            ajax: {
                url: '{{ route('search_approved_events') }}', // URL for the AJAX request
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // Search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: '{{ env('APP_URL') }}/approved_events/',
                                text: item.eventTitle + ' || ',
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Trade Promotion Event . . .',
            minimumInputLength: 0,
        });
    });
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
