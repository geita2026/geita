<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <!-- Title -->
    <title>{{ $event->eventTitle }} Registration</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords"
        content="GeMEMIS, Geita, mineral exhibition, madini, maonesho ya madini, exhibition space, space allocation, Tanzania">
    <meta name="description" content="{{ $event->eventTitle }},{{ $event->eventDescription }}">
    <meta name='copyright' content='Geita Mineral Exhibition'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/bootstrap.min.css') }}">
    <!-- Nice Select CSS -->
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/nice-select.css') }}">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/font-awesome.min.css') }}">
    <!-- icofont CSS -->
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/icofont.css') }}">
    <!-- Slicknav -->
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/slicknav.min.css') }}">
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/owl-carousel.css') }}">
    <!-- Datepicker CSS -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" /> --}}
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/datepicker.css') }}">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/animate.min.css') }}">
    <!-- Magnific Popup CSS -->
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/magnific-popup.css') }}">

    <!-- Medipro CSS -->
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/responsive.css') }}">

    <link rel="icon" href="{{ $event->eventPoster }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-forms.css') }}">
</head>

<body class="">
    <!-- Preloader -->
    @include('FrontEnd.Public_Pages.Layouts.PageLoader')

    <!-- Breadcrumbs -->
    <div class="overlay" style="background-image: url({{ $event->eventPoster }})">
        <div class="">
            <div class="bread-inner">
                <div class="">
                    <div class="col-md-12">
                        <h1 style="text-transform: capitalize;font-size:3vmax;color:white">{{ $event->eventTitle }}
                        </h1>
                        <p class=" text-justify  text-bold text-white" style="font-size: 1vmax;">
                            <b>{{ $event->eventDescription }}</b>
                        </p>
                        <br>
                        <div class=" col-md-5" style="font-size: 1vmax;font-family:monospace">
                            <p class=" text-justify text-white"><b>Event Date: </b>
                                {{ date('M d, Y', strtotime($event->startFrom)) }} -
                                {{ date('M d, Y', strtotime($event->endTo)) }}</p>
                            <p class=" text-justify text-white"><b>Registration Deadline:
                                </b>{{ date('M d, Y', strtotime($event->registrationDeadline)) }}</p>
                            <p class=" text-justify text-white"><b>Participants: </b>{{ $event->eventParticipants }}
                            </p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if (date('Y-m-d', strtotime($event->registrationDeadline)) < date('Y-m-d'))
            <div class=" alert alert-danger">
                <p>We regret to inform you that registration has officially closed. We appreciate your interest, but no
                    further applications will be accepted at this time. Please stay connected with us for future
                    opportunities and updates. Thank you for your understanding and continued support. We hope to see
                    you next time!
                </p>
            </div>
        @else
            <div class="col-md-12">
                <div class="form-group">
                    <form action="{{ route('participants.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class=" form-group col-md-12">
                            <div class=" col-md-12">
                                <p>Fields with <i class=" text-danger">*</i> Mark are required fields</p>
                            </div>
                            <div class=" form-group col-md-12">
                                @if (Session::get('success'))
                                    <div class=" alert alert-success">
                                        {{ Session::get('success') }}
                                    </div>
                                @elseif (Session::get('fail'))
                                    <div class=" alert alert-danger">
                                        {{ Session::get('fail') }}
                                    </div>
                                @endif
                            </div>
                            <div class=" row">
                                <div class=" form-group col-md-4">
                                    <label>Company/Insititution Name<i class=" text-danger">*</i></label>
                                    <input type="text" name="participantCompany" class=" form-control"
                                        placeholder="Company Name" value="{{ old('participantCompany') }}" required>
                                    <span class=" text-danger">
                                        @error('participantCompany')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <div class=" form-group col-md-4">
                                    <label>Contact Person Name<i class=" text-danger">*</i></label>
                                    <input type="text" name="participantName" class=" form-control"
                                        placeholder=" participant (Contact Personal) Name "
                                        value="{{ old('participantName') }}" required>
                                    <span class=" text-danger">
                                        @error('participantName')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class=" form-group col-md-4">
                                    <label>Contact Person Email<i class=" text-danger">*</i></label>
                                    <input type="text" name="participantEmail" class=" form-control"
                                        placeholder="Contact Personal Email" value="{{ old('participantEmail') }}"
                                        required>
                                    <span class=" text-danger">
                                        @error('participantEmail')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <div class=" form-group col-md-4">
                                    <label>Contact Person Phone Number<i class=" text-danger">*</i></label>
                                    <input type="text" name="participantPhone" class=" form-control"
                                        placeholder=" Contact Personal Phone" value="{{ old('participantPhone') }}"
                                        required>
                                    <span class=" text-danger">
                                        @error('participantPhone')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class=" form-group col-md-4">
                                    <label>Contact Personal Title<i class=" text-danger">*</i></label>
                                    <input type="text" name="participantTitle" class=" form-control"
                                        placeholder=" Contact Personal Title" value="{{ old('participantTitle') }}"
                                        required>
                                    <span class=" text-danger">
                                        @error('participantTitle')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <div class=" form-group col-md-12">
                                    <label>Area of Interest or Targeted Companies (Please specify the sectors or
                                        specific companies you are interested in engaging with during
                                        <b>{{ $event->eventTitle }}</b>.)</label>
                                    <textarea name="areaOfInterest" class=" form-control" placeholder=" Write here...">{{ old('companyProfile') }}</textarea>
                                    <span class=" text-danger">
                                        @error('areaOfInterest')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                            </div>

                            <div class=" col-md-12 text-right">
                                <button type="submit" class=" btn btn-primary">Register <i
                                        class=" fa fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
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
                url: '{{ route('search_sectors') }}', // URL for the AJAX request
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
                                id: item.sectorID,
                                text: item.sectorName,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search sector . . .',
            minimumInputLength: 0,
        });
    });
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
