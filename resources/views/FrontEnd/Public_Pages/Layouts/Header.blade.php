<!-- ================= GeMEMIS RESPONSIVE HEADER ================= -->
<style>
    body {
        padding-top: 86px;
    }

    /* The legacy public pages wrap this header in <header class="header">, which
       the Medipro stylesheet paints white. Keep that wrapper out of the way so
       the GeMEMIS bar reads the same on every page. */
    header.header {
        background: transparent !important;
        box-shadow: none !important;
    }

    /* …and the same stylesheet paints .container white at z-index 333, which
       would cut a white block out of the bar. */
    .gem-header .header-inner {
        background: transparent !important;
        z-index: auto !important;
    }

    /* ===== GeMEMIS NAVBAR ===== */
    .gem-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        z-index: 3000;
        background: rgba(7, 5, 6, 0.90);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-bottom: 1.5px solid rgba(255, 191, 0, 0.22);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.65), 0 0 20px rgba(255, 191, 0, 0.08);
        transition: all 0.3s ease;
    }

    .header-inner {
        padding: 12px 0;
    }

    /* ===== BRANDING ===== */
    .gem-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .gem-brand img.brand-logo {
        max-height: 52px;
        width: auto;
        filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.55));
    }

    .gem-brand img.brand-mark {
        max-height: 48px;
        width: auto;
        filter: drop-shadow(0 0 8px rgba(255, 191, 0, 0.45));
    }

    .brand-text {
        line-height: 1.2;
    }

    .brand-title {
        font-family: "Noto Sans Display", "Montserrat", sans-serif;
        font-weight: 700;
        font-size: 17px;
        color: #ffffff;
        letter-spacing: 0.02em;
        display: block;
    }

    .brand-subtitle {
        font-size: 11px;
        color: #FFBF00;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        display: block;
    }

    /* ===== DESKTOP NAV ===== */
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
        gap: 22px;
        margin: 0;
        padding: 0;
        align-items: center;
    }

    .nav-desktop li {
        position: relative;
    }

    .nav-desktop a.nav-link-custom {
        color: rgba(255, 255, 255, 0.85);
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        padding: 8px 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.02em;
        transition: color 0.2s ease;
    }

    .nav-desktop a.nav-link-custom:hover,
    .nav-desktop li:hover > a.nav-link-custom {
        color: #FFBF00;
    }

    .nav-desktop a.nav-link-custom i {
        font-size: 11px;
        transition: transform 0.25s ease;
    }

    .nav-desktop li:hover > a.nav-link-custom i {
        transform: rotate(180deg);
        color: #FFBF00;
    }

    /* ===== DROPDOWN ===== */
    .gem-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        min-width: 260px;
        background: #0d0a0f;
        border: 1.5px solid rgba(255, 191, 0, 0.3);
        border-radius: 18px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.85), 0 0 24px rgba(255, 191, 0, 0.15);
        padding: 8px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 3200;
    }

    .nav-desktop li:hover > .gem-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .gem-dropdown ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .gem-dropdown a {
        display: flex;
        align-items: center;
        padding: 11px 20px;
        color: rgba(255, 255, 255, 0.82);
        font-weight: 500;
        font-size: 13.5px;
        text-decoration: none;
        transition: all 0.18s ease;
    }

    .gem-dropdown a:hover {
        background: rgba(255, 191, 0, 0.12);
        color: #FFBF00;
        padding-left: 24px;
    }

    /* ===== LOGIN BUTTON ===== */
    .btn-header-login {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 22px;
        border-radius: 9999px;
        border: 1.5px solid #FFBF00;
        color: #070506 !important;
        background: linear-gradient(135deg, #FFBF00 0%, #E5A900 100%);
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        box-shadow: 0 4px 18px rgba(255, 191, 0, 0.35);
        transition: all 0.25s ease;
    }

    .btn-header-login:hover {
        background: linear-gradient(135deg, #FFD033 0%, #FFBF00 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(255, 191, 0, 0.55);
    }

    /* ===== MOBILE TOGGLE ===== */
    .mobile-toggle-btn {
        display: none;
        font-size: 26px;
        color: #FFBF00;
        cursor: pointer;
        background: transparent;
        border: none;
        padding: 6px;
        line-height: 1;
    }

    @media (max-width: 991px) {
        .mobile-toggle-btn {
            display: inline-block;
        }
    }

    /* ===== MOBILE SIDEBAR DRAWER ===== */
    .gem-sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.78);
        backdrop-filter: blur(6px);
        z-index: 3999;
    }

    .gem-sidebar-overlay.active {
        display: block;
    }

    .gem-sidebar {
        position: fixed;
        top: 0;
        left: -320px;
        width: 300px;
        height: 100%;
        background: #080609;
        border-right: 1.5px solid rgba(255, 191, 0, 0.25);
        z-index: 4000;
        overflow-y: auto;
        box-shadow: 6px 0 35px rgba(0, 0, 0, 0.9);
        transition: left 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .gem-sidebar.open {
        left: 0;
    }

    .gem-sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 20px;
        border-bottom: 1px solid rgba(255, 191, 0, 0.2);
        background: #0b090d;
    }

    .gem-sidebar-close {
        font-size: 28px;
        color: #FFBF00;
        cursor: pointer;
        background: none;
        border: none;
        line-height: 1;
    }

    .gem-sidebar-item {
        display: block;
        padding: 13px 20px;
        color: rgba(255, 255, 255, 0.85);
        font-weight: 600;
        font-size: 14.5px;
        text-decoration: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.2s ease;
    }

    .gem-sidebar-item:hover {
        background: rgba(255, 191, 0, 0.1);
        color: #FFBF00;
        padding-left: 24px;
    }

    .gem-sidebar-group {
        padding: 13px 20px;
        color: #FFBF00;
        font-weight: 700;
        font-size: 14.5px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .gem-sidebar-group i {
        transition: transform 0.25s ease;
    }

    .gem-sidebar-group.open i {
        transform: rotate(180deg);
    }

    .gem-sidebar-sub {
        display: none;
        background: #050305;
    }

    .gem-sidebar-sub.open {
        display: block;
    }

    .gem-sidebar-sub .gem-sidebar-item {
        padding-left: 36px;
        font-size: 13.5px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.7);
    }

    .gem-sidebar-sub .gem-sidebar-item:hover {
        color: #FFBF00;
        padding-left: 40px;
    }
