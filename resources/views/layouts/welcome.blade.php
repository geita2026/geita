<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Login') }}</title>

    <link rel="icon" href="{{ asset('images/nembo.PNG') }}" type="image/gif" sizes="16x16">
    <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/components.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/colors.css') }}" rel="stylesheet" type="text/css">
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>
<body class="login-container">
    <img src="{{ asset('images/tz-flag.jpg') }}" style="position:absolute; width:100%; height:100%; opacity:10%;">
<!-- Page container -->
<div class="page-container">
    <!-- Page content -->
    <div class="page-content">
        <!-- Main content -->
        <div class="content-wrapper">
            <!-- Content area -->
            <div class="content">
            {{ $slot }}
            </div>
            <div class=" text-center">
                @include('layouts.footer')
            </div>
        </div>

    </div>

</div>
</body>

    <!-- Core JS files -->
 <script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
 <!-- /core JS files -->
 <!-- Theme JS files -->
 <script type="text/javascript" src="{{ asset('assets/js/core/app.js') }}"></script>

 <script>

    $(function() {
    @if(Session::get('success'))
             $(document).ready(function() {
        new PNotify({
            title: 'Success!',
            text: '{{ session::get("success") }}',
            icon: 'icon-info3',
            type: 'success'
        });
        });
    @endif

    @if(Session::get('fail'))
             $(document).ready(function() {
        new PNotify({
            title: 'Ooops!',
            text: '{{ session::get("fail") }}',
            icon: 'icon-warning',
            type: 'error',
        });
        });
    @endif

    });
    </script>
    <script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>

