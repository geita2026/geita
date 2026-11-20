<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Create Account | TanTrade</title>
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

        .register-wrapper {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            background: rgba(255,255,255,0.96);
            max-width: 920px;
            width: 100%;
            border-radius: 16px;
            padding: 45px 50px;
            box-shadow: 0 30px 60px rgba(0,0,0,.35);
        }

        .register-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .register-header img {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .section-title {
            margin: 30px 0 15px;
            font-weight: 600;
            color: #555;
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
        }

        .form-floating {
            position: relative;
        }

        .form-floating input {
            height: 52px;
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

        .password-meter {
            height: 6px;
            background: #e0e0e0;
            border-radius: 4px;
            margin-top: 6px;
            overflow: hidden;
        }

        .password-meter div {
            height: 100%;
            width: 0%;
            transition: width .3s ease;
        }

        .pw-weak { background: #dc3545; }
        .pw-medium { background: #ffc107; }
        .pw-strong { background: #28a745; }

        .password-hint,
        .match-ok,
        .match-bad {
            font-size: 12px;
        }

        .match-ok { color: #28a745; }
        .match-bad { color: #dc3545; }

        .btn-primary:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
        }

        @media (max-width: 768px) {
            .register-card {
                padding: 30px 25px;
            }
        }
    </style>
</head>

<body class="login-container">

<canvas id="networkCanvas"></canvas>

<div class="register-wrapper">
<form action="{{ route('register') }}" method="POST" id="myForm" class="register-card">
@csrf

<div class="register-header">
    <img src="{{ asset('images/logo.jpg') }}">
    <h4>Create New Account</h4>
</div>

@if (Session::get('fail'))
    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
@endif

@if (Session::get('success'))
    <div class="alert alert-success">Account created successfully. Check your email.</div>
@endif

<div class="section-title">Personal Information</div>

<div class="row">
    <div class="col-md-6 form-group form-floating">
        <input type="text" name="firstName" class="form-control required-field"
               placeholder=" " value="{{ old('firstName') }}" required>
        <label>First Name *</label>
        <span class="text-danger">@error('firstName') {{ $message }} @enderror</span>
    </div>

    <div class="col-md-6 form-group form-floating">
        <input type="text" name="lastName" class="form-control required-field"
               placeholder=" " value="{{ old('lastName') }}" required>
        <label>Last Name *</label>
        <span class="text-danger">@error('lastName') {{ $message }} @enderror</span>
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group form-floating">
        <input type="text" name="phoneNumber" class="form-control required-field"
               placeholder=" " value="{{ old('phoneNumber') }}" required>
        <label>Phone Number *</label>
        <span class="text-danger">@error('phoneNumber') {{ $message }} @enderror</span>
    </div>

    <div class="col-md-6 form-group form-floating">
        <input type="email" name="userEmail" class="form-control required-field"
               placeholder=" " value="{{ old('userEmail') }}" required>
        <label>Email *</label>
        <span class="text-danger">@error('userEmail') {{ $message }} @enderror</span>
    </div>
</div>

<div class="section-title">Security</div>

<div class="row">
    <div class="col-md-6 form-group form-floating">
        <input type="password" id="password" name="password"
               class="form-control required-field" placeholder=" " required>
        <label>Password *</label>
        <i class="icon-eye toggle-password" data-target="#password"></i>

        <div class="password-meter">
            <div id="passwordStrengthBar"></div>
        </div>
        <div id="passwordHint" class="password-hint text-muted"></div>

        <span class="text-danger">@error('password') {{ $message }} @enderror</span>
    </div>

    <div class="col-md-6 form-group form-floating">
        <input type="password" id="confirmPassword"
               name="password_confirmation"
               class="form-control required-field" placeholder=" " required>
        <label>Confirm Password *</label>
        <i class="icon-eye toggle-password" data-target="#confirmPassword"></i>

        <div id="matchStatus"></div>
        <span class="text-danger">@error('password_confirmation') {{ $message }} @enderror</span>
    </div>
</div>

<div class="checkbox">
    <label>
        <input type="checkbox" id="termsCheck" class="required-field" required>
        Accept <a href="{{ route('terms_and_condition.index') }}">Terms</a>
    </label>
</div>

<div class="actions">
    <a href="{{ route('login') }}" class="btn btn-link">Back to Login</a>
    <button type="submit" id="submitBtn" class="btn btn-primary" disabled>
        Create Account
    </button>
</div>

</form>
</div>

<script>
/* ========= NETWORK ANIMATION (BLUE-GREEN THEME) ========= */
const canvas = document.getElementById('networkCanvas');
const ctx = canvas.getContext('2d');
let points = [];

function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}
window.addEventListener('resize', resizeCanvas);
resizeCanvas();

for (let i = 0; i < 90; i++) {
    points.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        vx: (Math.random() - .5) * 0.6,
        vy: (Math.random() - .5) * 0.6
    });
}

function drawNetwork() {
    ctx.clearRect(0,0,canvas.width,canvas.height);

    points.forEach(p => {
        p.x += p.vx;
        p.y += p.vy;

        if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
        if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
    });

    for (let i = 0; i < points.length; i++) {
        for (let j = i + 1; j < points.length; j++) {
            const dx = points[i].x - points[j].x;
            const dy = points[i].y - points[j].y;
            const dist = Math.sqrt(dx*dx + dy*dy);

            if (dist < 120) {
                ctx.strokeStyle = 'rgba(0, 200, 180, 0.15)';
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

/* ========= FORM LOGIC (UNCHANGED) ========= */
function validateForm() {
    let valid = true;

    $('.required-field').each(function () {
        if ($(this).is(':checkbox')) {
            if (!this.checked) valid = false;
        } else if (!this.value.trim()) {
            valid = false;
        }
    });

    if ($('#password').val() !== $('#confirmPassword').val()) {
        valid = false;
    }

    $('#submitBtn').prop('disabled', !valid);
}

$('.required-field').on('input change', validateForm);

$('.toggle-password').on('click', function () {
    const target = $($(this).data('target'));
    target.attr('type', target.attr('type') === 'password' ? 'text' : 'password');
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
        bar.addClass('pw-weak').css('width','30%');
        hint.text('Weak password');
    } else if (s <= 4) {
        bar.addClass('pw-medium').css('width','65%');
        hint.text('Medium strength');
    } else {
        bar.addClass('pw-strong').css('width','100%');
        hint.text('Strong password (uppercase, lowercase, number, symbol)');
    }
});

$('#confirmPassword').on('input', function () {
    const match = $('#password').val() === this.value;
    $('#matchStatus')
        .text(match ? 'Passwords match' : 'Passwords do not match')
        .attr('class', match ? 'match-ok' : 'match-bad');
    validateForm();
});

$(document).ready(validateForm);

$('#myForm').on('submit', function () {
    $('#submitBtn').prop('disabled', true);
});
</script>

</body>
</html>
