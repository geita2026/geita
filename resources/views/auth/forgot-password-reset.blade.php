<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Set New Password | GeMEMIS</title>
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
        }
        .recovery-wrapper {
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
    </style>
</head>

<body class="login-container">

<div class="recovery-wrapper">
<form action="{{ route('password.reset.submit') }}" method="POST" id="resetForm" class="recovery-card">
@csrf
<input type="hidden" name="token" value="{{ $token }}">

<h4 class="text-center">Set New Password</h4>
<p class="text-muted text-center">Use at least 8 characters with uppercase, lowercase, a number, and a special character.</p>

@if (Session::get('status'))
    <div class="alert alert-info">{{ Session::get('status') }}</div>
@endif

<div class="form-group">
    <label>New password *</label>
    <input type="password" name="password" class="form-control" required autocomplete="new-password">
    <span class="text-danger">@error('password') {{ $message }} @enderror</span>
</div>

<div class="form-group">
    <label>Confirm new password *</label>
    <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
</div>

<button type="submit" id="submitBtn" class="btn btn-primary btn-block">Reset Password</button>
<a href="{{ route('login') }}" class="btn btn-default btn-block" style="margin-top:10px;">Back to Login</a>

</form>
</div>

<script>
$('#resetForm').on('submit', function () {
    $('#submitBtn').prop('disabled', true).text('Saving...');
});
</script>

</body>
</html>
