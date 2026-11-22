<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Create your account | GeMEMIS</title>
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
        body.register-page-body {
            margin: 0;
            min-height: 100vh;
            font-family: "Montserrat", sans-serif;
            background-color: #070506;
            background-image: radial-gradient(1200px 480px at 50% -8%, rgba(255, 191, 0, 0.25), transparent 60%);
            color: #ffffff;
            overflow-x: hidden;
            position: relative;
        }

        canvas#goldNetworkCanvas {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        .register-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 20px;
        }

        .register-card-box {
            max-width: 860px;
            width: 100%;
            padding: 45px 42px;
        }

        @media (max-width: 768px) {
            .register-card-box {
                padding: 32px 24px;
            }
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-crest-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .brand-crest-group img.logo-crest {
            height: 78px;
            width: auto;
            filter: drop-shadow(0 6px 18px rgba(255, 191, 0, 0.30));
        }

        .register-header h3 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .register-header p {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.65);
            margin: 0;
        }

        .form-section-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #FFBF00;
            margin: 28px 0 18px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255, 191, 0, 0.25);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .gold-floating-field {
            position: relative;
            margin-bottom: 18px;
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

        .toggle-password:hover {
            opacity: 1;
        }

        /* Password Strength Meter */
        .password-meter {
            height: 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-meter div {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
        }

        .pw-weak { background: #dc3545; }
        .pw-medium { background: #FFBF00; }
        .pw-strong { background: #198754; }

        .password-hint {
            font-size: 12px;
            margin-top: 4px;
        }

        .match-ok { color: #28a745; font-size: 12px; margin-top: 4px; }
        .match-bad { color: #dc3545; font-size: 12px; margin-top: 4px; }

        .form-check-input {
            accent-color: #FFBF00;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 191, 0, 0.3);
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #FFBF00;
            border-color: #FFBF00;
        }

        .actions-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 191, 0, 0.2);
        }
    </style>
</head>

<body class="register-page-body">

<canvas id="goldNetworkCanvas"></canvas>

<div class="register-wrapper">
    <div class="brand-card register-card-box">
        <form action="{{ route('register') }}" method="POST" id="myForm">
            @csrf

            <!-- Header -->
            <div class="register-header">
                <div class="brand-crest-group">
                    <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Court of Arms of the United Republic of Tanzania" class="logo-crest">
                </div>
                <h3>Create New Account</h3>
                <p>Register to apply for exhibition space, permits and the other GeMEMIS services</p>
            </div>

            <!-- Session Alerts -->
            @if (Session::get('fail'))
                <div class="alert alert-brand-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ Session::get('fail') }}
                </div>
            @endif

            @if (Session::get('success'))
                <div class="alert alert-brand-success mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i> Account created successfully. Please check your email for activation instructions.
                </div>
            @endif

            <!-- Section 1: Personal Information -->
            <div class="form-section-title">
                <i class="bi bi-person-badge"></i> Personal Information
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="gold-floating-field">
                        <input type="text"
                               name="firstName"
                               id="firstNameInput"
                               class="form-control required-field"
                               placeholder=" "
                               value="{{ old('firstName') }}"
                               required
                               autocomplete="given-name">
                        <label for="firstNameInput">First Name *</label>
                        @error('firstName')
                            <span class="text-danger" style="font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="gold-floating-field">
                        <input type="text"
                               name="lastName"
                               id="lastNameInput"
                               class="form-control required-field"
                               placeholder=" "
                               value="{{ old('lastName') }}"
                               required
                               autocomplete="family-name">
                        <label for="lastNameInput">Last Name *</label>
                        @error('lastName')
                            <span class="text-danger" style="font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="gold-floating-field">
                        <input type="tel"
                               name="phoneNumber"
                               id="phoneInput"
                               class="form-control required-field"
                               placeholder=" "
                               value="{{ old('phoneNumber') }}"
                               required
                               autocomplete="tel">
                        <label for="phoneInput">Phone Number *</label>
                        @error('phoneNumber')
                            <span class="text-danger" style="font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="gold-floating-field">
                        <input type="email"
                               name="userEmail"
                               id="emailInput"
                               class="form-control required-field"
                               placeholder=" "
                               value="{{ old('userEmail') }}"
                               required
                               autocomplete="email">
                        <label for="emailInput">Email Address *</label>
                        @error('userEmail')
                            <span class="text-danger" style="font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Account Security -->
            <div class="form-section-title">
                <i class="bi bi-shield-lock"></i> Account Security
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="gold-floating-field mb-1">
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control required-field"
                               placeholder=" "
                               required
                               autocomplete="new-password">
                        <label for="password">Password *</label>
                        <i class="bi bi-eye toggle-password" data-target="#password"></i>
                    </div>
                    <div class="password-meter">
                        <div id="passwordStrengthBar"></div>
                    </div>
                    <div id="passwordHint" class="password-hint text-white-50"></div>
                    @error('password')
                        <span class="text-danger" style="font-size:12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <div class="gold-floating-field mb-1">
                        <input type="password"
                               id="confirmPassword"
                               name="password_confirmation"
                               class="form-control required-field"
                               placeholder=" "
                               required
                               autocomplete="new-password">
                        <label for="confirmPassword">Confirm Password *</label>
                        <i class="bi bi-eye toggle-password" data-target="#confirmPassword"></i>
                    </div>
                    <div id="matchStatus"></div>
                    @error('password_confirmation')
                        <span class="text-danger" style="font-size:12px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Terms Checkbox -->
            <div class="form-check mt-3 mb-2">
                <input type="checkbox" id="termsCheck" class="form-check-input required-field" required>
                <label class="form-check-label text-white-50" for="termsCheck" style="font-size:13.5px;">
                    I have read and agree to the
                    <a href="{{ route('terms_and_condition.index') }}" class="text-warning text-decoration-none fw-semibold">Terms &amp; Conditions</a>
                </label>
            </div>

            <!-- Actions Bar -->
            <div class="actions-bar">
                <a href="{{ route('login') }}" class="btn-brand-outline">
                    <i class="bi bi-arrow-left"></i> Already have an account? Login
                </a>

                <button type="submit" id="submitBtn" class="btn-brand px-4 py-2.5" disabled>
                    <i class="bi bi-check2-circle"></i> Create Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    /* ===== AMBIENT GOLD NETWORK CANVAS ===== */
    const canvas = document.getElementById('goldNetworkCanvas');
    const ctx = canvas.getContext('2d');
    let points = [];

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    for (let i = 0; i < 75; i++) {
        points.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            vx: (Math.random() - .5) * 0.5,
            vy: (Math.random() - .5) * 0.5,
            radius: Math.random() * 2 + 1
        });
    }

    function drawNetwork() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        points.forEach(p => {
            p.x += p.vx;
            p.y += p.vy;
            if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
            if (p.y < 0 || p.y > canvas.height) p.vy *= -1;

            ctx.fillStyle = 'rgba(255, 191, 0, 0.45)';
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
            ctx.fill();
        });

        for (let i = 0; i < points.length; i++) {
            for (let j = i + 1; j < points.length; j++) {
                const dx = points[i].x - points[j].x;
                const dy = points[i].y - points[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < 130) {
                    ctx.strokeStyle = 'rgba(255, 191, 0, ' + (0.15 * (1 - dist / 130)) + ')';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(points[i].x, points[i].y);
                    ctx.lineTo(points[j].x, points[j].y);
                    ctx.stroke();
                }
            }
        }
        requestAnimationFrame(drawNetwork);
    }
    drawNetwork();

    /* ===== FORM VALIDATION LOGIC (UNCHANGED) ===== */
    function validateForm() {
        let valid = true;

        $('.required-field').each(function () {
            if ($(this).is(':checkbox')) {
                if (!this.checked) valid = false;
            } else if (!this.value.trim()) {
                valid = false;
            }
        });

        if ($('#password').val() !== $('#confirmPassword').val() || !$('#password').val().trim()) {
            valid = false;
        }

        $('#submitBtn').prop('disabled', !valid);
    }

    $('.required-field').on('input change', validateForm);

    $('.toggle-password').on('click', function () {
        const target = $($(this).data('target'));
        const isPassword = target.attr('type') === 'password';
        target.attr('type', isPassword ? 'text' : 'password');
        $(this).toggleClass('bi-eye bi-eye-slash');
    });

    $('#password').on('input', function () {
        let v = this.value, s = 0;
        if (v.length >= 8) s++;
        if (/[A-Z]/.test(v)) s++;
        if (/[a-z]/.test(v)) s++;
        if (/[0-9]/.test(v)) s++;
        if (/[\W]/.test(v)) s++;

        const bar = $('#passwordStrengthBar');
        const hint = $('#passwordHint');
        bar.removeClass('pw-weak pw-medium pw-strong');

        if (s <= 2) {
            bar.addClass('pw-weak').css('width', '30%');
            hint.text('Weak password');
        } else if (s <= 4) {
            bar.addClass('pw-medium').css('width', '65%');
            hint.text('Medium strength');
        } else {
            bar.addClass('pw-strong').css('width', '100%');
            hint.text('Strong password (uppercase, lowercase, number, symbol)');
        }
    });

    $('#confirmPassword').on('input', function () {
        const match = $('#password').val() === this.value;
        $('#matchStatus')
            .text(match ? '✓ Passwords match' : '✗ Passwords do not match')
            .attr('class', match ? 'match-ok' : 'match-bad');
        validateForm();
    });

    $(document).ready(validateForm);

    $('#myForm').on('submit', function () {
        $('#submitBtn').prop('disabled', true).html('<span>Creating account...</span>');
    });
</script>

</body>
</html>
