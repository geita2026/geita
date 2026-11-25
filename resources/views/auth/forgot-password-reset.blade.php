@extends('auth.recovery-layout')

@section('title', 'Set a new password')

@section('form')
    <form action="{{ route('password.reset.submit') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="geita-login-field">
            <label for="newPasswordInput">New password</label>
            <div class="geita-password-field">
                <input type="password" name="password" id="newPasswordInput" required minlength="8"
                    autocomplete="new-password" aria-describedby="recovery-password-hint{{ $errors->has('password') ? ' recovery-password-error' : '' }}"
                    @error('password') aria-invalid="true" @enderror>
                @include('auth.password-visibility', ['inputId' => 'newPasswordInput'])
            </div>
            <p id="recovery-password-hint" class="geita-registration-hint">
                Use at least 8 characters, including uppercase and lowercase letters, a number, and a symbol.
            </p>
            @error('password')
                <p id="recovery-password-error" class="geita-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="geita-login-field">
            <label for="confirmPasswordInput">Confirm new password</label>
            <div class="geita-password-field">
                <input type="password" name="password_confirmation" id="confirmPasswordInput"
                    required autocomplete="new-password">
                @include('auth.password-visibility', ['inputId' => 'confirmPasswordInput'])
            </div>
        </div>

        <button class="geita-primary geita-login-submit" type="submit">Update password</button>
    </form>
@endsection
