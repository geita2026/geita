<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    <style>
        .zoom {
            transition: transform .5s;
        }

        .zoom:hover {
            -ms-transform: scale(1.02);
            /* IE 9 */
            -webkit-transform: scale(1.02);
            /* Safari 3-8 */
            transform: scale(1.02);
        }

        #h1 {
            font-size: 250%;
            text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);
        }

        #h2 {
            font-size: 200%;
            text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);
        }

        #h3 {
            font-size: 150%;
            text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);
        }

        #h4 {
            font-size: 100%;
            text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);
        }

        .virtual-content {
            background-color: rgb(227, 233, 233);
            border: 1px solid rgb(151, 206, 194);
            padding: 1%;
        }

        .exhibitor {
            background-image: url('{{ asset('images/Tz.gif') }}');
            background-size: cover;
            background-size: 900px 300px;
        }
    </style>


    <link rel="icon" href="{{ asset('images/nembo.PNG') }}" type="image/gif" sizes="16x16">
    <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/components.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/colors.css') }}" rel="stylesheet" type="text/css">
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
</head>

<body>


    <!-- Main navbar -->
    <div class="navbar navbar-inverse bg-primary">
        <div class="navbar-header">
            <a class="navbar-brand text-bold" href="{{ route('About_ditf') }}" id="h3">
                <img src="{{ asset('images/nembo.png') }}">
            </a>

            <ul class="nav navbar-nav pull-right visible-xs-block">
                <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class=" icon-menu4"></i></a></li>
            </ul>
        </div>
        <div class="navbar-collapse collapse" id="navbar-mobile">
            <ul class="nav navbar-nav ">

                <li class="dropdown btn">

                    <a href="#" class="dropdown-toggle text-bold " data-toggle="dropdown">About DITF <span
                            class="caret"></span></a>
                    <ul class="dropdown-menu dropdown-menu-left">
                        <li><a href="{{ route('About_ditf') }}"> <i class=" icon-arrow-right13"></i> Understanding
                                DITF</a>
                        </li>
                        <li><a href="{{ route('experience.index') }}"><i class=" icon-arrow-right13"></i> Our
                                Exprience</a></li>

                        <li><a href="{{ route('discover_exhibitor.index') }}"><i
                                    class=" icon-arrow-right13"></i>Discover Exhbitors</a></li>
                        <li><a href="{{ route('preview_programs.index') }}"><i class=" icon-arrow-right13"></i> Event
                                Programs</a></li>
                    </ul>
                </li>
                <li class="btn text-bold"><a href="{{ route('ditf_map') }}"> DITF Map</a></li>
                <li class=" btn text-bold"><a href="{{ route('virtual_exhibition.index') }}"> Virtual DITF</a></li>
                <li class=" btn text-bold"><a href="{{ route('catalogy.index') }}">DITF Catalogue</a></li>

                {{-- <li class=" btn text-bold"><a href="{{ route('discover_exhibitor.index') }}">Discover Exhibitor</a></li> --}}
                {{-- <li class=" btn text-bold" data-toggle="modal" data-target="#login"><a ><i class=" icon-lock2"></i> Login </a></li> --}}

                {{-- <li class=" btn text-bold" data-toggle="modal" data-target="#NewAccount"><a><i class=" icon-user-plus"></i> REGISTER </a></li> --}}

            </ul>
            <ul class=" nav navbar-right">
                <li class=" btn text-bold "><a href="{{ route('index') }}"
                        class=" btn btn-default text-primary"><i></i> Go to Home </a></li>
            </ul>
        </div>
    </div>
    <!-- /main navbar -->

</body>

</html>
