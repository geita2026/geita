<link rel="icon" href="{{ asset('images/coat-of-arms.svg') }}">
<link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/components.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/colors.css') }}" rel="stylesheet" type="text/css">

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Noto+Sans+Display:wght@600;700&display=swap" rel="stylesheet">

<!-- GeMEMIS design system -->
<link rel="stylesheet" href="{{ asset('assets/css/gememis-theme.css') }}">

<style>
    body {
        background-color: #070506 !important;
        background-image: radial-gradient(1200px 480px at 50% -8%, rgba(255, 191, 0, 0.22), transparent 60%) !important;
        color: #ffffff !important;
        font-family: "Montserrat", sans-serif !important;
    }
    .panel, .panel-body, .panel-heading {
        background-color: #0c0a0e !important;
        border-color: rgba(255, 191, 0, 0.22) !important;
        color: #ffffff !important;
    }
    .registration-form {
        background: #0f0c13 !important;
        border: 1.5px solid rgba(255, 191, 0, 0.25) !important;
        border-radius: 20px !important;
        padding: 30px !important;
        box-shadow: 0 15px 40px rgba(0,0,0,0.6) !important;
    }
    .form-control {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 191, 0, 0.25) !important;
        color: #ffffff !important;
        border-radius: 10px !important;
    }
    .form-control:focus {
        border-color: #FFBF00 !important;
        box-shadow: 0 0 12px rgba(255, 191, 0, 0.35) !important;
        background: rgba(255, 255, 255, 0.08) !important;
    }
    .btn-primary {
        background: linear-gradient(135deg, #FFBF00 0%, #E5A900 100%) !important;
        border: none !important;
        color: #000000 !important;
        font-weight: 700 !important;
        border-radius: 9999px !important;
        box-shadow: 0 4px 15px rgba(255, 191, 0, 0.35) !important;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #FFD033 0%, #FFBF00 100%) !important;
        transform: translateY(-2px);
    }
    .text-muted {
        color: rgba(255, 255, 255, 0.6) !important;
    }
</style>
