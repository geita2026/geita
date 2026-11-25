@extends('auth.recovery-layout')

@section('title', 'Verify your identity')

@section('form')
    @error('verification')
        <p class="geita-login-error" role="alert">{{ $message }}</p>
    @enderror

    <form action="{{ route('password.challenge.verify') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        @foreach ($challenges as $challenge)
            @php
                $isMath = str_starts_with($challenge['key'], 'math');
                $fieldName = 'answer_'.$challenge['key'];
                $descriptions = [];
                if (!empty($challenge['hint'])) {
                    $descriptions[] = $fieldName.'-hint';
                }
                if ($errors->has($fieldName)) {
                    $descriptions[] = $fieldName.'-error';
                }
            @endphp
            <div class="geita-login-field">
                <label for="{{ $fieldName }}">{{ $challenge['question'] }}</label>
                <input type="{{ $challenge['key'] === 'email' ? 'email' : 'text' }}"
                    name="{{ $fieldName }}" id="{{ $fieldName }}" value="{{ old($fieldName) }}"
                    required autocomplete="off"
                    @if ($isMath) inputmode="numeric" pattern="-?[0-9]+" @endif
                    @if ($challenge['key'] === 'phone') inputmode="tel" @endif
                    @if ($descriptions) aria-describedby="{{ implode(' ', $descriptions) }}" @endif
                    @error($fieldName) aria-invalid="true" @enderror>
                @if (!empty($challenge['hint']))
                    <p id="{{ $fieldName }}-hint" class="geita-registration-hint">{{ $challenge['hint'] }}</p>
                @endif
                @error($fieldName)
                    <p id="{{ $fieldName }}-error" class="geita-field-error">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <button class="geita-primary geita-login-submit" type="submit">Verify answers</button>
    </form>

    @if (($regenerationsRemaining ?? 0) > 0)
        <form class="geita-recovery-alternative" action="{{ route('password.challenge.regenerate', ['token' => $token]) }}"
            method="POST" onsubmit="return window.confirm('Replace the current questions and clear your answers?')">
            @csrf
            <button class="geita-link geita-recovery-link-button" type="submit">Get different questions</button>
            <span>({{ $regenerationsRemaining }} remaining)</span>
        </form>
    @endif
@endsection

@section('footer')
    <p><a class="geita-link" href="{{ route('password.request') }}">Start over</a></p>
@endsection
