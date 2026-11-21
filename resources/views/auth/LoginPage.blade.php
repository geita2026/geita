<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sign in | GeMEMIS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="{{ asset('images/coat-of-arms.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Noto+Sans+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet">

    <!-- Golden Design System -->
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-theme.css') }}">

    <!-- Core jQuery -->
    <script src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>

    <style>
        body.login-page-body {
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

        .login-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .auth-card-box {
            max-width: 440px;
            width: 100%;
            padding: 42px 36px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-crest-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 16px;
        }

        .brand-crest-group img.logo-crest {
            height: 78px;
            width: auto;
            filter: drop-shadow(0 6px 18px rgba(255, 191, 0, 0.30));
        }

        .login-header h3 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .login-header p {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.65);
            margin: 0;
        }

        /* Floating Input Container */
        .gold-floating-field {
            position: relative;
            margin-bottom: 20px;
        }

        .gold-floating-field input.form-control {
            height: 54px;
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1.5px solid rgba(255, 191, 0, 0.25) !important;
            border-radius: 14px !important;
            color: #ffffff !important;
            padding: 20px 42px 6px 16px !important;
            font-size: 14.5px !important;
            transition: all 0.2s ease;
        }

        .gold-floating-field input.form-control:focus {
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: #FFBF00 !important;
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

        .auth-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 24px;
        }

        .auth-options a {
            color: #FFBF00;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .auth-options a:hover {
            color: #FFE9A8;
            text-decoration: underline;
        }

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

        .auth-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 26px 0 20px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 12.5px;
            letter-spacing: 0.08em;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 191, 0, 0.2);
        }

        .auth-divider span {
            padding: 0 12px;
        }

        .back-home-link {
            text-align: center;
            margin-top: 22px;
            font-size: 13.5px;
        }

        .back-home-link a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease;
        }

        .back-home-link a:hover {
            color: #FFBF00;
        }
    </style>
</head>

<body class="login-page-body">

<!-- Ambient Gold Particle Network Canvas -->
<canvas id="goldNetworkCanvas"></canvas>

<div class="login-wrapper">
    <div class="brand-card auth-card-box">
        <form action="{{ route('login') }}" method="POST" id="loginForm">
            @csrf

            <!-- Header with Logos -->
            <div class="login-header">
                <div class="brand-crest-group">
                    <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Court of Arms of the United Republic of Tanzania" class="logo-crest">
                </div>
                <h3>Welcome Back</h3>
                <p>Geita Mineral Exhibition Management Information System</p>
            </div>

            <!-- Session Feedback Messages -->
            @if (Session::get('fail'))
                <div class="alert alert-brand-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ Session::get('fail') }}
                </div>
            @endif

            @if (Session::get('success'))
                <div class="alert alert-brand-success mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ Session::get('success') }}
                </div>
            @endif

            <!-- Username / Email Field -->
            <div class="gold-floating-field">
                <input type="text"
                       name="user_name"
                       id="userNameInput"
                       class="form-control required-field"
                       placeholder=" "
                       value="{{ old('user_name') }}"
                       required
                       autocomplete="username">
                <label for="userNameInput">Email or Phone Number *</label>
                @error('user_name')
                    <span class="text-danger" style="font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="gold-floating-field">
                <input type="password"
                       name="password"
                       id="loginPassword"
                       class="form-control required-field"
                       placeholder=" "
                       required
                       autocomplete="current-password">
                <label for="loginPassword">Password *</label>
                <i class="bi bi-eye toggle-password" data-target="#loginPassword"></i>
                @error('password')
                    <span class="text-danger" style="font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember & Forgot Password -->
            <div class="auth-options">
                <div class="form-check m-0">
                    <input type="checkbox" name="remember" class="form-check-input" id="rememberCheck">
                    <label class="form-check-label text-white-50" for="rememberCheck">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}">Forgot password?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="loginBtn" class="btn-brand w-100 py-3" disabled>
                <span>Sign in</span>
                <i class="bi bi-arrow-right-short fs-5"></i>
            </button>

            <!-- Signup Divider -->
            <div class="auth-divider">
                <span>New to GeMEMIS?</span>
            </div>

            <!-- Register CTA -->
            <a href="{{ route('registration') }}" class="btn-brand-outline w-100 py-2.5">
                <i class="bi bi-person-plus me-1"></i> Create New Account
            </a>

            <!-- Terms & Conditions Link -->
            <p class="text-center text-white-50 mt-3 mb-0" style="font-size:12px;">
                By continuing, you agree to our
                <a href="{{ route('terms_and_condition.index') }}" class="text-warning text-decoration-none">Terms &amp; Conditions</a>
            </p>

            <!-- Return to Home -->
            <div class="back-home-link">
                <a href="{{ route('index') }}">
                    <i class="bi bi-house-door"></i> Back to Homepage
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    /* ===== AMBIENT GOLD NETWORK CANVAS ===== */
    const canvas = document.getElementById('goldNetworkCanvas');
    const ctx = canvas.getContext('2d');
    let nodes = [];

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    for (let i = 0; i < 65; i++) {
        nodes.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            vx: (Math.random() - .5) * 0.5,
            vy: (Math.random() - .5) * 0.5,
            radius: Math.random() * 2 + 1
        });
    }

    function animateGoldNetwork() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        nodes.forEach(n => {
            n.x += n.vx;
            n.y += n.vy;
            if (n.x < 0 || n.x > canvas.width) n.vx *= -1;
            if (n.y < 0 || n.y > canvas.height) n.vy *= -1;

            ctx.fillStyle = 'rgba(255, 191, 0, 0.5)';
            ctx.beginPath();
            ctx.arc(n.x, n.y, n.radius, 0, Math.PI * 2);
            ctx.fill();
        });

        for (let i = 0; i < nodes.length; i++) {
            for (let j = i + 1; j < nodes.length; j++) {
                const dx = nodes[i].x - nodes[j].x;
                const dy = nodes[i].y - nodes[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < 130) {
                    ctx.strokeStyle = 'rgba(255, 191, 0, ' + (0.16 * (1 - dist / 130)) + ')';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(nodes[i].x, nodes[i].y);
                    ctx.lineTo(nodes[j].x, nodes[j].y);
                    ctx.stroke();
                }
            }
        }
        requestAnimationFrame(animateGoldNetwork);
    }
    animateGoldNetwork();

    /* ===== FORM VALIDATION LOGIC (UNCHANGED) ===== */
    function validateLogin() {
        let valid = true;
        $('.required-field').each(function () {
            if (!this.value.trim()) valid = false;
        });
        $('#loginBtn').prop('disabled', !valid);
    }

    $('.required-field').on('input', validateLogin);

    $('.toggle-password').on('click', function () {
        const target = $($(this).data('target'));
        const isPassword = target.attr('type') === 'password';
        target.attr('type', isPassword ? 'text' : 'password');
        $(this).toggleClass('bi-eye bi-eye-slash');
    });

    $(document).ready(validateLogin);

    $('#loginForm').on('submit', function () {
        $('#loginBtn').prop('disabled', true).html('<span>Authenticating...</span>');
    });
</script>

</body>
</html>
