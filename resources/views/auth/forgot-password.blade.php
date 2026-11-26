@extends('auth.recovery-layout')

@section('title', 'Forgot password')

@section('form')
    <p class="geita-recovery-intro">Enter your registered email address or phone number to verify your account.</p>

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="geita-login-field">
            <label for="identifierInput">Email or phone number</label>
            <input type="text" name="identifier" id="identifierInput" value="{{ old('identifier') }}"
                required autocomplete="username"
                @error('identifier') aria-invalid="true" aria-describedby="identifier-error" @enderror>
            @error('identifier')
                <p id="identifier-error" class="geita-field-error">{{ $message }}</p>
            @enderror
        </div>

        <button class="geita-primary geita-login-submit" type="submit">Continue</button>
    </form>
@endsection

@section('footer')
    <p><a class="geita-link" href="{{ route('index') }}">Back to homepage</a></p>
@endsection
