<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Password Recovery | TanTrade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="{{ asset('images/nembo.png') }}">

    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet">

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

        .recovery-wrapper {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .recovery-card {
            background: rgba(255,255,255,0.96);
            max-width: 420px;
            width: 100%;
            border-radius: 16px;
            padding: 40px 35px;
            box-shadow: 0 30px 60px rgba(0,0,0,.35);
        }

        .recovery-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .recovery-header .icon {
            font-size: 48px;
            color: #1e90a8;
            margin-bottom: 10px;
        }

        .form-floating {
            position: relative;
        }

        .form-floating input {
            height: 50px;
            padding: 16px 12px 6px;
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

        .btn-primary:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .recovery-actions {
            margin-top: 20px;
        }
    </style>
</head>

<body class="login-container">

<canvas id="networkCanvas"></canvas>

<div class="recovery-wrapper">
<form action="{{ route('password.email') }}" method="POST" id="recoveryForm" class="recovery-card">
@csrf

<div class="recovery-header">
    <div class="icon">
        <a href="{{ route('index') }}"><i class="icon-lock2"></i></a>
    </div>
    <h4>Password Recovery</h4>
    <p class="text-muted">
        Enter the <strong>email address</strong> or <strong>phone number</strong> registered on your account. If a match is found, you will answer three verification questions.
    </p>
</div>

@if (Session::get('fail'))
    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
@endif

@if (Session::get('status'))
    <div class="alert alert-info">{{ Session::get('status') }}</div>
@endif

<div class="form-group form-floating">
    <input type="text"
           name="identifier"
           id="identifierInput"
           class="form-control required-field"
           placeholder=" "
           value="{{ old('identifier') }}"
           required
           autocomplete="username">
    <label>Email address or phone number *</label>
    <span class="text-danger">@error('identifier') {{ $message }} @enderror</span>
</div>

<div class="recovery-actions">
    <button type="submit" id="resetBtn" class="btn btn-primary btn-block" disabled>
        Continue <i class="icon-arrow-right14"></i>
    </button>

    <a href="{{ route('login') }}" class="btn btn-default btn-block" style="margin-top:10px;">
        Back to Login <i class="icon-unlocked position-right"></i>
    </a>
</div>

</form>
</div>

<script>
const canvas = document.getElementById('networkCanvas');
const ctx = canvas.getContext('2d');
let nodes = [];

function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}
window.addEventListener('resize', resizeCanvas);
resizeCanvas();

for (let i = 0; i < 60; i++) {
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
    $('#resetBtn').prop('disabled', true).text('Please wait...');
});
</script>

</body>
</html>
