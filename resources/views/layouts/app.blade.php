<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ===================== META ===================== -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description"
        content="GeMEMIS — the Geita Mineral Exhibition Management Information System. Exhibitor registration,
          space allocation, invoicing, payment and check-in for the Geita mineral exhibitions.">

    <meta name="keywords" content="GeMEMIS, Geita, mineral exhibition, madini, maonesho ya madini, exhibition space, space allocation, Tanzania">

    <meta name="author" content="Geita Mineral Exhibition Secretariat">

    <!-- ===================== ICONS ===================== -->
    <link rel="icon" href="{{ asset('images/coat-of-arms.svg') }}">

    <!-- ===================== STYLES ===================== -->
    <link rel="stylesheet" href="{{ asset('assets/css/icons/icomoon/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/colors.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/company-registration.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-dialogs.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-tables.css') }}">

    <!-- ===================== CORE JS ===================== -->
    <script src="{{ asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>

    <!-- ===================== SWEETALERT2 (MODERN) ===================== -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ===================== INLINE STYLES ===================== -->
    <style>
        #logo {
            font-size: 250%;
            padding: 5%;
            text-shadow: 1px 1px 1px #000, 2px 2px 1px #000;
            text-align: center;
        }

        .zoom {
            transition: transform .5s;
        }

        .zoom:hover {
            transform: scale(1.02);
        }

        .table-header {
            background-color: rgb(222, 222, 228);
            border: 0.5px solid rgb(163, 224, 219);
        }

        #h1 {
            font-size: 250%;
        }

        #h2 {
            font-size: 200%;
        }

        #h3 {
            font-size: 150%;
        }

        #h4 {
            font-size: 100%;
        }

        #h1,
        #h2,
        #h3,
        #h4 {
            text-shadow: 1px 1px 1px #000, 2px 2px 1px #000;
        }
    </style>
</head>

<body class="navbar-top gememis-app">

    <!-- ===================== HEADER ===================== -->
    @include('layouts.header')

    <!-- ===================== PAGE CONTAINER ===================== -->
    <div class="page-container">
        <div class="page-content">

            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- ===================== MAIN CONTENT ===================== -->
            <div class="content-wrapper">
                <main>
                    {{ $slot }}
                </main>
            </div>

        </div>
    </div>

    <!-- ===================== FOOTER ===================== -->
    @include('layouts.footer')

</body>

</html>

<!-- ===================== THEME JS ===================== -->
<script src="{{ asset('assets/js/core/app.js') }}"></script>

<!-- ===================== BUTTONS ===================== -->
<script src="{{ asset('assets/js/plugins/buttons/spin.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/buttons/ladda.min.js') }}">
    < /cript> <
    script src = "{{ asset('assets/js/pages/components_buttons.js') }}" >
</script>

<!-- ===================== DATATABLES ===================== -->
<script src="{{ asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatables_basic.js') }}"></script>

<!-- ===================== FORMS ===================== -->
<script src="{{ asset('assets/js/plugins/forms/selects/bootstrap_select.min.js') }}"></script>
<script src="{{ asset('assets/js/core/gememis-selects.js') }}"></script>

<!-- ===================== UTILITIES ===================== -->
<script src="{{ asset('assets/js/core/instascan.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/layout_fixed_native.js') }}"></script>

<!-- ===================== LEGACY NOTIFICATION LIBS (SAFE TO KEEP) ===================== -->
<script src="{{ asset('assets/js/plugins/notifications/bootbox.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>

<!-- ===================== LARGE CENTER SWEET ALERTS ===================== -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Operation Successful',
                html: `<p style="font-size:15px;line-height:1.6">{{ session('success') }}</p>`,
                width: 520,
                padding: '2em',
                confirmButtonText: 'OK',
                confirmButtonColor: '#1e88e5',
                allowOutsideClick: false
            });
        @endif

        @if (session('fail'))
            Swal.fire({
                icon: 'error',
                title: 'Operation Failed',
                html: `<p style="font-size:15px;line-height:1.6">{{ session('fail') }}</p>`,
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
                html: `<p style="font-size:15px;line-height:1.6">{{ session('warning') }}</p>`,
                width: 520,
                padding: '2em',
                confirmButtonText: 'Understood',
                confirmButtonColor: '#f9a825',
                allowOutsideClick: false
            });
        @endif

        @if (session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Information',
                html: `<p style="font-size:15px;line-height:1.6">{{ session('info') }}</p>`,
                width: 520,
                padding: '2em',
                confirmButtonText: 'OK',
                confirmButtonColor: '#0288d1',
                allowOutsideClick: false
            });
        @endif

    });
</script>


@php
    $myCompanies = App\Models\Companies\CompanyAdmin::where('adminId', Auth::user()->user_uuid)->count();

    $myUnupdatedCompanies = App\Models\companies::leftJoin(
        'company_admins',
        'companies.companyID',
        '=',
        'company_admins.companyId',
    )
        ->where('company_admins.adminId', Auth::user()->user_uuid)
        ->where('companies.lastUpdatedOn', '!=', date('Y'))
        ->count();
@endphp

@if (
    $myCompanies == 0 &&
        !Auth::user()->myCompany &&
        Auth::user()->account_type == 2 &&
        !request()->routeIs('companies.*') &&
        !request()->routeIs('company_profile*'))
    <div id="NoCompany" class="modal fade" role="dialog" aria-modal="true" tabindex="-1"
        aria-labelledby="company-registration-title" aria-describedby="company-registration-intro"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="registration-header">
                    <p class="registration-brand">GeMEMIS</p>
                    <h2 id="company-registration-title" class="modal-title" tabindex="-1">Company registration</h2>
                    <p id="company-registration-intro" class="registration-intro">
                        Enter your company details to get a GeMEMIS registration ID.
                    </p>
                </div>
                @include('company.first-company-registration', [
                    'countries' => App\Models\setting\Country::orderBy('country_name')->get(['country_name', 'country_short_code']),
                    'sectors' => App\Models\sectors::orderBy('sectorName')->get(['sectorID', 'sectorName']),
                ])
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/core/company-registration.js') }}"></script>
    <script>
        $(function () {
            $('#NoCompany').on('shown.bs.modal', function () {
                setTimeout(() => {
                    const invalid = this.querySelector('[aria-invalid="true"]');
                    const target = invalid?.nextElementSibling?.querySelector('.select2-selection')
                        || invalid || this.querySelector('#company-registration-title');
                    target.focus();
                }, 0);
            }).modal('show');
        });
    </script>
@endif

@if (
    $myUnupdatedCompanies > 0 &&
        Auth::user()->account_type == 2 &&
        !request()->routeIs('companies.*') &&
        !request()->routeIs('company_profile*'))
    <script>
        swal({
            title: "Unupdated Company Profile ({{ $myUnupdatedCompanies }})",
            text: "Please update your company details for the current year.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Update Now",
            cancelButtonText: "Later"
        }, function(isConfirm) {
            if (isConfirm) {
                window.location.href = "{{ route('companies.index') }}";
            }
        });
    </script>
@endif
