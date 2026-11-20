<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="Trade Fair is the web application system wich manages and control all largest trade
        Fair in Tanzania, such as Dar es salaam International Trade Fair (DITF), Tanzania industirial exhibition, e.t.c ">
    <meta name="keywords"
        content="trade fair, sabasaba, DITF, maonesho, viwanja vya maonesho, fair grounds, tradefair,tanzania,">
    <meta name="author" content="Tanzania Trade Development Authority (TanTrade)">
    <link rel="icon" href="{{ asset('images/nembo.png') }}" type="image/gif" sizes="16x16">
    <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/components.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/colors.css') }}" rel="stylesheet" type="text/css">
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
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
            <a class="navbar-brand" href="{{ route('index') }}"><img src="{{ asset('images/logo.jpg') }}"
                    alt=""></a>

            <ul class="nav navbar-nav pull-right visible-xs-block">
                <li><a href="{{ route('index') }}" data-toggle="collapse" data-target="#navbar-mobile"><i
                            class=" icon-menu5"></i></a></li>
            </ul>
        </div>
        <div class="navbar-collapse collapse" id="navbar-mobile">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="{{ route('index') }}">
                        <i class=" icon-arrow-left12"> Go to Home Page </i> <span
                            class="visible-xs-inline-block position-right"> </span>
                    </a>
                </li>
            </ul>
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
                    <div class=" col-md-12 form-group">
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
                    <h1 class=" text-center text-primary text-bold">TNBLC MARKING DASHBOARD</h1>
                    @foreach ($applicant as $item)
                        <div class=" row">
                            <div class=" col-md-12 form-group">
                                <h3>#: <b>{{ session('judgingNumber') }} || {{ $item->competitionNumber }}</b></h3>
                            </div>
                            <div class=" col-md-6">
                                <b>Logo Description: </b><br>
                                {{ $item->logoDescription }}
                            </div>
                            <form action="{{ route('submit_marks') }}" method="POST">
                                @csrf
                                <input type="hidden" name="tnbId" value="{{ $item->competitionNumber }}">
                                <div class=" col-md-6">
                                    <div class=" form-group col-md-12">
                                        <select name="criteria" class=" form-control" required>
                                            <option value=""> - - - Select Criteria - - -</option>
                                            @foreach ($creteria as $value)
                                                <option value="{{ $value->criteriaCode }}">
                                                    {{ $value->criteriaDescription }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class=" form-group col-md-10">
                                        <input type="number" name="marks" class=" form-control" min="0">
                                        <span class=" text-danger">@error('marks')
                                            {{ $message }}
                                        @enderror</span>
                                    </div>
                                    <div class=" col-md-2">
                                        <button type="submit" class=" btn btn-primary">Submit Marks<i class=" icon icon-arrow-right13"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endforeach
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
