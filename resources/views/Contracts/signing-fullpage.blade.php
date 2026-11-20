<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_NAME') }} | Contract Signing</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}"/>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        #flutter-container {
            width: 100vw;
            height: 100vh;
            position: relative;
        }
        #flutter-app-frame {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
        /* Back button — circular animated (Uiverse.io / Creatlydev) */
        #back-button-wrap {
            position: fixed;
            top: 16px;
            right: 0%;
            top: 7%;
            transform: translateX(-50%);
            z-index: 1000;
            text-decoration: none;
        }
        .button {
            cursor: pointer;
            border: none;
            background: #081fd0;
            color: #fff;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            overflow: hidden;
            position: relative;
            display: grid;
            place-content: center;
            transition: background 300ms, transform 200ms;
            font-weight: 600;
        }
        .button__text {
            position: absolute;
            inset: 0;
            animation: text-rotation 8s linear infinite;
        }
        .button__text > span {
            position: absolute;
            transform: rotate(calc(21.18deg * var(--index)));
            inset: 6px;
            font-size: 10px;
            letter-spacing: 0;
        }
        .button__circle {
            position: relative;
            width: 36px;
            height: 36px;
            overflow: hidden;
            background: #fff;
            color: #7808d0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .button__icon--copy {
            position: absolute;
            transform: translate(-150%, 150%);
        }
        .button:hover {
            background: #000;
            transform: scale(1.05);
        }
        .button:hover .button__icon {
            color: #000;
        }
        .button:hover .button__icon:first-child {
            transition: transform 0.3s ease-in-out;
            transform: translate(150%, -150%);
        }
        .button:hover .button__icon--copy {
            transition: transform 0.3s ease-in-out 0.1s;
            transform: translate(0);
        }
        @keyframes text-rotation {
            to { rotate: 360deg; }
        }
    </style>
</head>
<body>
    <!-- Back button (role-aware: admins → contracts table, customers → dashboard) -->
    @php
        $backUrl = auth()->user()->can('view-contracts') ? route('contracts.index') : route('dashboard');
        $backText = auth()->user()->can('view-contracts') ? 'Go Back?' : 'Back to Dashboard';
        $backTitle = auth()->user()->can('view-contracts') ? 'Back to Contracts Management' : 'Back to Dashboard';
    @endphp
    @php
        $chars = str_split(strtoupper($backText));
    @endphp
    <a href="{{ $backUrl }}" id="back-button-wrap" title="{{ $backTitle }}">
        <button class="button">
            <p class="button__text">
                @foreach($chars as $i => $char)
                    <span style="--index: {{ $i }};">{{ $char }}</span>
                @endforeach
            </p>
            <div class="button__circle">
                {{-- left arrow --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="button__icon" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                    <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="button__icon button__icon--copy" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                    <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                </svg>
            </div>
        </button>
    </a>

    

    <!-- Flutter App Container -->
    <div id="flutter-container">
        @php
            $appHost = parse_url(config('app.url') ?? '', PHP_URL_HOST) ?: null;
            $requestHost = request()->getHost();
            // If configured app URL host differs from current request host (e.g. localhost vs 127.0.0.1),
            // use a same-origin relative path to avoid X-Frame-Options / SAMEORIGIN issues when testing locally.
            $iframeBase = ($appHost && $appHost !== $requestHost)
                ? '/flutter/contract-signing/index.html'
                : asset('flutter/contract-signing/index.html');
        @endphp

        <iframe
            id="flutter-app-frame"
            src="{{ $iframeBase }}{{ $registrationID ? '?registrationID=' . $registrationID : '' }}"
            title="Contract Signing Application"
            allow="clipboard-read; clipboard-write"
        ></iframe>
    </div>

    <script>
        
        const iframe = document.getElementById('flutter-app-frame');
        const backButton = document.getElementById('back-button-wrap');

        // Fade out back button after iframe loads
        iframe.addEventListener('load', function() {
            setTimeout(function() {
                if (backButton) {
                    backButton.style.opacity = '0.3';
                    backButton.addEventListener('mouseenter', function() {
                        this.style.opacity = '1';
                    });
                    backButton.addEventListener('mouseleave', function() {
                        this.style.opacity = '0.3';
                    });
                }
            }, 3000);
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                var confirmMsg = @json(auth()->user()->can('view-contracts') ? 'Exit and return to contracts list?' : 'Exit and return to dashboard?');
                var redirectUrl = @json($backUrl);
                if (confirm(confirmMsg)) {
                    window.location.href = redirectUrl;
                }
            }
        });

        window.addEventListener('message', function(event) {
            if (event.data.type === 'signature_complete') {
                setTimeout(function() {
                    var confirmMsg = @json(auth()->user()->can('view-contracts') ? 'Contract signed successfully! Return to contracts list?' : 'Contract signed successfully! Return to dashboard?');
                    var redirectUrl = @json($backUrl);
                    if (confirm(confirmMsg)) {
                        window.location.href = redirectUrl;
                    }
                }, 1000);
            }
        });
    </script>
</body>
</html>
