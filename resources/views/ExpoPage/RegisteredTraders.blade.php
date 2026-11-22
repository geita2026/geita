<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Tanzania Expo2025 Osaka Japan Participation registration Form">
    <meta name="keywords" content="Register, Expo, Osaka, expo2025,tantrade,maonesho">
    <meta name="author" content="Tanzania Trade Development Authority (TanTrade)">
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
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
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
                <div class="card">
                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <h4 class="panel-title text-bold text-uppercase text-center">LIST OF CONFIRMED PARTICIPANTS
                                FOR EXPO 2025 OSAKA JAPAN ON DIFFERENT PROGRAMS</h6>
                        </div>
                        <div class=" panel-body">

                            <!-- Main charts -->
                            <div class="row">
                                <div class="col-lg-12">

                                    <div class="panel panel-flat">
                                        <div class="panel-body">
                                        @if (Session::get('fail'))
                                            <div class=" alert alert-danger">
                                                {{ Session('fail') }}
                                            </div>
                                        @endif
                                            @if (session('secret')!=date('dmy'))
                                            <div class="form-group">
                                                <form class="{{ route('registered') }}" method="POST">
                                                    @csrf
                                                    @method('GET')
                                                    <div class="row">
                                                        <div class=" col-md-10">
                                                            <input type="text" name="secret" placeholder="Enter Data secret" class=" form-control">
                                                        </div>
                                                        <div class=" col-md-2">
                                                            <button class=" btn btn-primary">Request Data<i class=" icon icon-arrow-right13"></i></button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            @else
                                            <div class=" form-group col-md-12 table-responsive">
                                                <table
                                                    class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr class=" bg-teal-400">
                                                            <th>#</th>
                                                            <th class=" text-bold text-center">COMPANY NAME</th>
                                                            <th class=" text-bold text-center">Personal Name</th>
                                                            <th class=" text-bold text-center">Company Sector</th>
                                                            <th class=" text-bold text-center">Program Of Interest</th>
                                                            <th class=" text-bold text-center">Targeted companies/sector                                                     </th>
                                                            <th class=" text-bold text-center">Sex</th>
                                                            <th class=" text-bold text-center">country</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                  
                                                        @foreach ($expoData as $key => $value)
                                                            <tr>
                                                                <td>{{ $key + 1 }}</td>
                                                                <td>{{ $value->companyName }}</td>
                                                                <td>{{ $value->firstName }} -
                                                                    {{ $value->lastName }} </td>
                                                                <td>{{ $value->companySector }} </td>
                                                                <td>{{ $value->programOfInterest }}</td>
                                                                <td>{{ $value->anyOtherDescription }} </td>
                                                                <td>{{ $value->sex }}</td>
                                                                <td>{{ $value->country }}</td>
                                                            </tr>
                                                          
                                                        @endforeach
                                                        <b class=" text-warning">Total Participants: {{ $total }}</b>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class=" col-md-12 form-group text-right">
                                                {{ $expoData->links() }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- another 7-col-md here --}}

                                </div>
                            </div>
                            <!-- /main charts -->
                        </div>
                    </div>

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

