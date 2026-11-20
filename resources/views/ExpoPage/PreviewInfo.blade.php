@foreach ($data as $item)
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Tanzania Expo2025 Osaka Japan Participation registration Form">
        <meta name="keywords" content="Register, Expo, Osaka, expo2025,tantrade,maonesho">

        <link rel="icon" href="{{ asset('images/Logo.png') }}" type="image/gif" sizes="16x16">
        <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/css/components.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/css/colors.css') }}" rel="stylesheet" type="text/css">
        <!-- /global stylesheets -->

        <!-- Core JS files -->
        <script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
        <!-- /core JS files -->

        <!-- Theme JS files -->
        <script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>

        <script type="text/javascript" src="{{ asset('assets/js/core/app.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/pages/form_multiselect.js') }}"></script>
        <!-- /theme JS files -->
    </head>

    <body class="login-container" style="background-image: url({{ asset('ExpoFolder/Expobackground.png') }})">

        <!-- Main navbar -->

        <div class="navbar navbar-inverse bg-info">
            <div class="navbar-header ">

                <a class="navbar-brand" href="#">
                    EXPO2025 OSAKA JAPAN PARTICIPATION REGISTRATION FORM
                </a>

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
                        <form action="{{ route('update_data') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="participantId" value="{{ $item->participantId }}">
                            <div class="row">
                                <div class="col-lg-12 col-lg-offset-0">
                                    <div class="panel registration-form">
                                        <div class="panel-body">
                                            <div class="text-center">
                                                <div class="border-success text-success">

                                                    {{-- <img src="{{ asset('images/nembo.png') }}" height="50"
                                                    width="70"> --}}
                                                    <img src="{{ asset('ExpoFolder/OfficialLogo.jpeg') }}"
                                                        style="width: 50%;">
                                                    {{-- <img src="{{ asset('images/logo.jpg') }}" height="50"
                                                    width="80"> --}}
                                                </div>
                                                <h5 class="content-group-lg">Preview Submitted information for Expo2025
                                                    Osaka
                                                    Japan<small class="display-block">All fields with <i
                                                            class=" text-danger">*</i>
                                                        are required</small></h5>
                                            </div>
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

                                            <div class=" form-group">
                                                <div class=" row">
                                                    <div class=" col-md-12"
                                                        style="background-color: rgb(209, 233, 235);">
                                                        <h2 class=" text-center">PERSONAL INFORMATION</h2>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>First Name as written in passport<i
                                                                class=" text-danger">*</i></label>
                                                        <input type="text" name="firstName" class="form-control"
                                                            placeholder="First Name as written in passport"
                                                            value="{{ $item->firstName }}" required autofocus>
                                                        <span class=" text-danger">
                                                            @error('firstName')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label>Middle Name as written in passport</label>
                                                        <input type="text" name="middleName" class="form-control"
                                                            placeholder="Middle Name as written in passport"
                                                            value="{{ $item->middleName }}" autofocus>
                                                        <span class=" text-danger">
                                                            @error('middleName')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label>Last Name as written in passport<i
                                                                class=" text-danger">*</i></label>
                                                        <input type="text" name="lastName" class="form-control"
                                                            placeholder="Last Name as written in passport"
                                                            value="{{ $item->lastName }}" required autofocus>
                                                        <span class=" text-danger">
                                                            @error('lastName')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class=" row form-group">
                                                    <div class=" col-md-4 ">
                                                        <label>Date Of Birth<i class=" text-danger">*</i></label>
                                                        <input type="date" name="dateOfBirth" class="form-control"
                                                            required autofocus value="{{ $item->dateOfBirth }}">
                                                        <span class=" text-danger">
                                                            @error('dateOfBirth')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                    <div class=" col-md-4">
                                                        <label>Sex<i class=" text-danger">*</i></label>
                                                        <select name="sex" class=" form-control" required
                                                            autofocus>
                                                            <option value="{{ $item->sex }}">
                                                                {{ $item->sex ?? '- - - Select Gender - - -' }}
                                                            </option>
                                                            <option value="MALE">MALE</option>
                                                            <option value="FEMALE">FEMALE</option>
                                                        </select>
                                                        <span class=" text-danger">
                                                            @error('sex')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class=" col-md-4">
                                                        <label>passport Number<i class=" text-danger">*</i></label>
                                                        <input name="passportNumber" placeholder="passport Number"
                                                            class=" form-control" value="{{ $item->passportNumber }}"
                                                            required autofocus>
                                                        <span class=" text-danger">
                                                            @error('passportNumber')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                </div>


                                                <div class=" row form-group">

                                                    <div class=" col-md-4">
                                                        <label>Type Of Passport<i class=" text-danger">*</i></label>
                                                        <select name="typeOfPassport" class=" form-control" required
                                                            autofocus>
                                                            <option value="{{ $item->typeOfPassport }}">
                                                                {{ $item->typeOfPassport ?? '- - - Select Type Of Passport - - -' }}
                                                            </option>
                                                            <option value="DIPLOMATS">DIPLOMATS</option>
                                                            <option value="OFFICIAL">OFFICIAL</option>
                                                            <option value="GENERAL">GENERAL</option>
                                                        </select>
                                                        <span class=" text-danger">
                                                            @error('typeOfPassport')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class=" col-md-4">
                                                        <label>Email Address<i class=" text-danger">*</i> <span
                                                                class=" text-muted">(Ensure you have access to this
                                                                Email)</span></label>
                                                        <input type="email" name="emailAddress"
                                                            placeholder="Email Address" class=" form-control" required
                                                            autofocus value="{{ $item->emailAddress }}">
                                                        <span class=" text-danger">
                                                            @error('emailAddress')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class=" col-md-4">
                                                        <label>Phone Number<i class=" text-danger">*</i> </label>
                                                        <input type="text" name="phoneNumber" class="form-control"
                                                            placeholder="Eg 255xxxxxxx"
                                                            value="{{ $item->phoneNumber }}" required autofocus>
                                                        <span class=" text-danger">
                                                            @error('phoneNumber')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class=" row form-group">

                                                    <div class=" col-md-4">
                                                        <img src="{{ $item->passportSizePhoto }}" height="100"
                                                            width="100"><br>
                                                        <label>Change File<span class=" text-muted"> (must be clear and
                                                                it is png,jpg or
                                                                jpeg not more than 2 MB size)</span></label>
                                                        <input type="file" name="passportSizePhoto"
                                                            class=" form-control">
                                                        <span class=" text-danger">
                                                            @error('passportSizePhoto')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class=" col-md-4">
                                                       <iframe src="{{ $item->travelPassport }}" height="100" width="400"></iframe><br>

                                                        <label>Change File <span class=" text-muted">(must be clear and
                                                                it is png,jpg or
                                                                jpeg not more than 2 MB size)</span></label>
                                                        <input type="file" name="travelPassport"
                                                            placeholder="Travel Passport" class=" form-control"
                                                            autofocus value="{{ old('travelPassport') }}">
                                                        <span class=" text-danger">
                                                            @error('travelPassport')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class=" col-md-4">
                                                        <iframe src=" {{ $item->workingId }}" height="100" width="400"></iframe><br>
                                                        <br>
                                                        <label>Change File <span class=" text-muted"> (must be a pdf
                                                                and not more than 2 MB size)</span></label>
                                                        <input type="file" name="workingId" placeholder="Work Id"
                                                            class=" form-control" autofocus
                                                            value="{{ old('workingId') }}">
                                                        <span class=" text-danger">
                                                            @error('workingId')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class=" col-md-12 rounded form-group"
                                                    style="background-color: rgb(209, 233, 235);">
                                                    <h2 class=" text-center">INSTITUTION/COMPANY INFORMATION</h2>
                                                </div>

                                                <div class=" row form-group">
                                                    <div class=" col-md-4">
                                                        <label>Your Institution/company Name<i
                                                                class=" text-danger">*</i>
                                                        </label>
                                                        <input type="text" name="companyName" class="form-control"
                                                            placeholder="Institution/company Name"
                                                            value="{{ $item->companyName }}" required>
                                                        <span class=" text-danger">
                                                            @error('companyName')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label>Your Institution/company Email <i
                                                                class=" text-danger">*</i>
                                                        </label>
                                                        <input type="text" class="form-control"
                                                            name="companyEmail" placeholder="Eg. demo@gmail.com"
                                                            value="{{ $item->companyEmail }}" required autofocus>
                                                        <span class=" text-danger">
                                                            @error('companyEmail')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label>Your Institution/company Mobile Number <i
                                                                class=" text-danger">*</i> </label>
                                                        <input type="text" class="form-control"
                                                            name="companyPhoneNumber" placeholder="Eg. 255xxxxxxxxx"
                                                            value="{{ $item->companyPhoneNumber }}" required
                                                            autofocus>
                                                        <span class=" text-danger">
                                                            @error('companyPhoneNumber')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class=" row form-group">

                                                    <div class=" col-md-6">
                                                        <label>Institution/Company Profile<i
                                                                class=" text-danger">*</i></label>
                                                        <textarea name="companyProfile" placeholder="Write here your Institution/Company Profile" class=" form-control"
                                                            required>{{ $item->companyProfile }}</textarea>
                                                        <span class=" text-danger">
                                                            @error('companyProfile')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <div class=" multi-select-full">
                                                            <label>Select your sector <i
                                                                    class=" text-danger">*</i></label>
                                                            <select name="companySector" class="multiselect"
                                                                 required autofocus
                                                                id="companySector">
                                                                <option value="{{ $item->companySector }}">{{ $item->companySector }}</option>
                                                                <option value="Science& Technology">Science& Technology
                                                                </option>
                                                                <option value="Energy & Mining">Energy & Mining
                                                                </option>
                                                                <option value="Culture">Culture</option>
                                                                <option value="Health">Health</option>
                                                                <option value="Tourism">Tourism</option>
                                                                <option value="Infrastructure">Infrastructure</option>
                                                                <option value="Agriculture">Agriculture</option>
                                                                <option value="Transport & Logistics">Transport &
                                                                    Logistics
                                                                </option>
                                                                <option value="Financial Services">Financial Services
                                                                </option>
                                                                <option value="Manufacturing">Manufacturing</option>
                                                            </select>
                                                            <span class=" text-muted" style="font-size: 10px">
                                                                <b>Selected: </b> {{ $item->companySector }}
                                                             </span>
                                                           
                                                        </div>
                                                    </div>
                                                </div>



                                                <div class=" col-md-12 rounded form-group"
                                                    style="background-color: rgb(209, 233, 235);">
                                                    <h2 class=" text-center">PARTICIPATION INFORMATION</h2>
                                                </div>

                                                <div class=" row form-group">
                                                    <div class="form-group col-md-6">
                                                        <div class="multi-select-full">
                                                            <label>Type Of Participation [Aina ya ushiriki] <i
                                                                    class=" text-danger">*</i></label>
                                                            <select name="typeOfParticipation" required autofocus
                                                                id="typeOfParticipation" class="multiselect">
                                                                <option value="{{ $item->typeOfParticipation }}">{{ $item->typeOfParticipation }}</option>
                                                                <option
                                                                    value="Banda Kuu la Tanzania (kuanzia 13 Aprili, 2025 – 13 Oktoba, 2025) [Tanzania Pavilion]">
                                                                    Banda Kuu la Tanzania (kuanzia 13 Aprili, 2025 – 13
                                                                    Oktoba,
                                                                    2025) [Tanzania Pavilion]</option>
                                                                <option
                                                                    value="Programu maalum (za siku 12 kuanzia Mwezi Aprili, 2025 – Oktoba 2025)">
                                                                    Programu maalum (za siku 12 kuanzia Mwezi Aprili,
                                                                    2025 –
                                                                    Oktoba 2025)</option>
                                                                <option value="Eneo la Mauzo (Commercial Space)">Eneo
                                                                    la Mauzo
                                                                    (Commercial Space)
                                                                </option>
                                                            </select>
                                                            <span class=" text-muted" style="font-size: 10px">
                                                                <b>Selected: </b> {{ $item->typeOfParticipation }}
                                                             </span>
                                                           
                                                        </div>
                                                    </div>

                                                    <div class="form-group col-md-6" id="specificProgram">
                                                        <div class=" multi-select-full">
                                                            <label>Select Program of your Interest [Chagua aina ya
                                                                program
                                                                utakayo hudhuria] </label>
                                                            <select name="programOfInterest" class="multiselect"
                                                                 autofocus>
                                                                <option value="{{ $item->programOfInterest }}">{{ $item->programOfInterest }}</option>
                                                                {{-- <option
                                                                    value="Tanzania Unforgettable Experience [25th April – 6th May, 2025]">
                                                                    Tanzania Unforgettable Experience [25th April – 6th
                                                                    May,
                                                                    2025]</option> --}}
                                                                {{-- <option
                                                                    value="Infrastructure for sustainable development [15th May- 26th May, 2025]">
                                                                    Infrastructure for sustainable development [15th
                                                                    May-
                                                                    26th
                                                                    May, 2025]</option> --}}
                                                                {{-- <option value="Tanzania National Day [25th May, 2025]">
                                                                    Tanzania
                                                                    National Day [25th May, 2025] </option> --}}
                                                                {{-- <option
                                                                    value="Tanzania Business, Investment and Tourism Forum [26th May, 2025]">
                                                                    Tanzania Business, Investment and Tourism Forum
                                                                    [26th
                                                                    May,
                                                                    2025]</option> --}}
                                                                <option
                                                                    value="Building Better Tomorrow- Tanzania Focus in the Agriculture, livestock, Fisheries and Blue Economy [5th June- 16th June, 2025]">
                                                                    Building Better Tomorrow- Tanzania Focus in the
                                                                    Agriculture,
                                                                    livestock, Fisheries and Blue Economy [5th June-
                                                                    16th
                                                                    June,
                                                                    2025]</option>
                                                                <option
                                                                    value="The Transformation of Health sector in Tanzania [20th June- 1st July, 2025]">
                                                                    The Transformation of Health sector in Tanzania
                                                                    [20th
                                                                    June-
                                                                    1st July, 2025]</option>
                                                                <option
                                                                    value="Unity in diversity- Kiswahili language in promoting unity and cultural diversity [1st July- 7th July, 2025]">
                                                                    Unity in diversity- Kiswahili language in promoting
                                                                    unity
                                                                    and cultural diversity [1st July- 7th July, 2025]
                                                                </option>
                                                                <option
                                                                    value="Women Empowerment for Future Society [1st Aug- 12th Aug, 2025]">
                                                                    Women Empowerment for Future Society [1st Aug- 12th
                                                                    Aug,
                                                                    2025]</option>
                                                                <option
                                                                    value="Clean Energy for Shared Future [17th Sept- 28 Sept, 2025]">
                                                                    Clean Energy for Shared Future [17th Sept- 28 Sept,
                                                                    2025]
                                                                </option>
                                                                <option
                                                                    value="Innovation and inventions for prosperity [02nd Oct- 12th  Oct, 2025]">
                                                                    Innovation and inventions for prosperity [02nd Oct-
                                                                    12th
                                                                    Oct, 2025]</option>
                                                            </select>
                                                            <span class=" text-muted" style="font-size: 10px">
                                                               <b>Selected: </b> {{ $item->programOfInterest }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class=" col-md-12 form-group" id="anyOtherDescription">
                                                        <label>Provide the details of your targeted companies or
                                                            institutions for better organization of meetings.
                                                            (Optional)</label>
                                                        <textarea name="anyOtherDescription" class=" form-control" rows="2"
                                                            placeholder="provide Institution/Company Name & Address if available">{{ $item->anyOtherDescription }}</textarea>
                                                        <span class=" text-danger">
                                                            @error('anyOtherDescription')
                                                                {{ $message }}
                                                            @enderror
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label>When do you plan to visit Osaka? [Chagua tarehe utakayo
                                                            kuja Osaka] <i class=" text-danger">*</i>
                                                        </label>
                                                        <input type="date" class="form-control" name="dateOfVisit"
                                                            value="{{ $item->dateOfVisit }}" required>
                                                        
                                                        
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label>When do you plan to return home ? [Chagua tarehe utakayo
                                                            rudi kutoka Osaka Japan] <i class=" text-danger">*</i>
                                                        </label>
                                                        <input type="date" class="form-control"
                                                            name="returningDate" value="{{ $item->returningDate }}"
                                                            required>
                                                        <span class=" text-danger">
                                                            @error('returningDate')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="styled" required checked>
                                                        I declare that the information provided is complete and correct
                                                        to the best of my knowledge. I understand that any false
                                                        information supplied could lead to my application being
                                                        disqualified.
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="text-right col-md-11 form-group">
                                                <button type="submit" id="submitBtn"
                                                    class="btn bg-info   btn-ladda btn-ladda-spinner">Update
                                                    your Information <b><i class=" icon-arrow-right13"></i></b>
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
@endforeach
