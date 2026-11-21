<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Tanzania Expo2025 Osaka Japan Participation registration Form">
    <meta name="keywords" content="Register, Expo, Osaka, expo2025,tantrade,maonesho">
    <meta name="author" content="Geita Mineral Exhibition Secretariat">
    <link rel="icon" href="{{ asset('images/nembo.png') }}" type="image/gif" sizes="16x16">
    <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/components.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/colors.css') }}" rel="stylesheet" type="text/css">
    <link rel="icon" href="{{ asset('ExpoFolder/ExpoIcon.png') }}" type="image/gif" sizes="16x16">
    <!-- Core JS files -->
    <script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
    <!-- /core JS files -->
</head>

<body class="login-container">

    <!-- Main navbar -->

    <div class="navbar navbar-inverse bg-info">
        <div class="navbar-header ">

            <h2 class="navbar-brand" href="#">
                EXPO2025 OSAKA JAPAN TANZANIA REGISTRATION FORM
            </h2>

        </div>


    </div>
    <!-- /main navbar -->

    <!-- Page container -->
    <div class="page-container">

        <!-- Page content -->
        <div class="page-content">

            <!-- Main content -->
            <div class="content-wrapper">
                <!-- Content area -->
                <div class="content">
                    <form action="{{ route('third_part_reg') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12 col-lg-offset-0">
                                <div class="panel registration-form">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <div class="border-success text-success">

                                                <div class=" col-md-12">
                                                    <img src="{{ asset('ExpoFolder/OfficialLogo.jpeg') }}"
                                                        style="width: 30%;">
                                                </div>

                                            </div>

                                        </div>
                                        <div class=" col-md-12">
                                            @if (Session::get('fail'))
                                                <div class="alert alert-danger alert-bordered">
                                                    <button type="button" class="close"
                                                        data-dismiss="alert"><span>&times;</span><span
                                                            class="sr-only">Close</span></button>
                                                    {{ Session::get('fail') }}
                                                </div>
                                            @elseif (Session::get('success'))
                                                <div class="alert alert-success alert-bordered">
                                                    <button type="button" class="close"
                                                        data-dismiss="alert"><span>&times;</span><span
                                                            class="sr-only">Close</span></button>
                                                    <span class="text-semibold">
                                                        {{ Session::get('success') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class=" form-group">
                                            <div class=" form-group">
                                                <p class=" text-justify">
                                                <h3><b>Welcome to the <q>Tanzania Business, Investment and Tourism
                                                        Forum</q> at EXPO 2025 Osaka, Japan!</b></h3>

                                                <p><b>Theme:</b> Discover Tanzania as a Gateway to Investment, Trade,
                                                    and
                                                    Tourism Excellence</p>
                                                <p>
                                                    <b>Venue:</b> Westin Hotel
                                                </p>
                                                <p>
                                                    <b>Date:</b> 26<sup>th</sup> May, 2025 9am morning to 3pm afternoon.
                                                </p>

                                                <p>
                                                    <b>Sector of Interest:</b> Energy & Mining, Culture, Health,
                                                    Tourism,
                                                    Infrastracture , Agriculture , Transport & Logistics, Livestock &
                                                    Fisheries, Financial Services and Manufacturing.
                                                </p>

                                                <p style="text-align: justify">
                                                    Join us in discovering the incredible opportunities that Tanzania
                                                    has to offer. By registering your details in the form below, you’re
                                                    taking the first step toward building powerful partnerships that
                                                    drive mutual growth and lasting prosperity.

                                                    Don’t miss out on the chance to connect, collaborate, and thrive
                                                    together! We look forward to embarking on this exciting journey with
                                                    you!
                                                </p>
                                                </p>
                                            </div>
                                            <div class=" row form-group">
                                                <div class=" col-md-4">
                                                    <label>Your Institution / company Name<i class=" text-danger">*</i>
                                                    </label>
                                                    <input type="text" name="companyName" class="form-control"
                                                        placeholder="Institution/company Name"
                                                        value="{{ old('companyName') }}" required autofocus>
                                                    <span class=" text-danger">
                                                        @error('companyName')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Your Institution / company email <i
                                                            class=" text-danger">*</i>
                                                    </label>
                                                    <input type="text" class="form-control" name="companyEmail"
                                                        placeholder="Eg. demo@gmail.com"
                                                        value="{{ old('companyEmail') }}" required autofocus>
                                                    <span class=" text-danger">
                                                        @error('companyEmail')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Select company sector <i class=" text-danger">*</i></label>
                                                    <select name="companySector" class=" form-control" required
                                                        autofocus id="companySector">
                                                        <option value="{{ old('companySector') }}">
                                                            {{ old('companySector') ?? '- - - Select type of participation - - -' }}
                                                        </option>
                                                        <option value="Science& Technology">Science& Technology
                                                        </option>
                                                        <option value="Energy & Mining">Energy & Mining</option>
                                                        <option value="Culture">Culture</option>
                                                        <option value="Health">Health</option>
                                                        <option value="Tourism">Tourism</option>
                                                        <option value="Infrastructure">Infrastructure</option>
                                                        <option value="Agriculture">Agriculture</option>
                                                        <option value="Transport & Logistics">Transport & Logistics
                                                        </option>
                                                        <option value="Transport & Logistics">Livestock & Fisheries
                                                        </option>
                                                        <option value="Financial Services">Financial Services</option>
                                                        <option value="Manufacturing">Manufacturing</option>
                                                    </select>
                                                    <span class=" text-danger">
                                                        @error('typeOfParticipation')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="form-group col-md-4">
                                                    <label>Your country <i class=" text-danger">*</i></label>
                                                    <select name="country" class=" form-control" required autofocus
                                                        id="typeOfParticipation">
                                                        <option value="{{ old('country') }}">
                                                            {{ old('country') ?? '- - - Select your country - - -' }}
                                                        </option>
                                                        @foreach ($countries as $item)
                                                            <option value="{{ $item->country_name }}">
                                                                {{ $item->country_name }} [
                                                                {{ $item->country_short_code }} ]</option>
                                                        @endforeach
                                                    </select>
                                                    <span class=" text-danger">
                                                        @error('typeOfParticipation')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>What are you looking from Tanzania ? <i
                                                            class=" text-danger">*</i></label>
                                                    <select name="typeOfParticipation" class=" form-control" required
                                                        autofocus id="typeOfParticipation">
                                                        <option value="{{ old('typeOfParticipation') }}">
                                                            {{ old('typeOfParticipation') ?? '- - - Select what are you looking from Tanzania - - -' }}
                                                        </option>
                                                        <option value="Invest in Tanzania">
                                                            Invest in Tanzania</option>
                                                        <option value="Patner with Tanzania in doing business">
                                                            Patner with Tanzania in doing business</option>

                                                    </select>
                                                    <span class=" text-danger">
                                                        @error('typeOfParticipation')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>

                                                <div class=" col-md-4">
                                                    <label>Institution / company profile<i class=" text-danger">*</i>
                                                        <span class=" text-muted">( Not greater than 2,000
                                                            characters)</span></label>
                                                    <textarea name="companyProfile" placeholder="Write your company profile here" class=" form-control" required
                                                        rows="1">{{ old('companyProfile') }}</textarea>
                                                    <span class=" text-danger">
                                                        @error('companyProfile')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>

                                                {{-- <div class="form-group col-md-4" id="specificProgram">
                                                    <label>Choose program of interest to meet with Tanzanians</label>
                                                    <select name="programOfInterest" class=" form-control" autofocus>
                                                        <option value="{{ old('programOfInterest') }}">
                                                            {{ old('programOfInterest') ?? '- - - Select program of interest - - -' }}
                                                        </option>
                                                        <option
                                                            value="Tanzania Unforgettable Experience [25th April – 6th May, 2025]">
                                                            Tanzania Unforgettable Experience [ 25th April – 6th May,
                                                            2025 ]</option>
                                                        <option
                                                            value="Infrastructure for sustainable development [15th May- 26th May, 2025]">
                                                            Infrastructure for sustainable development [ 15th May- 26th
                                                            May, 2025 ]</option>
                                                        <option value="Tanzania National Day [25th May, 2025]">Tanzania
                                                            National Day [ 25th May, 2025 ] </option>
                                                        <option
                                                            value="Tanzania Business, Investment and Tourism Forum [26th May, 2025]">
                                                            Tanzania Business, Investment and Tourism Forum [ 26th May,
                                                            2025 ]</option>
                                                        <option
                                                            value="Building Better Tomorrow- Tanzania Focus in the Agriculture, livestock, Fisheries and Blue Economy [5th June- 16th June, 2025]">
                                                            Building Better Tomorrow- Tanzania Focus in the Agriculture,
                                                            livestock, Fisheries and Blue Economy [ 5th June- 16th June,
                                                            2025 ]</option>
                                                        <option
                                                            value="The Transformation of Health sector in Tanzania [20th June- 1st July, 2025]">
                                                            The Transformation of Health sector in Tanzania [ 20th June-
                                                            1st July, 2025 ]</option>
                                                        <option
                                                            value="Unity in diversity- Kiswahili language in promoting unity and cultural diversity [1st July- 7th July, 2025]">
                                                            Unity in diversity- Kiswahili language in promoting unity
                                                            and cultural diversity [ 1st July- 7th July, 2025 ]</option>
                                                        <option
                                                            value="Women Empowerment for Future Society [1st Aug- 12th Aug, 2025]">
                                                            Women Empowerment for Future Society [ 1st Aug- 12th Aug,
                                                            2025 ]</option>
                                                        <option
                                                            value="Clean Energy for Shared Future [17th Sept- 28 Sept, 2025]">
                                                            Clean Energy for Shared Future [ 17th Sept- 28 Sept, 2025 ]
                                                        </option>
                                                        <option
                                                            value="Innovation and inventions for prosperity [02nd Oct- 12th  Oct, 2025]">
                                                            Innovation and inventions for prosperity [ 02nd Oct- 12th
                                                            Oct, 2025 ]</option>
                                                    </select>
                                                    <span class=" text-danger">
                                                        @error('programOfInterest')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div> --}}

                                            </div>
                                            <div class=" row form-group">
                                                <div class="form-group col-md-3">
                                                    <label>Representative full name <i
                                                            class=" text-danger">*</i></label>
                                                    <input type="text" name="firstName" class="form-control"
                                                        placeholder="Full name" required
                                                        value="{{ old('firstName') }}" required autofocus>
                                                    <span class=" text-danger">
                                                        @error('firstName')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>
                                                <div class=" col-md-3">
                                                    <label>Sex<i class=" text-danger">*</i></label>
                                                    <select name="sex" class=" form-control" required autofocus>
                                                        <option value="{{ old('sex') }}">
                                                            {{ old('sex') ?? '- - - Select Gender - - -' }}</option>
                                                        <option value="MALE">MALE</option>
                                                        <option value="FEMALE">FEMALE</option>
                                                    </select>
                                                    <span class=" text-danger">
                                                        @error('sex')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>

                                                <div class=" col-md-3">
                                                    <label>Email Address<i class=" text-danger">*</i> </label>
                                                    <input type="email" name="emailAddress"
                                                        placeholder="demo@gmail.com" class=" form-control" required
                                                        autofocus value="{{ old('emailAddress') }}">
                                                    <span class=" text-danger">
                                                        @error('emailAddress')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>

                                                <div class=" col-md-3">
                                                    <label>Phone Number<i class=" text-danger">*</i> </label>
                                                    <input type="text" name="phoneNumber" class="form-control"
                                                        placeholder="eg: +255xxxxxxx"
                                                        value="{{ old('phoneNumber') }}" required autofocus>
                                                    <span class=" text-danger">
                                                        @error('phoneNumber')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>
                                            </div>

                                            <div class=" row">
                                                <div class=" col-md-12 form-group" id="anyOtherDescription">
                                                    <label>Please provide other details of your intended companies or
                                                        institutions to facilitate more effective meeting or any other details you want to share with us.
                                                        (Optional)</label>
                                                    <textarea name="anyOtherDescription" class=" form-control" rows="2"
                                                        placeholder="Extra description of your target if available">{{ old('anyOtherDescription') }}</textarea>
                                                    <span class=" text-danger">
                                                        @error('anyOtherDescription')
                                                            {{ $message }}
                                                        @enderror
                                                </div>
                                            </div>



                                        </div>

                                        <div class="text-right col-md-12 form-group">
                                            <button type="submit" id="submitBtn"
                                                class="btn bg-info   btn-ladda btn-ladda-spinner">
                                                Save Data<b><i class=" icon-arrow-right13"></i></b>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Footer -->
                    @include('layouts.footer')
                    <!-- /footer -->
                </div>
                <!-- /content area -->
            </div>
            <!-- /main content -->
        </div>
        <!-- /page content -->

    </div>
    <!-- /page container -->

</body>

</html>
