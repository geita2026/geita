<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reset your password | GeMEMIS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('images/coat-of-arms.svg') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Noto+Sans+Display:wght@600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-theme.css') }}">
    <style>
        body {
            background-color: #070506;
            background-image: radial-gradient(1200px 480px at 50% -8%, rgba(255, 191, 0, 0.25), transparent 60%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }
    </style>
</head>
<body>
<div class="brand-card p-4 p-md-5" style="max-width: 460px; width: 100%;">
    <div class="text-center mb-4">
        <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Court of Arms of the United Republic of Tanzania" height="76" style="margin-bottom:12px;filter:drop-shadow(0 6px 16px rgba(255, 191, 0,0.28));">
        <h3 style="font-weight:700;color:#ffffff;margin-bottom:6px;">Reset Password</h3>
        <p class="text-white-50" style="font-size:13.5px;">Enter your email and chosen password</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label class="form-label text-warning small fw-bold">Email Address *</label>
            <input type="email" name="email" class="form-control brand-field" value="{{ old('email', $request->email) }}" required>
            @error('email')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label text-warning small fw-bold">New Password *</label>
            <input type="password" name="password" class="form-control brand-field" required>
            @error('password')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label text-warning small fw-bold">Confirm Password *</label>
            <input type="password" name="password_confirmation" class="form-control brand-field" required>
        </div>

        <button type="submit" class="btn-brand w-100 py-3 mb-3">
            Reset Password <i class="bi bi-arrow-right-short fs-5"></i>
        </button>
        <a href="{{ route('login') }}" class="btn-brand-outline w-100 py-2.5">
            Back to Login
        </a>
    </form>
</div>
</body>
</html>
