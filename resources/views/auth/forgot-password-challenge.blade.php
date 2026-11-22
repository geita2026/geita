<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Verify your identity | GeMEMIS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="{{ asset('images/coat-of-arms.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Noto+Sans+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Golden Design System -->
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gememis-forms.css') }}">

    <!-- Core jQuery & SweetAlert2 -->
    <script src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
    @include('layouts.sweet-alerts')

    <style>
        body.challenge-page-body {
            margin: 0;
            min-height: 100vh;
            font-family: "Montserrat", sans-serif;
            background-color: #070506;
            background-image: radial-gradient(1200px 480px at 50% -8%, rgba(255, 191, 0, 0.25), transparent 60%);
            color: #ffffff;
            overflow-x: hidden;
            position: relative;
        }

        .challenge-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 45px 20px;
        }

        .challenge-card-box {
            max-width: 560px;
            width: 100%;
            padding: 42px 38px;
        }

        @media (max-width: 576px) {
            .challenge-card-box {
                padding: 30px 20px;
            }
        }

        .challenge-box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 191, 0, 0.25);
            border-left: 4px solid #FFBF00;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 14px;
            transition: all 0.2s ease;
        }

        .challenge-box.math {
            border-left-color: #28a745;
        }

        .challenge-box:focus-within {
            background: rgba(255, 255, 255, 0.06);
            border-color: #FFBF00;
            box-shadow: 0 0 20px rgba(255, 191, 0, 0.15);
        }

        .challenge-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            line-height: 1;
            background: #FFBF00;
            color: #000;
            font-weight: 700;
            border-radius: 50%;
            font-size: 13px;
            margin-right: 8px;
        }

        .challenge-box.math .challenge-number {
            background: #28a745;
            color: #fff;
        }

        .hint-pill {
            display: inline-block;
            margin-top: 10px;
            padding: 4px 14px;
            background: rgba(255, 191, 0, 0.12);
            border: 1px solid rgba(255, 191, 0, 0.3);
            border-radius: 9999px;
            font-family: monospace;
            font-size: 13.5px;
            color: #FFBF00;
            letter-spacing: 0.5px;
        }

        .challenge-input {
            height: 48px;
            background: rgba(255, 255, 255, 0.06) !important;
            border: 1.5px solid rgba(255, 191, 0, 0.25) !important;
            border-radius: 12px !important;
            color: #ffffff !important;
            padding: 10px 16px !important;
            font-size: 15px !important;
            transition: all 0.2s ease;
        }

        .challenge-input:focus {
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: #FFBF00 !important;
            box-shadow: 0 0 16px rgba(255, 191, 0, 0.35) !important;
            outline: none !important;
        }

        .regenerate-section {
            margin: 22px 0;
            padding-top: 18px;
            border-top: 1px dashed rgba(255, 191, 0, 0.25);
            text-align: center;
        }
    </style>
</head>

<body class="challenge-page-body">

