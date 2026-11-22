<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <!-- Title -->
    <title>Prefair Seminar Feedback</title>
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
                            Prefairseminar Feedback Form.
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="col-md-12">
            <p class=" text-justify">
                Kindly provide feedback on the prefair seminar provided.
            </p>
            <div class="form-group">
                <form action="{{ route('prefair-semina-feedback-reg') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class=" form-group col-md-12">

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
                            <div class=" form-group col-md-12">
                                <label>Name (Optional)</label>
                                <input type="text" name="programName" class=" form-control"
                                    placeholder="Name"
                                    value="{{ old('programName') }}">
                                <span class=" text-danger">
                                    @error('programName')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-12">
                                <label>Did you saticified ?<i
                                        class=" text-danger">*</i></label><br>
                                <select name="saticifactionLevel" required class=" form-control"
                                     style="width: 200%">
                                    <option value="Very Satisfied">Very Satisfied</option>
                                    <option value="Satisfied">Satisfied</option>
                                    <option value="Neutral">Neutral</option>
                                    <option value="Dissatisfied">Dissatisfied</option>
                                    <option value="Very Dissatisfied">Very Dissatisfied</option>
                                </select>
                                <span class=" text-danger">
                                    @error('saticifactionLevel')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div><br>


                            <div class=" form-group col-md-12">
                                <label>Write your comment here</label>
                                <textarea name="attendeeFeedback" class=" form-control" placeholder=" Write here...">{{ old('attendeeFeedback') }}</textarea>
                                <span class=" text-danger">
                                    @error('attendeeFeedback')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                        </div>

                        <div class=" col-md-12 text-right">
                            <button type="submit" class=" btn btn-primary">Submit Feedback <i
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
