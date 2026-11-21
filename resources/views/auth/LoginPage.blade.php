<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login | GeMEMIS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="{{ asset('images/nembo.png') }}">

    <!-- Core CSS -->
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet">

    <!-- Core JS -->
    <script src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>

    <style>
        body.login-container {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            background: radial-gradient(circle at top, #0a3d62, #043d3a, #021f1f);
            overflow: hidden;
        }

        canvas#networkCanvas {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .login-wrapper {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: rgba(255,255,255,0.96);
            max-width: 420px;
            width: 100%;
            border-radius: 16px;
            padding: 40px 35px;
            box-shadow: 0 30px 60px rgba(0,0,0,.35);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        /* Floating labels */
        .form-floating {
            position: relative;
        }

        .form-floating input {
            height: 50px;
            padding: 16px 40px 6px 12px;
            border-radius: 8px;
        }

        .form-floating label {
            position: absolute;
            top: 50%;
            left: 12px;
            font-size: 14px;
            color: #777;
            transform: translateY(-50%);
            pointer-events: none;
            transition: .2s ease;
            background: #fff;
            padding: 0 4px;
        }

        .form-floating input:focus + label,
        .form-floating input:not(:placeholder-shown) + label {
            top: 6px;
            font-size: 12px;
            color: #1e90a8;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
        }

        .btn-primary:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .login-actions {
            margin-top: 20px;
        }

        .login-links {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
    </style>
</head>

<body class="login-container">

<canvas id="networkCanvas"></canvas>

<div class="login-wrapper">
<form action="{{ route('login') }}" method="POST" id="loginForm" class="login-card">
@csrf

<div class="login-header">
    <img src="{{ asset('images/logo.jpg') }}">
    <h4>Login to your account</h4>
    <p class="text-muted">Enter your credentials</p>
</div>

@if (Session::get('fail'))
    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
@endif

@if (Session::get('success'))
    <div class="alert alert-info">{{ Session::get('success') }}</div>
@endif

<div class="form-group form-floating">
    <input type="text" name="user_name"
           class="form-control required-field"
           placeholder=" "
           value="{{ old('user_name') }}"
           required>
    <label>Email or phone number *</label>
    <span class="text-danger">@error('user_name') {{ $message }} @enderror</span>
</div>

<div class="form-group form-floating">
    <input type="password" name="password" id="loginPassword"
           class="form-control required-field"
           placeholder=" "
           required>
    <label>Password *</label>
    <i class="icon-eye toggle-password" data-target="#loginPassword"></i>
    <span class="text-danger">@error('password') {{ $message }} @enderror</span>
</div>

<div class="login-links">
    <label class="checkbox-inline">
        <input type="checkbox" class="styled"> Remember
    </label>
    <a href="{{ route('password.request') }}">Forgot password?</a>
</div>

<div class="login-actions">
    <button type="submit" id="loginBtn" class="btn btn-primary btn-block" disabled>
        Login <i class="icon-arrow-right14"></i>
    </button>
</div>

<div class="content-divider text-muted text-center" style="margin:20px 0;">
    <span>Don't have an account?</span>
</div>

<a href="{{ route('registration') }}" class="btn btn-default btn-block">
    Sign up
</a>

<p class="help-block text-center" style="margin-top:10px;">
    By continuing, you agree to our
    <a href="{{ route('terms_and_condition.index') }}">Terms &amp; Conditions</a>
</p>

</form>
</div>

<script>
/* ===== NETWORK BACKGROUND ===== */
const canvas = document.getElementById('networkCanvas');
const ctx = canvas.getContext('2d');
let nodes = [];

function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}
window.addEventListener('resize', resizeCanvas);
resizeCanvas();

for (let i = 0; i < 70; i++) {
    nodes.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        vx: (Math.random() - .5) * 0.6,
        vy: (Math.random() - .5) * 0.6
    });
}

function animateNetwork() {
    ctx.clearRect(0,0,canvas.width,canvas.height);

    nodes.forEach(n => {
        n.x += n.vx;
        n.y += n.vy;
        if (n.x < 0 || n.x > canvas.width) n.vx *= -1;
        if (n.y < 0 || n.y > canvas.height) n.vy *= -1;
    });

    for (let i = 0; i < nodes.length; i++) {
        for (let j = i + 1; j < nodes.length; j++) {
            const dx = nodes[i].x - nodes[j].x;
            const dy = nodes[i].y - nodes[j].y;
            const dist = Math.sqrt(dx*dx + dy*dy);
            if (dist < 120) {
                ctx.strokeStyle = 'rgba(0, 200, 180, 0.15)';
                ctx.beginPath();
                ctx.moveTo(nodes[i].x, nodes[i].y);
                ctx.lineTo(nodes[j].x, nodes[j].y);
                ctx.stroke();
            }
        }
    }
    requestAnimationFrame(animateNetwork);
}
animateNetwork();

/* ===== FORM VALIDATION (FRONTEND ONLY) ===== */
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
    target.attr('type', target.attr('type') === 'password' ? 'text' : 'password');
});

$(document).ready(validateLogin);

$('#loginForm').on('submit', function () {
    $('#loginBtn').prop('disabled', true);
});
</script>

</body>
</html>