<div class="challenge-wrapper">
    <div class="brand-card challenge-card-box">
        <form action="{{ route('password.challenge.verify') }}" method="POST" id="challengeForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="text-center mb-4">
                <div class="d-inline-flex p-3 rounded-4 mb-2" style="background:rgba(255, 191, 0,0.12);border:1.5px solid rgba(255, 191, 0,0.35);">
                    <i class="bi bi-shield-check text-warning fs-3"></i>
                </div>
                <h3 style="font-weight:700;color:#ffffff;margin-bottom:6px;">Identity Verification</h3>
                <p class="text-white-50" style="font-size:13.5px;margin:0;">
                    Please answer all <strong>{{ count($challenges) }} verification questions</strong> below to prove account ownership.
                </p>
            </div>

            @error('verification')
                <div class="alert alert-brand-danger mb-3">{{ $message }}</div>
            @enderror

            @foreach ($challenges as $index => $challenge)
                @php
                    $isMath = str_starts_with($challenge['key'], 'math');
                    $fieldName = 'answer_'.$challenge['key'];
                @endphp
                <div class="challenge-box {{ $isMath ? 'math' : '' }}">
                    <div class="d-flex align-items-center mb-2">
                        <span class="challenge-number">{{ $index + 1 }}</span>
                        <strong style="color:{{ $isMath ? '#7ce4a5' : '#FFBF00' }};font-size:14px;">
                            {{ $isMath ? 'Human Verification' : 'Account Verification' }}
                        </strong>
                    </div>

                    <p class="mb-2" style="font-size:14.5px;color:rgba(255,255,255,0.9);">
                        {{ $challenge['question'] }}
                    </p>

                    @if (!empty($challenge['hint']))
                        <div>
                            <span class="hint-pill">{{ $challenge['hint'] }}</span>
                        </div>
                    @endif

                    <div class="mt-3">
                        <input type="{{ $isMath ? 'text' : ($challenge['key'] === 'email' ? 'email' : 'text') }}"
                               name="{{ $fieldName }}"
                               class="form-control challenge-input"
                               value="{{ old($fieldName) }}"
                               required
                               autocomplete="off"
                               @if($isMath) inputmode="numeric" pattern="-?[0-9]+" placeholder="Enter number only" @endif
                               @if($challenge['key'] === 'phone') inputmode="tel" placeholder="Full phone number" @endif
                               @if($challenge['key'] === 'email') placeholder="Full email address" @endif>
                        @error($fieldName)
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @endforeach

            @if (($regenerationsRemaining ?? 0) > 0)
            <div class="regenerate-section">
                <p class="text-white-50" style="font-size:13px;margin-bottom:12px;">
                    Having difficulty answering these questions? You can request an alternate set ({{ $regenerationsRemaining }} remaining).
                </p>
                <button type="button" id="regenerateBtn" class="btn-brand-outline py-2 px-3" style="font-size:13px;">
                    <i class="bi bi-arrow-repeat me-1"></i> Get Different Questions
                </button>
            </div>
            @endif

            <button type="submit" id="verifyBtn" class="btn-brand w-100 py-3 mt-3">
                <span>Submit All Answers</span>
                <i class="bi bi-check2-circle fs-5"></i>
            </button>

            <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                <a href="{{ route('password.request') }}" class="btn-brand-ghost small">
                    <i class="bi bi-arrow-counterclockwise"></i> Start Over
                </a>
                <a href="{{ route('login') }}" class="btn-brand-ghost small">
                    <i class="bi bi-box-arrow-in-right"></i> Back to Login
                </a>
            </div>
        </form>

        @if (($regenerationsRemaining ?? 0) > 0)
        <form id="regenerateForm" action="{{ route('password.challenge.regenerate', ['token' => $token]) }}" method="POST" style="display:none;">
            @csrf
        </form>
        @endif
    </div>
</div>

<script>
    $('#challengeForm').on('submit', function () {
        $('#verifyBtn').prop('disabled', true).html('<span>Verifying answers...</span>');
    });

    $('#regenerateBtn').on('click', function () {
        Swal.fire({
            icon: 'question',
            title: 'Generate new questions?',
            background: '#0e0b12',
            color: '#ffffff',
            html: '<p style="font-size:14px;line-height:1.6;color:rgba(255,255,255,0.75);">Your current answers will be cleared and three new questions will be generated.</p>',
            showCancelButton: true,
            confirmButtonText: 'Yes, generate',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#FFBF00',
            cancelButtonColor: '#3a3540',
            reverseButtons: true,
            focusCancel: true,
        }).then(function (result) {
            if (result.isConfirmed) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    customClass: { popup: 'gememis-toast' },
                    title: 'Loading new questions',
                    showConfirmButton: false,
                    background: '#0e0b12',
                    color: '#ffffff',
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
            window.gememisToast({
                icon: 'success',
                title: 'Questions updated',
                text: @json(Session::get('status')),
            });
        @endif

        @if (Session::get('fail'))
            window.gememisToast({
                icon: 'error',
                title: 'Verification failed',
                text: @json(Session::get('fail')),
            });
        @endif
    });
</script>

</body>
</html>