</style>

<header class="gem-header">
    <div class="container header-inner">
        <div class="row align-items-center">
            <!-- Brand / Logos -->
            <div class="col-8 col-lg-4">
                <a href="{{ route('index') }}" class="gem-brand">
                    <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Court of Arms of the United Republic of Tanzania" class="brand-logo">
                    <div class="brand-text d-none d-sm-block">
                        <span class="brand-title">GeMEMIS</span>
                        <span class="brand-subtitle">Geita Mineral Exhibition</span>
                    </div>
                </a>
            </div>

            <!-- Mobile Hamburger -->
            <div class="col-4 d-lg-none text-end">
                <button class="mobile-toggle-btn" onclick="openMobileSidebar()" aria-label="Toggle Navigation">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <!-- Desktop Nav Links -->
            <div class="col-12 col-lg-8 nav-desktop">
                <ul>
                    <li>
                        <a href="{{ route('index') }}" class="nav-link-custom">
                            <i class="fa fa-home"></i> Home
                        </a>
                    </li>

                    <!-- The Exhibition -->
                    <li>
                        <a href="#" class="nav-link-custom">
                            The Exhibition <i class="fa fa-chevron-down"></i>
                        </a>
                        <div class="gem-dropdown">
                            <ul>
                                <li><a href="{{ route('About_ditf') }}"><i class="fa fa-info-circle me-2 text-warning"></i> About the exhibition</a></li>
                                <li><a href="{{ route('preview_programs.index') }}"><i class="fa fa-calendar me-2 text-warning"></i> Programme</a></li>
                                <li><a href="{{ route('ditf_map') }}"><i class="fa fa-map-marker me-2 text-warning"></i> Map of Geita</a></li>
                                <li><a href="{{ route('discover_exhibitor.index') }}"><i class="fa fa-search me-2 text-warning"></i> Discover exhibitors</a></li>
                                <li><a href="{{ route('exhibited_products.index') }}"><i class="fa fa-th-large me-2 text-warning"></i> Exhibited minerals</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Book space -->
                    <li>
                        <a href="#" class="nav-link-custom">
                            Book space <i class="fa fa-chevron-down"></i>
                        </a>
                        <div class="gem-dropdown">
                            <ul>
                                <li><a href="{{ route('exhibition_space_booking.index') }}"><i class="fa fa-building-o me-2 text-warning"></i> Exhibition space</a></li>
                                <li><a href="{{ route('sponsorship_package_groups') }}"><i class="fa fa-trophy me-2 text-warning"></i> Sponsorship packages</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Login Button -->
                    <li>
                        <a href="{{ route('login') }}" class="btn-header-login">
                            <i class="fa fa-lock"></i> Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<!-- ================= MOBILE SIDEBAR DRAWER ================= -->
