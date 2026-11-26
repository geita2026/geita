<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create an account | GeMEMIS</title>
    <link rel="icon" href="{{ asset('images/coat-of-arms.svg') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-public-entry.css') }}">
</head>
<body class="geita-login">
    <main class="geita-login-panel geita-registration-panel" aria-labelledby="registration-title">
        <header class="geita-login-header">
            <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Coat of arms of Tanzania" width="56" height="62">
            <p>Geita Mineral Exhibition Management Information System</p>
            <h1 id="registration-title">Create an account</h1>
        </header>

        @if ($failure = session('fail', $fail ?? null))
            <p class="geita-login-error" role="alert">{{ $failure }}</p>
        @endif
        @if (session('success'))
            <p class="geita-login-success" role="status">{{ session('success') }}</p>
        @endif

        <form action="{{ route('register') }}" method="POST" id="myForm">
            @csrf

            @php
                $contactFields = [
                    ['firstName', 'firstNameInput', 'First name', 'text', 'given-name', 100],
                    ['lastName', 'lastNameInput', 'Last name', 'text', 'family-name', 100],
                    ['phoneNumber', 'phoneInput', 'Phone number', 'tel', 'tel', 20],
                    ['userEmail', 'emailInput', 'Email address', 'email', 'email', 100],
                ];
                $passwordFields = [
                    ['password', 'password', 'Password'],
                    ['password_confirmation', 'confirmPassword', 'Confirm password'],
                ];
            @endphp

            <div class="geita-registration-fields">
                @foreach ($contactFields as [$name, $id, $label, $type, $autocomplete, $max])
                    <div class="geita-login-field">
                        <label for="{{ $id }}">{{ $label }}</label>
                        <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" value="{{ old($name) }}"
                            required autocomplete="{{ $autocomplete }}" maxlength="{{ $max }}"
                            @error($name) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror>
                        @error($name)
                            <p id="{{ $id }}-error" class="geita-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                @foreach ($passwordFields as [$name, $id, $label])
                    <div class="geita-login-field">
                        <label for="{{ $id }}">{{ $label }}</label>
                        <div class="geita-password-field">
                            <input type="password" name="{{ $name }}" id="{{ $id }}" required autocomplete="new-password"
                                @if ($name === 'password') minlength="8" @endif
                                aria-describedby="{{ $name === 'password' ? 'registration-password-hint' : 'registration-match-error' }}{{ $errors->has($name) ? ' '.$id.'-error' : '' }}"
                                @error($name) aria-invalid="true" @enderror>
                            @include('auth.password-visibility', ['inputId' => $id])
                        </div>
                        @if ($name === 'password')
                            <p id="registration-password-hint" class="geita-registration-hint">
                                Use at least 8 characters, including uppercase and lowercase letters, a number, and a symbol.
                            </p>
                        @else
                            <p id="registration-match-error" class="geita-field-error" aria-live="polite" hidden></p>
                        @endif
                        @error($name)
                            <p id="{{ $id }}-error" class="geita-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="geita-login-options">
                <label for="termsCheck">
                    <input type="checkbox" id="termsCheck" required>
                    I agree to the <a class="geita-link" href="{{ route('terms_and_condition.index') }}">Terms &amp; Conditions</a>.
                </label>
            </div>

            <button class="geita-primary geita-login-submit" type="submit" id="submitBtn">Create account</button>
        </form>

        <footer class="geita-login-footer">
            <p>Already have an account? <a class="geita-link" href="{{ route('login') }}">Sign in</a></p>
            <a class="geita-link" href="{{ route('index') }}">Back to homepage</a>
        </footer>
    </main>

    <script>
        const form = document.getElementById('myForm');
        const password = document.getElementById('password');
        const confirmation = document.getElementById('confirmPassword');
        const matchError = document.getElementById('registration-match-error');
        const submitButton = document.getElementById('submitBtn');

        function checkPasswordMatch() {
            const mismatch = confirmation.value !== '' && password.value !== confirmation.value;
            const message = mismatch ? 'Passwords do not match.' : '';
            confirmation.setCustomValidity(message);
            matchError.hidden = !mismatch;
            matchError.textContent = message;
        }

        password.addEventListener('input', checkPasswordMatch);
        confirmation.addEventListener('input', checkPasswordMatch);

        form.addEventListener('submit', function (event) {
            checkPasswordMatch();
            if (!form.reportValidity()) {
                event.preventDefault();
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Creating account…';
        });

        window.addEventListener('pageshow', function () {
            submitButton.disabled = false;
            submitButton.textContent = 'Create account';
        });
    </script>
</body>
</html>
