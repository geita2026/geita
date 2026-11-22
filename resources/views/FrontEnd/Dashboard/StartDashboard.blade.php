
<style>
    :root {
        --gememis-gold: #D4A017;
        --gememis-gold-light: #F8E7B0;
        --gememis-gold-dark: #9A6B08;
        --gememis-primary: #123B5D;
        --gememis-dark: #09283F;
        --gememis-light: #F8F6EF;
        --gememis-text: #253447;
        --gememis-muted: #64748b;
    }

    .gememis-home {
        padding: 25px 15px 40px;
    }

    /* WELCOME BANNER */
    .gememis-welcome {
        background: linear-gradient(120deg, #09283F, #123B5D 60%, #1B5274);
        color: #ffffff;
        padding: 38px;
        border-radius: 16px;
        margin-bottom: 30px;
        border-left: 6px solid var(--gememis-gold);
        box-shadow: 0 9px 28px rgba(9, 40, 63, .18);
        position: relative;
        overflow: hidden;
    }

    .gememis-welcome h2 {
        font-size: 29px;
        font-weight: 800;
        margin: 0 0 15px;
        color: #ffffff;
        letter-spacing: .3px;
    }

    .gememis-welcome .gememis-accent {
        color: #F2C75C;
    }

    .gememis-welcome p {
        font-size: 15px;
        line-height: 1.9;
        max-width: 780px;
        margin: 0;
        color: #f1f5f9;
    }

    .gememis-welcome-icon {
        font-size: 100px;
        color: var(--gememis-gold);
        opacity: .16;
        position: absolute;
        right: 45px;
        top: 22px;
    }

    .gememis-welcome-label {
        display: inline-block;
        background: rgba(212, 160, 23, .17);
        border: 1px solid rgba(242, 199, 92, .5);
        color: #F2C75C;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.2px;
        margin-bottom: 15px;
    }

    /* SECTION TITLE */
    .gememis-section-title {
        color: var(--gememis-primary);
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 22px;
    }

    .gememis-section-title i {
        color: var(--gememis-gold-dark);
    }

    /* MODULE CARD */
    .gememis-module-card {
        display: block;
        background: #ffffff;
        border-radius: 16px;
        padding: 28px;
        border: 1px solid #e8e3d6;
        border-top: 5px solid var(--gememis-gold);
        box-shadow: 0 6px 20px rgba(15, 23, 42, .07);
        transition: all .3s ease;
        text-decoration: none !important;
        color: var(--gememis-text) !important;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .gememis-module-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 15px 32px rgba(9, 40, 63, .15);
        border-color: var(--gememis-gold);
    }

    .gememis-module-icon {
        width: 68px;
        height: 68px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: linear-gradient(135deg, #FFF5D8, #F8E7B0);
        color: var(--gememis-gold-dark);
        font-size: 32px;
        margin-bottom: 20px;
        border: 1px solid #EBD18A;
    }

    .gememis-module-card h3 {
        font-size: 23px;
        font-weight: 800;
        color: var(--gememis-primary);
        margin: 0 0 15px;
    }

    .gememis-module-card p {
        color: var(--gememis-muted);
        font-size: 14px;
        line-height: 1.9;
        text-align: justify;
        margin-bottom: 22px;
    }

    .gememis-module-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid #eee9dc;
        color: var(--gememis-gold-dark);
        font-weight: 800;
        font-size: 13px;
    }

    .gememis-module-footer i {
        background: var(--gememis-primary);
        color: #ffffff;
        width: 32px;
        height: 32px;
        line-height: 32px;
        text-align: center;
        border-radius: 50%;
        transition: all .3s ease;
    }

    .gememis-module-card:hover .gememis-module-footer i {
        background: var(--gememis-gold-dark);
        transform: translateX(4px);
    }

    /* ACTIVE MODULE */
    .gememis-active {
        border-bottom: 5px solid #16a34a;
    }

    /* STATUS */
    .gememis-status {
        display: inline-block;
        padding: 7px 13px;
        border-radius: 30px;
        background: #ecfdf3;
        color: #166534;
        font-size: 11px;
        font-weight: 800;
        margin-bottom: 17px;
        letter-spacing: .5px;
    }

    /* FOOTER */
    .gememis-footer {
        margin-top: 40px;
        padding: 20px;
        border-top: 1px solid #e8e3d6;
        text-align: center;
    }

    .gememis-footer p {
        font-size: 12px;
        color: var(--gememis-muted);
        margin: 0;
        line-height: 1.8;
    }

    .gememis-footer strong {
        color: var(--gememis-gold-dark);
    }

    /* RESPONSIVE */
    @media (max-width: 767px) {
        .gememis-home {
            padding: 15px 5px 30px;
        }

        .gememis-welcome {
            padding: 27px 20px;
        }

        .gememis-welcome h2 {
            font-size: 23px;
            line-height: 1.4;
        }

        .gememis-welcome p {
            font-size: 13px;
        }

        .gememis-welcome-icon {
            font-size: 55px;
            right: 15px;
            top: 15px;
        }

        .gememis-module-card {
            padding: 22px;
            margin-bottom: 20px;
        }

        .gememis-module-card h3 {
            font-size: 20px;
        }
    }
</style>

<x-app-layout>

    <title>{{ env('APP_NAME') }} | GeMEMIS Home</title>

    <!-- PAGE HEADER -->
    <div class="page-header">

        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>

        <div class="breadcrumb-line breadcrumb-line-component">

            <ul class="breadcrumb">
                <li>
                    <a href="{{ route('startApp') }}">
                        <i class="icon-grid2 position-left"></i>
                        Start System
                    </a>
                </li>

                <li class="active">
                    GeMEMIS Dashboard
                </li>
            </ul>

            <ul class="breadcrumb-elements">
                {{-- @include('layouts.help') --}}
            </ul>

        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="content gememis-home">

        <!-- WELCOME SECTION -->
        <div class="gememis-welcome">

            <span class="gememis-welcome-label">
                GEITA MINERAL EXHIBITION MANAGEMENT
            </span>

            <i class="icon-office gememis-welcome-icon"></i>

            <h2>
                Welcome to <span class="gememis-accent">GeMEMIS</span>
            </h2>

            <p>
                GeMEMIS (Geita Mineral Exhibition Management Information
                System) is a digital management platform designed to
                support the planning, coordination and administration
                of mineral exhibitions in Geita.

                The system facilitates exhibition space allocation,
                exhibitor registration, booking management and related
                exhibition services, providing an efficient and
                coordinated environment for exhibition stakeholders.
            </p>

        </div>

        <!-- MODULE SECTION -->
        <div class="row">

            <div class="col-md-12">
                <h3 class="gememis-section-title">
                    <i class="icon-grid6 position-left"></i>
                    Available System Modules
                </h3>
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">

                <a href="{{ route('dashboard') }}"
                   class="gememis-module-card
                   {{ session('moduleNo') == 1 ? 'gememis-active' : '' }}">

                    <div class="gememis-module-icon">
                        <i class="icon-office"></i>
                    </div>

                    <span class="gememis-status">
                        <i class="icon-checkmark-circle"></i>
                        AVAILABLE
                    </span>

                    <h3>
                        GeMEMIS - SAMS
                    </h3>

                    <p>
                        The Space Allocation Management System (SAMS)
                        is a digital platform designed to simplify and
                        improve the management of mineral exhibition
                        spaces.

                        It supports exhibitor registration, exhibition
                        space allocation, booking management and
                        related exhibition services.

                        The module helps administrators and other
                        stakeholders coordinate exhibition activities
                        efficiently, improve service delivery and
                        strengthen the management of mineral exhibitions
                        in Geita.
                    </p>

                    <div class="gememis-module-footer">

                        <span>
                            Open Module
                        </span>

                        <i class="icon-arrow-right13"></i>

                    </div>

                </a>

            </div>

        </div>

        <!-- FOOTER INFORMATION -->
        <div class="gememis-footer">

            <p>
                <strong>GeMEMIS</strong><br>
                Geita Mineral Exhibition Management Information System
                <br>
                Digital Management for Mineral Exhibitions
            </p>

        </div>

    </div>

</x-app-layout>

<script src="{{ asset('assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/visualization/c3/c3.min.js') }}"></script>
