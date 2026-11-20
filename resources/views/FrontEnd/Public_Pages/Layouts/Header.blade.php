<!-- ================= TANTRADE RESPONSIVE HEADER ================= -->
<style>
    :root {
        --blue: #0d6efd;
        --blue-light: #e9f4ff;
        --text-blue: #0d6efd;
        --text-dark: #0b1f33;
    }

    body {
        padding-top: 85px;
    }

    /* ===== HEADER ===== */
    .header-modern {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 3000;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
    }

    .header-inner {
        padding: 12px 0;
    }

    /* ===== LOGO ===== */
    .logo img {
        max-height: 55px;
        width: auto;
        display: block;
    }

    /* ===== DESKTOP NAV (only visible on lg+) ===== */
    .nav-desktop {
        display: none;
        justify-content: flex-end;
        align-items: center;
    }

    @media (min-width: 992px) {
        .nav-desktop {
            display: flex;
        }
    }

    .nav-desktop > ul {
        list-style: none;
        display: flex;
        gap: 26px;
        margin: 0;
        padding: 0;
        align-items: center;
    }

    .nav-desktop li {
        position: relative;
    }

    .nav-desktop a {
        color: var(--text-blue);
        font-weight: 600;
        text-decoration: none;
        padding: 6px 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .nav-desktop a:hover {
        color: #084298;
    }

    .nav-desktop i {
        transition: transform .3s ease;
    }

    .nav-desktop li:hover > a i {
        transform: rotate(180deg);
    }

    /* ===== DROPDOWN ===== */
    .dropdown {
        position: absolute;
        top: 120%;
        left: 0;
        min-width: 260px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 12px 30px rgba(13,110,253,.15);
        padding: 6px 0;

        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all .25s ease;
    }

    .nav-desktop li:hover > .dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .dropdown li {
        width: 100%;
    }

    .dropdown a {
        display: block;
        padding: 12px 20px;
        color: var(--text-blue);
        font-weight: 600;
    }

    .dropdown a:hover {
        background: var(--blue-light);
        color: var(--blue);
    }

    /* ===== LOGIN BUTTON ===== */
    .login-btn {
        padding: 10px 22px;
        border-radius: 30px;
        border: 2px solid var(--blue);
        color: var(--blue) !important;
        background: transparent;
        transition: all .25s ease;
    }

    .login-btn:hover {
        background: var(--blue);
        color: #fff !important;
    }

    /* ===== MOBILE TOGGLE (hamburger icon) ===== */
    .mobile-toggle {
        font-size: 26px;
        color: var(--blue);
        cursor: pointer;
        display: none;
    }

    @media (max-width: 991px) {
        .mobile-toggle {
            display: block;
        }
    }

    /* ===== MOBILE SIDEBAR ===== */
    .mobile-sidebar {
        display: none;
    }

    @media (max-width: 991px) {
        .mobile-sidebar {
            display: block;
        }

        .mobile-sidebar .sidebar-panel {
            position: fixed;
            top: 0;
            left: -300px;
            width: 280px;
            height: 100%;
            background: #ffffff;
            z-index: 4000;
            overflow-y: auto;
            box-shadow: 2px 0 12px rgba(0,0,0,.15);
            transition: left .3s ease;
        }

        .mobile-sidebar .sidebar-panel.open {
            left: 0;
        }

        .mobile-sidebar .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
            background: #ffffff;
        }

        .mobile-sidebar .sidebar-header img {
            max-height: 40px;
            width: auto;
        }

        .mobile-sidebar .sidebar-close {
            font-size: 28px;
            color: var(--blue);
            cursor: pointer;
            background: none;
            border: none;
            line-height: 1;
        }

        .mobile-sidebar .sidebar-item {
            display: block;
            padding: 14px 20px;
            color: var(--text-blue);
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            border-bottom: 1px solid #f0f0f0;
        }

        .mobile-sidebar .sidebar-item:hover {
            background: var(--blue-light);
            color: var(--blue);
        }

        .mobile-sidebar .sidebar-group-title {
            padding: 14px 20px;
            color: var(--text-dark);
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f0f0f0;
        }

        .mobile-sidebar .sidebar-group-title:hover {
            background: var(--blue-light);
        }

        .mobile-sidebar .sidebar-group-title i {
            transition: transform .3s ease;
        }

        .mobile-sidebar .sidebar-group-title.open i {
            transform: rotate(180deg);
        }

        .mobile-sidebar .sidebar-submenu {
            display: none;
            background: #f8fafc;
        }

        .mobile-sidebar .sidebar-submenu.open {
            display: block;
        }

        .mobile-sidebar .sidebar-submenu .sidebar-item {
            padding-left: 36px;
            font-weight: 500;
            font-size: 14px;
        }

        .mobile-sidebar .sidebar-login {
            margin: 20px;
            text-align: center;
            padding: 12px 22px !important;
            border-radius: 30px;
            border: 2px solid var(--blue);
            color: var(--blue) !important;
            font-weight: 700;
            background: transparent;
            display: block;
            text-decoration: none;
            transition: all .25s ease;
        }

        .mobile-sidebar .sidebar-login:hover {
            background: var(--blue);
            color: #fff !important;
        }

        .mobile-sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,.4);
            z-index: 3999 !important;
        }

        .mobile-sidebar-overlay.active {
            display: block;
        }
    }
</style>


