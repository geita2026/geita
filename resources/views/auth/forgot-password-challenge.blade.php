<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Verify Identity | TanTrade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('images/nembo.png') }}">
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            max-width: 520px;
            width: 100%;
            border-radius: 16px;
            padding: 40px 35px;
            box-shadow: 0 30px 60px rgba(0,0,0,.35);
        }
        .challenge-box {
            background: #f8f9fa;
            border-left: 4px solid #1e90a8;
            padding: 15px;
            margin-bottom: 18px;
            border-radius: 4px;
        }
        .challenge-box.math {
            border-left-color: #28a745;
        }
        .hint-pill {
            display: inline-block;
            margin-top: 8px;
            padding: 6px 12px;
            background: #e8f4f8;
            border-radius: 20px;
            font-family: monospace;
            font-size: 14px;
            color: #0a3d62;
            letter-spacing: 0.5px;
        }
        .challenge-number {
            display: inline-block;
            width: 26px;
            height: 26px;
            line-height: 26px;
            text-align: center;
            background: #1e90a8;
            color: #fff;
            border-radius: 50%;
            font-size: 13px;
            margin-right: 8px;
        }
        .challenge-box.math .challenge-number {
            background: #28a745;
        }
        .regenerate-section {
            margin: 20px 0;
            padding-top: 15px;
            border-top: 1px dashed #ddd;
            text-align: center;
        }
    </style>
</head>

<body class="login-container">

<div class="recovery-wrapper">
<form action="{{ route('password.challenge.verify') }}" method="POST" id="challengeForm" class="recovery-card">
@csrf
<input type="hidden" name="token" value="{{ $token }}">

<h4 class="text-center">Identity Verification</h4>
<p class="text-muted text-center">
    Answer <strong>all {{ count($challenges) }} questions</strong> below correctly to reset your password.
</p>

@error('verification')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror

@foreach ($challenges as $index => $challenge)
    @php
        $isMath = str_starts_with($challenge['key'], 'math');
        $fieldName = 'answer_'.$challenge['key'];
    @endphp
    <div class="challenge-box {{ $isMath ? 'math' : '' }}">
        <div>
            <span class="challenge-number">{{ $index + 1 }}</span>
            <strong>{{ $isMath ? 'Human verification' : 'Account verification' }}</strong>
        </div>
        <p class="mb-0" style="margin-top:10px;">{{ $challenge['question'] }}</p>
        @if (!empty($challenge['hint']))
            <span class="hint-pill">{{ $challenge['hint'] }}</span>
        @endif
        <div class="form-group" style="margin-top:12px;margin-bottom:0;">
            <input type="{{ $isMath ? 'text' : ($challenge['key'] === 'email' ? 'email' : 'text') }}"
                   name="{{ $fieldName }}"
                   class="form-control"
                   value="{{ old($fieldName) }}"
                   required
                   autocomplete="off"
                   @if($isMath) inputmode="numeric" pattern="-?[0-9]+" placeholder="Enter number only" @endif
                   @if($challenge['key'] === 'phone') inputmode="tel" placeholder="Full phone number" @endif
                   @if($challenge['key'] === 'email') placeholder="Full email address" @endif>
            <span class="text-danger">@error($fieldName) {{ $message }} @enderror</span>
        </div>
    </div>
@endforeach

@if (($regenerationsRemaining ?? 0) > 0)
<div class="regenerate-section">
    <p class="text-muted" style="font-size:13px;margin-bottom:10px;">
        Cannot answer these questions? You can request a different set ({{ $regenerationsRemaining }} remaining).
    </p>
    <button type="button"
            id="regenerateBtn"
            class="btn btn-default btn-sm">
        <i class="icon-spinner11"></i> Get different questions
    </button>
</div>
@endif

<button type="submit" id="verifyBtn" class="btn btn-primary btn-block">Submit all answers</button>
<a href="{{ route('password.request') }}" class="btn btn-default btn-block" style="margin-top:10px;">Start Over</a>
<a href="{{ route('login') }}" class="btn btn-link btn-block">Back to Login</a>

</form>

@if (($regenerationsRemaining ?? 0) > 0)
<form id="regenerateForm" action="{{ route('password.challenge.regenerate', ['token' => $token]) }}" method="POST" style="display:none;">
    @csrf
</form>
@endif

</div>

<script>
$('#challengeForm').on('submit', function () {
    $('#verifyBtn').prop('disabled', true).text('Verifying...');
});

$('#regenerateBtn').on('click', function () {
    Swal.fire({
        icon: 'question',
        title: 'Generate new questions?',
        html: '<p style="font-size:15px;line-height:1.6;">Your current answers will be cleared. Three new verification questions will be loaded.</p>',
        showCancelButton: true,
        confirmButtonText: 'Yes, generate',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#1e90a8',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        focusCancel: true,
    }).then(function (result) {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Please wait...',
                text: 'Loading new questions',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: function () {
                    Swal.showLoading();
                },
            });
            document.getElementById('regenerateForm').submit();
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    @if (Session::get('status'))
        Swal.fire({
            icon: 'success',
            title: 'Questions updated',
            text: @json(Session::get('status')),
            confirmButtonColor: '#1e90a8',
        });
    @endif

    @if (Session::get('fail'))
        Swal.fire({
            icon: 'error',
            title: 'Unable to continue',
            text: @json(Session::get('fail')),
            confirmButtonColor: '#d32f2f',
        });
    @endif
});
</script>

</body>
</html>
