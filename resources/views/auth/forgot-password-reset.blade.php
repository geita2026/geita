<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Set a new password | GeMEMIS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="{{ asset('images/coat-of-arms.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Noto+Sans+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Golden Design System -->
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-forms.css') }}">

    <!-- Core jQuery -->
    <script src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>

    <style>
        body.reset-page-body {
            margin: 0;
            min-height: 100vh;
            font-family: "Montserrat", sans-serif;
            background-color: #070506;
            background-image: radial-gradient(1200px 480px at 50% -8%, rgba(255, 191, 0, 0.25), transparent 60%);
            color: #ffffff;
            overflow-x: hidden;
            position: relative;
        }

        .recovery-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .reset-card-box {
            max-width: 460px;
            width: 100%;
            padding: 42px 36px;
        }

        .gold-floating-field {
            position: relative;
            margin-bottom: 20px;
        }

        .gold-floating-field input.form-control {
            height: 54px;
            background: var(--gem-field-bg) !important;
            border: 1px solid var(--gem-field-border) !important;
            border-radius: var(--gem-control-radius) !important;
            color: #ffffff !important;
            padding: 20px 42px 6px 16px !important;
            font-size: 14.5px !important;
            transition: all 0.2s ease;
        }

        .gold-floating-field input.form-control:focus {
            background: var(--gem-field-bg) !important;
            border-color: var(--gem-field-focus) !important;
            box-shadow: 0 0 18px rgba(255, 191, 0, 0.35) !important;
            outline: none !important;
        }

        .gold-floating-field label {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.55);
            pointer-events: none;
            transition: all 0.2s ease;
            margin: 0;
        }

        .gold-floating-field input:focus + label,
        .gold-floating-field input:not(:placeholder-shown) + label {
            top: 14px;
            font-size: 11px;
            font-weight: 600;
            color: #FFBF00;
            letter-spacing: 0.03em;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #FFBF00;
            font-size: 18px;
            opacity: 0.8;
            transition: opacity 0.2s ease;
            z-index: 5;
        }
    </style>
</head>

<body class="reset-page-body">

<div class="recovery-wrapper">
    <div class="brand-card reset-card-box">
        <form action="{{ route('password.reset.submit') }}" method="POST" id="resetForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="text-center mb-4">
                <div class="d-inline-flex p-3 rounded-4 mb-2" style="background:rgba(255, 191, 0,0.12);border:1.5px solid rgba(255, 191, 0,0.35);">
                    <i class="bi bi-key text-warning fs-3"></i>
                </div>
                <h3 style="font-weight:700;color:#ffffff;margin-bottom:6px;">Set New Password</h3>
                <p class="text-white-50" style="font-size:13.5px;margin:0;">
                    Create a strong password with at least 8 characters including uppercase, lowercase, numbers, and symbols.
                </p>
            </div>

            @if (Session::get('status'))
                <div class="alert alert-brand-info mb-3">
                    <i class="bi bi-info-circle-fill me-2"></i> {{ Session::get('status') }}
                </div>
            @endif

            <div class="gold-floating-field">
                <input type="password"
                       name="password"
                       id="newPasswordInput"
                       class="form-control"
                       placeholder=" "
                       required
                       autocomplete="new-password">
                <label for="newPasswordInput">New Password *</label>
                <i class="bi bi-eye toggle-password" data-target="#newPasswordInput"></i>
                @error('password')
                    <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="gold-floating-field">
                <input type="password"
                       name="password_confirmation"
                       id="confirmPasswordInput"
                       class="form-control"
                       placeholder=" "
                       required
                       autocomplete="new-password">
                <label for="confirmPasswordInput">Confirm New Password *</label>
                <i class="bi bi-eye toggle-password" data-target="#confirmPasswordInput"></i>
            </div>

            <button type="submit" id="submitBtn" class="btn-brand w-100 py-3 mt-2">
                <span>Update Password</span>
                <i class="bi bi-check2-circle fs-5"></i>
            </button>

            <a href="{{ route('login') }}" class="btn-brand-outline w-100 py-2.5 mt-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Login
            </a>
        </form>
    </div>
</div>

<script>
    $('.toggle-password').on('click', function () {
        const target = $($(this).data('target'));
        const isPassword = target.attr('type') === 'password';
        target.attr('type', isPassword ? 'text' : 'password');
        $(this).toggleClass('bi-eye bi-eye-slash');
    });

    $('#resetForm').on('submit', function () {
        $('#submitBtn').prop('disabled', true).html('<span>Saving password...</span>');
    });
</script>

</body>
</html>