<div class="gem-sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

<div class="gem-sidebar" id="mobileSidebar">
    <div class="gem-sidebar-header">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Court of Arms of the United Republic of Tanzania" style="height:40px;">
            <span style="font-weight:700;color:#FFBF00;font-size:14px;">GeMEMIS</span>
        </div>
        <button class="gem-sidebar-close" onclick="closeMobileSidebar()" aria-label="Close">&times;</button>
    </div>

    <!-- Home -->
    <a href="{{ route('index') }}" class="gem-sidebar-item">
        <i class="fa fa-home me-2 text-warning"></i> Home
    </a>

    <!-- The Exhibition Dropdown -->
    <div class="gem-sidebar-group" onclick="toggleSubmenu('subAbout')">
        <span><i class="fa fa-bookmark-o me-2"></i> The Exhibition</span>
        <i class="fa fa-chevron-down"></i>
    </div>
    <div class="gem-sidebar-sub" id="subAbout">
        <a href="{{ route('About_ditf') }}" class="gem-sidebar-item">About the exhibition</a>
        <a href="{{ route('preview_programs.index') }}" class="gem-sidebar-item">Programs</a>
        <a href="{{ route('ditf_map') }}" class="gem-sidebar-item">Map of Geita</a>
        <a href="{{ route('discover_exhibitor.index') }}" class="gem-sidebar-item">Discover exhibitors</a>
        <a href="{{ route('exhibited_products.index') }}" class="gem-sidebar-item">Exhibited minerals</a>
    </div>

    <!-- Book space -->
    <div class="gem-sidebar-group" onclick="toggleSubmenu('subBooking')">
        <span><i class="fa fa-check-circle me-2"></i> Book space</span>
        <i class="fa fa-chevron-down"></i>
    </div>
    <div class="gem-sidebar-sub" id="subBooking">
        <a href="{{ route('exhibition_space_booking.index') }}" class="gem-sidebar-item">Exhibition Space</a>
        <a href="{{ route('sponsorship_package_groups') }}" class="gem-sidebar-item">Sponsorship packages</a>
    </div>

    <!-- Login & Register in Sidebar -->
    <div style="padding: 20px;">
        <a href="{{ route('login') }}" class="btn-brand w-100 mb-2">
            <i class="fa fa-lock me-1"></i> Sign in
        </a>
        <a href="{{ route('registration') }}" class="btn-brand-outline w-100">
            <i class="fa fa-user-plus me-1"></i> Create an account
        </a>
    </div>
</div>

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
    function toggleSubmenu(id) {
        const sub = document.getElementById(id);
        const parent = event.currentTarget;
        if (sub) {
            sub.classList.toggle('open');
            parent.classList.toggle('open');
        }
    }
</script>
