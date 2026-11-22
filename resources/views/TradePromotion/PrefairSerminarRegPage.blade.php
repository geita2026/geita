<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <!-- Title -->
    <title>Prefair Seminar</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords"
        content="GeMEMIS, Geita, mineral exhibition, madini, maonesho ya madini, exhibition space, space allocation, Tanzania">
    <meta name="description" content="{{ $liveEvent->eventTitle }},{{ $liveEvent->eventDescription }}">
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
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/datepicker.css') }}">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/animate.min.css') }}">
    <!-- Magnific Popup CSS -->
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/magnific-popup.css') }}">

    <!-- Medipro CSS -->
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('frontEndAssets/css/responsive.css') }}">

    <link rel="icon" href="{{ asset('images/Logo.png') }}">
</head>

<body class="">
    <!-- Preloader -->
    @include('FrontEnd.Public_Pages.Layouts.PageLoader')
    <!-- Breadcrumbs -->
    <div class="overlay">
        <div class="">
            <div class="bread-inner">
                <div class="">
                    <div class="col-md-12">
                        <h1 style="text-transform: capitalize;font-size:3vmax;color:white">
                            {{ $liveEvent->event_name }}<br>
                            Prefairseminar Registration Form.
                        </h1>
                        <p class=" text-justify  text-bold text-white" style="font-size: 1vmax;">
                            Exhibitors, seize this opportunity to enhance your presence! Register now for the exclusive
                            pre-fair seminar at SabaSaba. Gain invaluable insights and maximize your exhibition impact.
                            Don't miss out on this essential preparation for a successful event!
                        </p>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="col-md-12">
            <p class=" text-justify">
                The TSRN number is generated through the GeMEMIS - Geita Mineral Exhibition Management Information System. To find it, navigate
                to the "Companies" section, select "Company Registration," and view your list of registered companies.
                Copy the TSRN number from the relevant company. **Note:** Only companies that have already booked space
                are eligible to register.
            </p>
            <div class="form-group">
                <form action="{{ route('save-prefair-semina-data') }}" method="POST" enctype="multipart/form-data">
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
                            <div class=" form-group col-md-6">
                                <label>Service Registration Number<i class=" text-danger">*</i></label>
                                <input type="text" name="TSRN" class="form-control"
                                    placeholder="Service Registration Number (TSRN)"
                                    value="{{ old('TSRN') }}" required>
                                <span class=" text-danger">
                                    @error('TSRN')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class=" form-group col-md-6">
                                <label>Contact Person Name<i class=" text-danger">*</i></label>
                                <input type="text" name="fullName" class=" form-control"
                                    placeholder=" participant (Contact Personal) Full Name "
                                    value="{{ old('fullName') }}" required>
                                <span class=" text-danger">
                                    @error('fullName')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>Contact Person Email</label>
                                <input type="text" name="email" class=" form-control"
                                    placeholder="Contact Personal Email" value="{{ old('email') }}" required>
                                <span class=" text-danger">
                                    @error('email')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class=" form-group col-md-6">
                                <label>Contact Person Phone Number<i class=" text-danger">*</i></label>
                                <input type="text" name="phoneNumber" class=" form-control"
                                    placeholder=" Contact Personal Phone" value="{{ old('phoneNumber') }}" required>
                                <span class=" text-danger">
                                    @error('phoneNumber')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            {{-- <div class=" form-group col-md-12">
                                    <label>Area of Interest or Targeted Companies (Please specify the sectors or
                                        specific companies you are interested in engaging with during
                                        <b>{{ $liveEvent->eventTitle }}</b>.)</label>
                                    <textarea name="areaOfInterest" class=" form-control" placeholder=" Write here...">{{ old('companyProfile') }}</textarea>
                                    <span class=" text-danger">
                                        @error('areaOfInterest')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div> --}}

                        </div>

                        <div class=" col-md-12 text-right">
                            <button type="submit" class=" btn btn-primary">Register <i
                                    class=" fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div>
    </div>

    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>
