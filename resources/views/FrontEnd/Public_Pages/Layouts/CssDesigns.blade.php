<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="keywords" content="GeMEMIS,Geita,mineral exhibition,madini,maonesho ya madini,exhibition space,space allocation,Tanzania">
<meta name="description" content="GeMEMIS — Geita Mineral Exhibition Management Information System. Space planning, exhibitor registration, allocation, billing and occupancy for the Geita mineral exhibitions.">
<meta name='copyright' content='Geita Mineral Exhibition Secretariat'>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="icon" href="{{ asset('images/coat-of-arms.svg') }}">

<!-- Google Fonts: Manrope (the GeMEMIS type family) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Noto+Sans+Display:wght@500;600;700;800&display=swap" rel="stylesheet">

<!-- Legacy Medipro theme stack (still used by every public page that has not been redesigned) -->
<link rel="stylesheet" href="{{ asset('frontEndAssets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('frontEndAssets/css/nice-select.css') }}">
<link rel="stylesheet" href="{{ asset('frontEndAssets/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('frontEndAssets/css/icofont.css') }}">
<link rel="stylesheet" href="{{ asset('frontEndAssets/css/slicknav.min.css') }}">
<link rel="stylesheet" href="{{ asset('frontEndAssets/css/owl-carousel.css') }}">
<link rel="stylesheet" href="{{ asset('frontEndAssets/css/datepicker.css') }}">
<link rel="stylesheet" href="{{ asset('frontEndAssets/css/animate.min.css') }}">
<link rel="stylesheet" href="{{ asset('frontEndAssets/css/magnific-popup.css') }}">
<link rel="stylesheet" href="{{ asset('frontEndAssets/css/normalize.css') }}">
<link rel="stylesheet" href="{{ asset('frontEndAssets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('frontEndAssets/css/responsive.css') }}">
<link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">

<!-- Bootstrap 5 + Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- GeMEMIS design system -->
<link rel="stylesheet" href="{{ asset('assets/css/gememis-theme.css') }}">

<style>
    /* The Court of Arms is a vector: without a cap it fills whatever box it is
       dropped into on the legacy pages. */
    .section-title img[src$=".svg"],
    .choose-left img[src$=".svg"] {
        height: 110px;
        width: auto;
        max-width: 100%;
    }

    .zoom {
        transition: transform .3s ease;
    }
    .zoom:hover {
        transform: translateY(-4px);
    }
    a {
        text-decoration: none;
    }

    /* ---------------------------------------------------------------
       Golden (dark) treatment — scoped to <body class="gememis-theme">
       so that public pages still on the legacy Medipro theme keep
       their original light layout.
       --------------------------------------------------------------- */
    body.gememis-theme {
        background-color: #070506 !important;
        background-image: radial-gradient(1200px 480px at 50% -8%, rgba(255, 191, 0, 0.22), transparent 60%) !important;
        color: #ffffff !important;
        font-family: "Montserrat", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }
    body.gememis-theme .card,
    body.gememis-theme .panel {
        background: #0c0a0e !important;
        border: 1px solid rgba(255, 191, 0, 0.2) !important;
        color: #ffffff !important;
        border-radius: 20px !important;
    }
    body.gememis-theme .card-body,
    body.gememis-theme .panel-body {
        color: #ffffff !important;
    }
    body.gememis-theme .text-muted {
        color: rgba(255, 255, 255, 0.6) !important;
    }
    body.gememis-theme .text-dark {
        color: #ffffff !important;
    }
    body.gememis-theme .bg-light,
    body.gememis-theme .bg-white {
        background-color: transparent !important;
    }
    body.gememis-theme .modal-content {
        background: #0c0a0e !important;
        border: 1.5px solid rgba(255, 191, 0, 0.35) !important;
        color: #fff !important;
        border-radius: 24px !important;
        box-shadow: 0 20px 60px rgba(0,0,0,0.85), 0 0 30px rgba(255, 191, 0, 0.15) !important;
    }
    body.gememis-theme .modal-header,
    body.gememis-theme .modal-footer {
        border-color: rgba(255, 191, 0, 0.2) !important;
    }
</style>
