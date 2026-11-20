<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reset Password | TanTrade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('images/nembo.png') }}">
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet">
</head>
<body style="padding:40px;">
<div class="container" style="max-width:420px;">
    <h4>Reset Password</h4>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $request->email) }}" required>
            @error('email')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label>New password</label>
            <input type="password" name="password" class="form-control" required>
            @error('password')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label>Confirm password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
        <a href="{{ route('login') }}" class="btn btn-link">Back to login</a>
    </form>
</div>
</body>
</html>
