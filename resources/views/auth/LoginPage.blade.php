<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | GeMEMIS</title>
    <link rel="icon" href="{{ asset('images/coat-of-arms.svg') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-public-entry.css') }}">
</head>
<body class="geita-login">
    <main class="geita-login-panel" aria-labelledby="sign-in-title">
        <header class="geita-login-header">
            <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Coat of arms of Tanzania" width="56" height="62">
            <p>Geita Mineral Exhibition Management Information System</p>
            <h1 id="sign-in-title">Sign in</h1>
        </header>

        @if (session('fail'))
            <p class="geita-login-error" role="alert">{{ session('fail') }}</p>
        @endif
        @if (session('success'))
            <p class="geita-login-success" role="status">{{ session('success') }}</p>
        @endif

        <form action="{{ route('login') }}" method="POST" id="loginForm">
            @csrf

            <div class="geita-login-field">
                <label for="userNameInput">Email or phone number</label>
                <input type="text" name="user_name" id="userNameInput" value="{{ old('user_name') }}"
                    required autocomplete="username"
                    @error('user_name') aria-invalid="true" aria-describedby="login-username-error" @enderror>
                @error('user_name')
                    <p id="login-username-error" class="geita-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="geita-login-field">
                <label for="loginPassword">Password</label>
                <div class="geita-password-field">
                    <input type="password" name="password" id="loginPassword" required autocomplete="current-password"
                        @error('password') aria-invalid="true" aria-describedby="login-password-error" @enderror>
                    @include('auth.password-visibility', ['inputId' => 'loginPassword'])
                </div>
                @error('password')
                    <p id="login-password-error" class="geita-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="geita-login-options">
                <label for="rememberCheck"><input type="checkbox" name="remember" id="rememberCheck"> Remember me</label>
                <a class="geita-link" href="{{ route('password.request') }}">Forgot password?</a>
            </div>

            <button class="geita-primary geita-login-submit" type="submit" id="loginBtn">Sign in</button>
        </form>

        <footer class="geita-login-footer">
            <p>New to GeMEMIS? <a class="geita-link" href="{{ route('registration') }}">Create an account</a></p>
            <p>By continuing, you agree to the <a class="geita-link" href="{{ route('terms_and_condition.index') }}">Terms &amp; Conditions</a>.</p>
            <a class="geita-link" href="{{ route('index') }}">Back to homepage</a>
        </footer>
    </main>

    <script>
        const loginForm = document.getElementById('loginForm');
        const submitButton = document.getElementById('loginBtn');

        loginForm.addEventListener('submit', function () {
            submitButton.disabled = true;
            submitButton.textContent = 'Signing in…';
        });

        window.addEventListener('pageshow', function () {
            submitButton.disabled = false;
            submitButton.textContent = 'Sign in';
        });
    </script>
</body>
</html>