<header class="header-modern">
    <div class="container header-inner">
        <div class="row align-items-center">
            <div class="col-6">
                <div class="logo">
                    <a href="{{ route('index') }}">
                        <img src="{{ asset('images/logo.jpg') }}" alt="TanTrade Logo">
                    </a>
                </div>
            </div>

            <div class="col-6 text-end" style="padding: 8px 16px; right: 21px">
                <i class="fa fa-bars mobile-toggle"></i>
            </div>

            <!-- DESKTOP NAV (hidden on mobile, sidebar used instead) -->
            <div class="col-12 nav-desktop d-none d-lg-flex">
                <ul>
                    <li><a href="{{ route('index') }}">Home</a></li>

                    <li>
                        <a href="#">About SabaSaba <i class="fa fa-angle-down"></i></a>
                        <div class="dropdown">
                            <ul>
                                <li><a href="{{ route('About_ditf') }}">Understanding SabaSaba</a></li>
                                <li><a href="{{ route('preview_programs.index') }}">Programs</a></li>
                                <li><a href="{{ route('ditf_map') }}">Ground Map</a></li>
                                <li><a href="{{ route('discover_exhibitor.index') }}">Discover Exhibitors</a></li>
                                <li><a href="{{ route('exhibited_products.index') }}">Exhibited Products</a></li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#">Trade Promotion Events <i class="fa fa-angle-down"></i></a>
                        <div class="dropdown">
                            <ul>
                                <li><a href="{{ route('/') }}">Apply Event Permit</a></li>
                                <li><a href="{{ route('approved_events') }}">Approved Events</a></li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#">Online Booking <i class="fa fa-angle-down"></i></a>
                        <div class="dropdown">
                            <ul>
                                <li><a href="{{ route('exhibition_space_booking.index') }}">Exhibition Space</a></li>
                                <li><a href="{{ route('sponsorship_package_groups') }}">Sponsorship Packages</a></li>
                                <li><a href="{{ route('online_booking.index') }}">Halls & Offices</a></li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="{{ route('login') }}" class="p-10">
                            Login<i class="fa fa-unlock"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
<!-- ================= END HEADER ================= -->

<!-- ================= MOBILE SIDEBAR ================= -->
<div class="mobile-sidebar">
    <div class="mobile-sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

    <div class="sidebar-panel" id="mobileSidebar">
        {{-- Sidebar Header --}}
        <div class="sidebar-header">
            <a href="{{ route('index') }}">
                <img src="{{ asset('images/logo.jpg') }}" alt="TanTrade Logo">
            </a>
            <button class="sidebar-close" onclick="closeMobileSidebar()">&times;</button>
        </div>

        {{-- Home --}}
        <a href="{{ route('index') }}" class="sidebar-item">
            <i class="fa fa-home" style="margin-right:8px;"></i> Home
        </a>

        {{-- About SabaSaba --}}
        <div class="sidebar-group-title" onclick="toggleSubmenu(this)">
            <span><i class="fa fa-info-circle" style="margin-right:8px;"></i> About SabaSaba</span>
            <i class="fa fa-angle-down"></i>
        </div>
        <div class="sidebar-submenu">
            <a href="{{ route('About_ditf') }}" class="sidebar-item">Understanding SabaSaba</a>
            <a href="{{ route('preview_programs.index') }}" class="sidebar-item">Programs</a>
            <a href="{{ route('ditf_map') }}" class="sidebar-item">Ground Map</a>
            <a href="{{ route('discover_exhibitor.index') }}" class="sidebar-item">Discover Exhibitors</a>
            <a href="{{ route('exhibited_products.index') }}" class="sidebar-item">Exhibited Products</a>
        </div>

        {{-- Trade Promotion Events --}}
        <div class="sidebar-group-title" onclick="toggleSubmenu(this)">
            <span><i class="fa fa-calendar" style="margin-right:8px;"></i> Trade Promotion Events</span>
            <i class="fa fa-angle-down"></i>
        </div>
        <div class="sidebar-submenu">
            <a href="{{ route('/') }}" class="sidebar-item">Apply Event Permit</a>
            <a href="{{ route('approved_events') }}" class="sidebar-item">Approved Events</a>
        </div>

        {{-- Online Booking --}}
        <div class="sidebar-group-title" onclick="toggleSubmenu(this)">
            <span><i class="fa fa-bookmark" style="margin-right:8px;"></i> Online Booking</span>
            <i class="fa fa-angle-down"></i>
        </div>
        <div class="sidebar-submenu">
            <a href="{{ route('exhibition_space_booking.index') }}" class="sidebar-item">Exhibition Space</a>
            <a href="{{ route('sponsorship_package_groups') }}" class="sidebar-item">Sponsorship Packages</a>
            <a href="{{ route('online_booking.index') }}" class="sidebar-item">Halls & Offices</a>
        </div>

        {{-- Login --}}
        <a href="{{ route('login') }}" class="sidebar-login">
            <i class="fa fa-unlock" style="margin-right:6px;"></i> Login
        </a>
    </div>
</div>
<!-- ================= END MOBILE SIDEBAR ================= -->

<script>
    function openMobileSidebar() {
        document.getElementById('mobileSidebar').classList.add('open');
        document.getElementById('sidebarOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        document.getElementById('mobileSidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    function toggleSubmenu(el) {
        el.classList.toggle('open');
        var submenu = el.nextElementSibling;
        submenu.classList.toggle('open');
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.mobile-toggle').addEventListener('click', function () {
            openMobileSidebar();
        });
    });
</script>
