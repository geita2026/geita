<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
        content="GeMEMIS — the Geita Mineral Exhibition Management Information System. Exhibitor registration, space allocation, invoicing, payment and check-in for the Geita mineral exhibitions.">
    <meta name="keywords"
        content="GeMEMIS, Geita, mineral exhibition, madini, maonesho ya madini, exhibition space, space allocation, Tanzania">
    <meta name="author" content="Geita Mineral Exhibition Secretariat">

    <title>{{ env('APP_NAME') }} | Self Registration</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    @include('WelcomePageLayouts.welcomeCss')
</head>

<body>
    <!-- Page container -->
    <div class="page-container">
        <!-- Page content -->
        <div class="page-content">

            <!-- Main content -->
            <div class="content-wrapper">
                <!-- HEADER MENU-->
                @include('WelcomePageLayouts.welcomeHeaderMenu')
                <!-- END HEADER MENU-->


                <!-- Search results -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-body">
                            <div class=" panel-heading">
                                <!-- == Exhibition Time Line == -->
                                @include('layouts.ExhibitionTime')
                                <!-- == Exhibition Time Line == -->
                                <h4 class=" text-center  text-bold" style="font-size:1.5vmax">
                                    SELF REGISTRATION FORM
                                </h4>
                                <p class=" text-muted text-center">
                                    <b>Note! if you don't have </b> <b class=" text-warning">Service
                                        Registration Number (TSRN)</b>. send whatsapp message / Normal Call Via <a
                                        href="tel: +255 733-002-014">+255 733 002 014</a> to provide it for you.
                                </p>
                            </div>
                            <div class=" panel-body">


                                <div class="col-lg-10 col-lg-offset-1">
                                    <div class=" registration-form p-10" style="border:1px solid rgb(224, 222, 222);">
                                        @if (Session::get('success'))
                                            <div class=" alert alert-success">
                                                {{ Session::get('success') }} <a href="{{ route('index') }}"
                                                    class=" btn btn-default"><i class=" icon-arrow-left12"></i> Go to
                                                    Login</a>
                                            </div>
                                        @endif

                                        @if (!session('verifiedTSRN'))
                                            <form action="{{ route('register') }}" method="POST">
                                                @csrf
                                                <div class=" row ">
                                                    <div class="form-group col-md-8 ">
                                                        <label>Service Registration Number (SRN)<i
                                                                class=" text-danger">*</i>
                                                            <a href="#" data-popup="popover"
                                                                title="Service Registration Number (SRN)"
                                                                data-trigger="hover"
                                                                data-content="The number issued after registering for an exhibition service. If you do not have it, contact the Exhibition Secretariat."><i
                                                                    class=" icon-question3"></i></a>
                                                        </label>
                                                        <input type="text" name="TSRN" class="form-control"
                                                            placeholder="Service Registration Number (SRN)"
                                                            value="{{ old('TSRN') }}">
                                                        <span class=" text-danger ">
                                                            @error('TSRN')
                                                                <b class=" text-uppercase">{{ $message }}</b>
                                                            @enderror
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 ">
                                                        <label class=" text-muted">Applying to next step will allow you
                                                            to inter
                                                            you detail</label><br>
                                                        <button
                                                            class=" btn btn-primary btn-ladda btn-ladda-spinner">Next <i
                                                                class=" icon-arrow-right13"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        @endif

                                        @if (session('verifiedTSRN'))
                                            <div class="alert alert-info alert-styled-left alert-bordered">
                                                <button type="button" class="close"
                                                    data-dismiss="alert"><span>&times;</span><span
                                                        class="sr-only">Close</span></button>
                                                <span class="text-semibold">Thank you our valuable Customer!</span>
                                                You are registered as <b>{{ session('companyName') }}</b>

                                            </div>
                                            <form action="{{ route('register') }}" method="POST">
                                                @csrf
                                                <div class=" form-group">
                                                    <div class=" row">
                                                        <div class=" col-md-12 form-group">
                                                            <input type="text" name="TSRN" class=" form-control"
                                                                value="{{ session('verifiedTSRN') }}" readonly>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>First Name<i class=" text-danger">*</i></label>
                                                            <input type="text" name="firstName" class="form-control"
                                                                placeholder="First Name"
                                                                value="{{ old('firstName') }}">
                                                            <span class=" text-danger">
                                                                @error('firstName')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>Middle Name</label>
                                                            <input type="text" name="middleName" class="form-control"
                                                                placeholder="Middle Name"
                                                                value="{{ old('middleName') }}">
                                                            <span class=" text-danger">
                                                                @error('middleName')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>Last Name<i class=" text-danger">*</i></label>
                                                            <input type="text" name="lastName" class="form-control"
                                                                placeholder="Last Name" value="{{ old('lastName') }}">
                                                            <span class=" text-danger">
                                                                @error('lastName')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                    </div>


                                                    <div class=" row">
                                                        <div class="form-group col-md-4">
                                                            <label>User Email <i class=" text-danger">*</i> </label>
                                                            <input type="text" class="form-control" name="userEmail"
                                                                placeholder="User Email"
                                                                value="{{ old('userEmail') }}">
                                                            <span class=" text-danger">
                                                                @error('userEmail')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>Phone Number <i class=" text-danger">*</i> </label>
                                                            <input type="text" class="form-control" id="phone"
                                                                name="phone_number" placeholder="255xxxxxxxxx"
                                                                value="{{ old('phone_number') }}">
                                                            <span class=" text-danger">
                                                                @error('phone_number')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>

                                                        <div class=" col-md-4">
                                                            <div class="form-group">
                                                                <div class="">
                                                                    <label>User Gender<i
                                                                            class=" text-danger">*</i></label><br>
                                                                    <label>
                                                                        <input type="radio" name="gender"
                                                                            value="1" class="styled">
                                                                        MALE
                                                                    </label>
                                                                    <label class=" ml-20">
                                                                        <input type="radio" value="0"
                                                                            name="gender" class="styled">
                                                                        FEMALE
                                                                    </label>
                                                                </div>

                                                                <span class=" text-danger">
                                                                    @error('gender')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class=" row">
                                                        <div class="form-group col-md-6">
                                                            <label>Password <i class=" text-danger">*</i> </label>
                                                            <input type="password" class="form-control"
                                                                name="password"
                                                                placeholder="* * * * * * * * * * * * * * * *"
                                                                value="{{ old('password') }}">
                                                            <span class=" text-danger">
                                                                @error('password')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label>Confirm Password <i class=" text-danger">*</i>
                                                            </label>
                                                            <input type="password" class="form-control"
                                                                id="phone" name="confirm_password"
                                                                placeholder="* * * * * * * * * * * * * * * *"
                                                                value="{{ old('confirm_password') }}">
                                                            <span class=" text-danger">
                                                                @error('confirm_password')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group col-4 text-right">
                                                        <a href="{{ route('index') }}"
                                                            class=" btn btn-default text-primary"><i
                                                                class=" icon-arrow-left12"></i>I have Account Go to
                                                            Login</a>
                                                        <button class=" btn btn-primary ml-20">Create Account</button>

                                                    </div>
                                                </div>


                                            </form>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- /search results -->

            </div>
            <!-- /main content -->

        </div>
        <!-- /page content -->
        @include('layouts.footer')

    </div>
    <!-- /page container -->

</body>

</html>

<!-- Core JS files -->
<script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
<!-- /core JS files -->
<!-- Theme JS files -->
<script type="text/javascript" src="{{ asset('assets/js/core/app.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/buttons/spin.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/buttons/ladda.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/components_buttons.js') }}"></script>
