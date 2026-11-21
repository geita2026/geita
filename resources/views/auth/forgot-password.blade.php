<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Password recovery | GeMEMIS</title>
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

    <!-- Core jQuery -->
    <script src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>

    <style>
        body.recovery-page-body {
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

        .recovery-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .recovery-card-box {
            max-width: 460px;
            width: 100%;
            padding: 42px 36px;
        }

        .recovery-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .recovery-icon-circle {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: rgba(255, 191, 0, 0.12);
            border: 1.5px solid rgba(255, 191, 0, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #FFBF00;
            margin-bottom: 16px;
            box-shadow: 0 0 24px rgba(255, 191, 0, 0.25);
        }

        .recovery-header h3 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .recovery-header p {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
            margin: 0;
        }

        .gold-floating-field {
            position: relative;
            margin-bottom: 24px;
        }

        .gold-floating-field input.form-control {
            height: 54px;
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1.5px solid rgba(255, 191, 0, 0.25) !important;
            border-radius: 14px !important;
            color: #ffffff !important;
            padding: 20px 16px 6px 16px !important;
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

        .recovery-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
    </style>
</head>

<body class="recovery-page-body">

<canvas id="goldNetworkCanvas"></canvas>

<div class="recovery-wrapper">
    <div class="brand-card recovery-card-box">
        <form action="{{ route('password.email') }}" method="POST" id="recoveryForm">
            @csrf

            <!-- Header -->
            <div class="recovery-header">
                <div class="recovery-icon-circle">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h3>Password Recovery</h3>
                <p>
                    Enter your registered <strong>email address</strong> or <strong>phone number</strong>. We will guide you through identity verification.
                </p>
            </div>

            <!-- Feedback -->
            @if (Session::get('fail'))
                <div class="alert alert-brand-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ Session::get('fail') }}
                </div>
            @endif

            @if (Session::get('status'))
                <div class="alert alert-brand-info mb-3">
                    <i class="bi bi-info-circle-fill me-2"></i> {{ Session::get('status') }}
                </div>
            @endif

            <!-- Identifier Input -->
            <div class="gold-floating-field">
                <input type="text"
                       name="identifier"
                       id="identifierInput"
                       class="form-control required-field"
                       placeholder=" "
                       value="{{ old('identifier') }}"
                       required
                       autocomplete="username">
                <label for="identifierInput">Email Address or Phone Number *</label>
                @error('identifier')
                    <span class="text-danger" style="font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="recovery-actions">
                <button type="submit" id="resetBtn" class="btn-brand w-100 py-3" disabled>
                    <span>Continue</span>
                    <i class="bi bi-arrow-right-short fs-5"></i>
                </button>

                <a href="{{ route('login') }}" class="btn-brand-outline w-100 py-2.5">
                    <i class="bi bi-arrow-left me-1"></i> Back to Login
                </a>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('index') }}" class="text-white-50 text-decoration-none small">
                    <i class="bi bi-house-door me-1"></i> Return to Homepage
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

    for (let i = 0; i < 55; i++) {
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

            ctx.fillStyle = 'rgba(255, 191, 0, 0.45)';
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
                    ctx.strokeStyle = 'rgba(255, 191, 0, ' + (0.15 * (1 - dist / 130)) + ')';
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
    function validateRecovery() {
        let valid = true;
        $('.required-field').each(function () {
            if (!this.value.trim()) valid = false;
        });
        $('#resetBtn').prop('disabled', !valid);
    }

    $('.required-field').on('input', validateRecovery);
    $(document).ready(validateRecovery);

    $('#recoveryForm').on('submit', function () {
        $('#resetBtn').prop('disabled', true).html('<span>Checking identity...</span>');
    });
</script>

</body>
</html>
