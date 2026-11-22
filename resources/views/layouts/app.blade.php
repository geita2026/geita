
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <!-- META -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description"
        content="Trade Fair is a web application that manages and controls major trade fairs in Tanzania, including Dar es Salaam International Trade Fair (DITF) and other national exhibitions.">

    <meta name="keywords"
        content="trade fair, sabasaba, DITF, maonesho, viwanja vya maonesho, tradefair, tanzania">

    <meta name="author"
        content="Tanzania Trade Development Authority (TanTrade)">

    <!-- FAVICONS -->
    <link rel="icon" href="{{ asset('images/nembo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.jpg') }}">

    <!-- BASE STYLES -->
    <link rel="stylesheet"
        href="{{ asset('assets/css/icons/icomoon/styles.css') }}?v=1">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/colors.css') }}">

    <!-- TANTRADE THEMES -->
    <link rel="stylesheet" href="{{ asset('assets/css/sams-tantrade.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-gold.css') }}">

    <!-- MAIN GEMEMIS THEME: MUST LOAD LAST -->
    <link rel="stylesheet"
        href="{{ asset('assets/css/gememis-modern.css') }}?v=5">

    <!-- ADDITIONAL ICON AND THEME FIXES -->
    <style>
        :root {
            --gem-black: #080808;
            --gem-black-light: #151515;
            --gem-gold: #d4af37;
            --gem-gold-light: #f1d778;
            --gem-white: #ffffff;
        }

        /* GENERAL */
        body {
            background-color: #f5f5f5;
        }

        /* LOGO */
        #logo {
            font-size: 250%;
            padding: 5%;
            text-align: center;
            text-shadow: 1px 1px 1px #000, 2px 2px 1px #000;
        }

        .zoom {
            transition: transform .5s;
        }

        .zoom:hover {
            transform: scale(1.02);
        }

        .table-header {
            background-color: #dedee4;
            border: 0.5px solid #a3e0db;
        }

        #h1 { font-size: 250%; }
        #h2 { font-size: 200%; }
        #h3 { font-size: 150%; }
        #h4 { font-size: 100%; }

        #h1, #h2, #h3, #h4 {
            text-shadow: 1px 1px 1px #000, 2px 2px 1px #000;
        }

        /* =====================================
           SIDEBAR BLACK AND GOLD
        ===================================== */

        .sidebar.sidebar-main,
        .sidebar-main,
        .gememis-sidebar {
            background: var(--gem-black) !important;
            border-right: 1px solid var(--gem-gold) !important;
        }

        .sidebar-main .navigation > li > a,
        .sidebar-main .navigation li ul li a {
            color: var(--gem-white) !important;
        }

        .sidebar-main .navigation > li > a:hover,
        .sidebar-main .navigation > li.active > a,
        .sidebar-main .navigation li ul li a:hover,
        .sidebar-main .navigation li ul li a.active {
            color: var(--gem-gold) !important;
            background: var(--gem-black-light) !important;
        }

        /* Sidebar Icomoon icons */
        .sidebar-main i[class^="icon-"],
        .sidebar-main i[class*=" icon-"],
        .sidebar-main .navigation i,
        .gememis-sidebar i {
            color: var(--gem-gold) !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Sidebar submenu */
        .sidebar-main .navigation li ul {
            background: #101010 !important;
        }

        .sidebar-main .navigation li ul li a {
            color: #eeeeee !important;
        }

        /* =====================================
           TOP BAR BLACK AND GOLD ICONS
        ===================================== */

        .navbar,
        .navbar-inverse,
        .navbar-default {
            border-color: var(--gem-gold) !important;
        }

        /* Icomoon icons */
        .navbar i[class^="icon-"],
        .navbar i[class*=" icon-"],
        .navbar .icon {
            color: var(--gem-gold) !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Font Awesome icons, if used */
        .navbar .fa,
        .navbar [class^="fa-"],
        .navbar [class*=" fa-"] {
            color: var(--gem-gold) !important;
        }

        /* Bootstrap glyphicon icons, if used */
        .navbar .glyphicon {
            color: var(--gem-gold) !important;
        }

        /* Top bar links */
        .navbar .navbar-nav > li > a {
            color: var(--gem-white) !important;
        }

        .navbar .navbar-nav > li > a:hover,
        .navbar .navbar-nav > li > a:focus {
            color: var(--gem-gold) !important;
        }

        /* Dropdown icons */
        .navbar .dropdown-menu i,
        .navbar .dropdown-menu .icon,
        .navbar .dropdown-menu .glyphicon,
        .navbar .dropdown-menu .fa {
            color: var(--gem-gold) !important;
        }

        /* Phone icon */
        .icon-phone-wave {
            color: var(--gem-gold) !important;
        }

        /* =====================================
           AVATAR
        ===================================== */

        .gememis-avatar {
            background: var(--gem-black) !important;
            border: 2px solid var(--gem-gold) !important;
            border-radius: 50% !important;
            overflow: hidden;
        }

        /* Preserve existing avatar image */
        .gememis-avatar img {
            border-radius: 50%;
        }

        /* =====================================
           GENERAL ICON VISIBILITY
        ===================================== */

        .sidebar i,
        .navbar i {
            opacity: 1;
            visibility: visible;
        }
    </style>

    <!-- CORE JS -->
    <script src="{{ asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>

    <!-- SWEETALERT2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')

</head>

<body class="navbar-top gememis-app">

    <!-- BACKGROUND -->
    <img src="{{ asset('images/tz-flag.jpg') }}"
        alt="Tanzania Flag Background"
        style="position:fixed;width:100%;height:100%;object-fit:cover;opacity:0.10;z-index:-1;pointer-events:none;">

    <!-- HEADER / TOP BAR -->
    @include('layouts.header')

    <!-- PAGE CONTAINER -->
    <div class="page-container">
        <div class="page-content">

            <!-- SIDEBAR -->
            @include('layouts.sidebar')

            <!-- MAIN CONTENT -->
            <div class="content-wrapper">
                <main>
                    {{ $slot }}
                </main>
            </div>

        </div>
    </div>

    <!-- FOOTER -->
    @include('layouts.footer')

    <!-- THEME JS -->
    <script src="{{ asset('assets/js/core/app.js') }}"></script>

    <!-- BUTTONS -->
    <script src="{{ asset('assets/js/plugins/buttons/spin.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/buttons/ladda.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/components_buttons.js') }}"></script>

    <!-- DATATABLES -->
    <script src="{{ asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables_basic.js') }}"></script>

    <!-- FORMS -->
    <script src="{{ asset('assets/js/plugins/forms/selects/bootstrap_select.min.js') }}"></script>

    <!-- UTILITIES -->
    <script src="{{ asset('assets/js/core/instascan.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/layout_fixed_native.js') }}"></script>

    <!-- LEGACY NOTIFICATION LIBRARIES -->
    <script src="{{ asset('assets/js/plugins/notifications/bootbox.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>

    <!-- SESSION NOTIFICATIONS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Operation Successful',
                    text: @json(session('success')),
                    width: 520,
                    padding: '2em',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d4af37',
                    allowOutsideClick: false
                });
            @endif

            @if (session('fail'))
                Swal.fire({
                    icon: 'error',
                    title: 'Operation Failed',
                    text: @json(session('fail')),
                    width: 520,
                    padding: '2em',
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#d32f2f',
                    allowOutsideClick: false
                });
            @endif

            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Attention Required',
                    text: @json(session('warning')),
                    width: 520,
                    padding: '2em',
                    confirmButtonText: 'Understood',
                    confirmButtonColor: '#d4af37',
                    allowOutsideClick: false
                });
            @endif

            @if (session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Information',
                    text: @json(session('info')),
                    width: 520,
                    padding: '2em',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d4af37',
                    allowOutsideClick: false
                });
            @endif

        });
    </script>

    <!-- COMPANY REGISTRATION MODAL -->
    <div id="NoCompany"
        class="modal fade"
        data-backdrop="static"
        data-keyboard="false">

        <div class="modal-dialog modal-full">
            <div class="modal-content alert alert-info">

                <div class="modal-body">

                    <p style="font-size:2vmax">
                        <b>Company Registration</b>
                    </p>

                    <p style="font-size:1.8vmin">
                        <b class="text-danger">Note!</b>

                        If your company participated in Dar es Salaam International Trade Fair
                        between 2022 and {{ date('Y') }},
                        register using the <b>TSRN</b> issued.
                    </p>

                </div>

                @include('company.NewCompanyRegistration')

            </div>
        </div>
    </div>

    <!-- COMPANY PROFILE CHECK -->
    @auth
        @php
            $currentUser = Auth::user();

            $myCompanies = App\Models\Companies\CompanyAdmin::where(
                'adminId',
                $currentUser->user_uuid
            )->count();

            $myUnupdatedCompanies = App\Models\companies::leftJoin(
                'company_admins',
                'companies.companyID',
                '=',
                'company_admins.companyId'
            )
                ->where('company_admins.adminId', $currentUser->user_uuid)
                ->where('companies.lastUpdatedOn', '!=', date('Y'))
                ->count();
        @endphp

        <!-- NEW COMPANY CHECK -->
        @if (
            $myCompanies == 0 &&
            $currentUser->account_type == 2 &&
            !request()->routeIs('companies.*')
        )
            <script>
                $(document).ready(function () {
                    $("#NoCompany").modal('show');
                });
            </script>
        @endif

        <!-- COMPANY UPDATE REMINDER -->
        @if (
            $myUnupdatedCompanies > 0 &&
            $currentUser->account_type == 2 &&
            !request()->routeIs('companies.*')
        )
            <script>
                $(document).ready(function () {
                    swal({
                        title: "Unupdated Company Profile ({{ $myUnupdatedCompanies }})",
                        text: "Please update your company details for the current year.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Update Now",
                        cancelButtonText: "Later"
                    }, function (isConfirm) {

                        if (isConfirm) {
                            window.location.href = @json(route('companies.index'));
                        }

                    });
                });
            </script>
        @endif
    @endauth

    @stack('scripts')

</body>
</html>
