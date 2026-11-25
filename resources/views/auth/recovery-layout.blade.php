<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | GeMEMIS</title>
    <link rel="icon" href="{{ asset('images/coat-of-arms.svg') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-public-entry.css') }}">
</head>
<body class="geita-login">
    <main class="geita-login-panel" aria-labelledby="recovery-title">
        <header class="geita-login-header">
            <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Coat of arms of Tanzania" width="56" height="62">
            <p>Geita Mineral Exhibition Management Information System</p>
            <h1 id="recovery-title">@yield('title')</h1>
        </header>

        @if (session('fail'))
            <p class="geita-login-error" role="alert">{{ session('fail') }}</p>
        @endif
        @if (session('status'))
            <p class="geita-login-success" role="status">{{ session('status') }}</p>
        @endif

        @yield('form')

        <footer class="geita-login-footer">
            @yield('footer')
            <a class="geita-link" href="{{ route('login') }}">Back to sign in</a>
        </footer>
    </main>
</body>
</html>
