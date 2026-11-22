<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description"
        content="GeMEMIS — the Geita Mineral Exhibition Management Information System. Exhibitor registration,
    space allocation, invoicing, payment and check-in for the Geita mineral exhibitions.">
    <meta name="keywords" content="GeMEMIS, Geita, mineral exhibition, madini, maonesho ya madini, exhibition space, space allocation, Tanzania">
    <meta name="author" content="Geita Mineral Exhibition Secretariat">

        


    <link rel="icon" href="{{ asset('images/coat-of-arms.svg') }}">
    <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/components.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/colors.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-dialogs.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-tables.css') }}">
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">

    <style>

/* #loading {
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 100;
  width: 100vw;
  height: 100vh;
  background-color: white;
  background-clip: url("{{ asset('Advertisements/13-12-2022 15:46:35.mp4') }}")
  background-image: url("{{ asset('images/loading3.gif') }}");
  background-repeat: no-repeat;
  background-position: center;
} */

    </style>

    </head>
<body class="gememis-app">
<!-- Page container -->
<div class="page-container">
    <!-- Page content -->
    <div class="page-content">
        <!-- Main content -->
        <div class="content-wrapper">
            <!-- Content area -->
            <div class="content">
            <div class="page"></div>
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

 <script type="text/javascript" src="{{ asset('assets/js/plugins/buttons/spin.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/plugins/buttons/ladda.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/pages/components_buttons.js') }}"></script>
 
 <script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/pages/form_select2.js') }}"></script>
 <script src="{{ asset('assets/js/core/gememis-selects.js') }}"></script>
 <script src="{{ asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
 <script src="{{ asset('assets/js/pages/datatables_basic.js') }}"></script>


 <script>

    $(function() {
        @if(Session::get('success'))
             $(document).ready(function() {
        new PNotify({
            title: 'OK',
            text: '{{ session::get("success") }}',
            icon: 'icon-check',
            type: 'success'
        });
        });
    @endif

    @if(Session::get('fail'))
             $(document).ready(function() {
        new PNotify({
            title: 'Ooops!',
            text: '{{ session::get("fail") }}',
            icon: 'icon-x',
            type: 'error',
        });
        });
    @endif

    });
    </script>
    <script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>


    {{-- loading animation --}}
    <script>
        function onReady(callback) {
      var intervalId = window.setInterval(function() {
        if (document.getElementsByTagName('body')[0] !== undefined) {
          window.clearInterval(intervalId);
          callback.call(this);
        }
      }, 1000);
    }

    function setVisible(selector, visible) {
      document.querySelector(selector).style.display = visible ? 'block' : 'none';
    }

    onReady(function() {
      setVisible('.page', true);
      setVisible('#loading', false);
    });
    </script>

<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    }
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>