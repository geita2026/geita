<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Print Car Stickers</title>
    <script type="text/javascript" src="https://unpkg.com/qr-code-styling@1.9.2/lib/qr-code-styling.js"></script>
    <style>
        /* ── A4 page setup ─────────────────────────────────────────────── */
        @page { size: A4 portrait; margin: 0; }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; background: #e8e8e8; font-family: 'Inter','Segoe UI',Roboto,sans-serif; }

        body.layout-6   { --grid-rows: 3; --sticker-w: 56.5mm; --sticker-h: 99mm;  }
        body.layout-4   { --grid-rows: 2; --sticker-w: 88mm;   --sticker-h: 120mm; }
        body.layout-4xl { --grid-rows: 2; --sticker-w: 86mm;   --sticker-h: 127mm; }

        /* ── Sidebar — offer-card style ────────────────────────────────── */
        :root {
            --sb-bg:          #0b0c10;
            --sb-bg-light:    #12141a;
            --sb-bg-hover:    #1a1c23;
            --sb-border:      rgba(255,255,255,0.05);
            --sb-border-hover:rgba(255,255,255,0.1);
            --sb-text:        #ffffff;
            --sb-text-dim:    #9496a8;
            --sb-accent:      #FFBF00;
            --sb-accent-rgb:  255,191,0;
            --sb-accent-bg:   #2d2510;
        }
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: 220px; height: 100vh;
            background: var(--sb-bg);
            border-right: 1px solid var(--sb-border);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            z-index: 10; color: var(--sb-text);
            overflow: hidden;
        }
        .sidebar::before {
            content: ""; position: absolute;
            top: 50%; transform: translateY(-50%);
            left: -40px; width: 60px; height: 50%;
            background: var(--sb-accent);
            filter: blur(60px); opacity: 0.12;
            pointer-events: none;
        }
        .sidebar > * { position: relative; z-index: 1; }

        .sidebar-inner {
            width: 100%; padding: 1.6em 1.2em;
            display: flex; flex-direction: column; gap: 1.4em;
        }

        .sb-header { text-align: center; }
        .sb-title  { margin: 0 0 0.25em; font-size: 1.15em; font-weight: 600; }
        .sb-subtitle { margin: 0; font-size: 0.78em; color: var(--sb-text-dim); line-height: 1.4; }

        .sb-progress { display: flex; justify-content: center; gap: 0.4em; }
        .sb-dot { width: 2em; height: 0.22em; background: rgba(255,255,255,0.12); border-radius: 1em; }
        .sb-dot--active { background: var(--sb-accent); box-shadow: 0 0 10px rgba(var(--sb-accent-rgb),0.5); }

        .sb-options { display: flex; flex-direction: column; gap: 0.7em; width: 100%; }
        .sb-option  { cursor: pointer; display: block; user-select: none; position: relative; }
        .sb-option input { position: absolute; opacity: 0; width: 0; height: 0; }

        /* Timetable gold-pill border + shades (border-box amber gradient,
           dark padding-box bg, inner radial glow, inset highlight + glow). */
        .sb-option-content {
            position: relative;
            display: flex; align-items: center;
            padding: 0.7em 1em;
            border: 1.5px solid transparent;
            border-radius: 20px;
            background:
                radial-gradient(120% 80% at 50% -5%, rgba(255,191,0,0.2), transparent 58%) padding-box,
                linear-gradient(71deg, #080509, #1a171c, #080509) padding-box,
                linear-gradient(71deg, #110e0e, #FFBF00, #110e0e) border-box;
            box-shadow:
                inset 0 1px 1px rgba(255,255,255,0.06),
                0 8px 22px -12px rgba(255,191,0,0.4);
            transition: transform 0.2s ease, box-shadow 0.25s ease;
        }
        .sb-option input:active + .sb-option-content { transform: scale(0.98); }

        /* Hover: lift + a touch more glow */
        .sb-option input:hover + .sb-option-content {
            transform: translateY(-1px);
            box-shadow:
                inset 0 1px 1px rgba(255,255,255,0.08),
                0 10px 26px -10px rgba(255,191,0,0.55);
        }

        /* Selected: stronger glow + amber ring + filled accent icon */
        .sb-option input:checked + .sb-option-content {
            box-shadow:
                inset 0 1px 1px rgba(255,255,255,0.08),
                0 0 0 1px rgba(255,191,0,0.5),
                0 8px 26px -8px rgba(255,191,0,0.6);
        }
        .sb-option input:checked + .sb-option-content .sb-option-icon {
            background: #FFBF00;
            color: #0b0c10;
        }
        .sb-option-icon {
            width: 2.4em; height: 2.4em; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            background: var(--sb-accent-bg); color: var(--sb-accent);
            margin-right: 0.9em; flex-shrink: 0;
        }
        .sb-option-icon svg { width: 1.1em; height: 1.1em; }
        .sb-option-text  { flex-grow: 1; display: flex; flex-direction: column; gap: 0.15em; }
        .sb-option-title { font-weight: 500; font-size: 0.88em; }
        .sb-option-desc  { font-size: 0.72em; color: var(--sb-text-dim); }
        .sb-option-arrow {
            width: 1.8em; height: 1.8em; border-radius: 50%;
            background: rgba(255,255,255,0.04);
            display: flex; align-items: center; justify-content: center;
            color: var(--sb-text-dim); transition: all 0.3s ease; flex-shrink: 0;
        }
        .sb-option-arrow svg { width: 0.9em; height: 0.9em; }

        /* ── Print button (Uiverse.io — animated, scaled & recoloured amber) ── */
        .print-btn-wrap { display: flex; justify-content: center; margin: 0.6em 0 0.4em; }
        .print-btn {
            --white:   #f0ebf0;
            --amber-100: #5a4a1a;
            --amber-200: #111011;
            --amber-300: #141114;
            --amber-400: #1f170b;
            --amber-500: #0d0804;
            --radius: 18px;
            border-radius: var(--radius);
            outline: none; cursor: pointer;
            font-size: 18px;
            font-family: Arial;
            background: transparent;
            letter-spacing: -1px;
            border: 0; position: relative;
            width: 180px; height: 60px;
            transform: rotate(353deg) skewX(4deg);
        }
        .print-btn .bg { position: absolute; inset: 0; border-radius: inherit; filter: blur(1px); }
        .print-btn .bg::before,
        .print-btn .bg::after {
            content: ""; position: absolute; inset: 0;
            border-radius: calc(var(--radius) * 1.1);
            background: var(--amber-500);
        }
        .print-btn .bg::before {
            filter: blur(5px); transition: all 0.3s ease;
            box-shadow:
                -6px 5px 0 0 rgb(255 191 0 / 40%),
                -12px 10px 0 0 rgb(255 191 0 / 30%),
                -18px 15px 4px 0 rgb(255 191 0 / 25%),
                -24px 20px 8px 0 rgb(255 191 0 / 15%),
                -30px 25px 12px 0 rgb(255 191 0 / 10%),
                -36px 30px 16px 0 rgb(255 191 0 / 6%);
        }
        .print-btn .wrap {
            border-radius: inherit; overflow: hidden; height: 100%;
            transform: translate(5px, -5px); padding: 3px;
            background: linear-gradient(to bottom, var(--amber-100) 0%, var(--amber-400) 100%);
            position: relative; transition: all 0.3s ease;
        }
        .print-btn .outline {
            position: absolute; overflow: hidden; inset: 0;
            opacity: 0; outline: none; border-radius: inherit;
            transition: all 0.4s ease;
        }
        .print-btn .outline::before {
            content: ""; position: absolute; inset: 2px;
            width: 110px; height: 240px; margin: auto;
            background: linear-gradient(to right, transparent 0%, white 50%, transparent 100%);
            animation: pb-spin 3s linear infinite; animation-play-state: paused;
        }
        .print-btn .content {
            pointer-events: none; display: flex; align-items: center; justify-content: center;
            z-index: 1; position: relative; height: 100%; gap: 12px;
            border-radius: calc(var(--radius) * 0.85);
            font-weight: 600; transition: all 0.3s ease;
            background: linear-gradient(to bottom, var(--amber-300) 0%, var(--amber-400) 100%);
            box-shadow:
                inset -2px 10px 9px -5px var(--amber-200),
                inset 1px -3px 9px 0px rgb(0 0 0 / 35%);
        }
        .print-btn .content::before {
            content: ""; inset: 0; position: absolute; z-index: 10;
            width: 80%; top: 45%; bottom: 35%; opacity: 0.7; margin: auto;
            background: linear-gradient(to bottom, transparent, var(--amber-400));
            filter: brightness(1.3) blur(5px);
        }
        .print-btn .char {
            transition: all 0.3s ease;
            display: flex; align-items: center; justify-content: center;
        }
        .print-btn .char span {
            display: block; color: transparent; position: relative;
        }
        .print-btn .char span:nth-child(6) { margin-left: 5px; }
        .print-btn .char.state-1 span:nth-child(6) { margin-right: -2px; }
        .print-btn .char.state-1 span {
            animation: pb-charAppear 1.2s ease backwards calc(var(--i) * 0.03s);
        }
        .print-btn .char.state-1 span::before,
        .print-btn .char span::after {
            content: attr(data-label); position: absolute;
            color: var(--white);
            text-shadow: -1px 1px 2px var(--amber-500);
            left: 0;
        }
        .print-btn .char span::before { opacity: 0; transform: translateY(-100%); }
        .print-btn .char.state-2 { position: absolute; left: 60px; }
        .print-btn .char.state-2 span::after { opacity: 1; }
        .print-btn .icon {
            animation: pb-resetArrow 0.8s cubic-bezier(0.7, -0.5, 0.3, 1.2) forwards;
            z-index: 10;
        }
        .print-btn .icon div,
        .print-btn .icon div::before,
        .print-btn .icon div::after {
            height: 2.5px; border-radius: 1px; background-color: var(--white);
        }
        .print-btn .icon div::before,
        .print-btn .icon div::after {
            content: ""; position: absolute; right: 0;
            transform-origin: center right; width: 11px; border-radius: 15px;
            transition: all 0.3s ease;
        }
        .print-btn .icon div {
            position: relative; width: 20px;
            box-shadow: -2px 2px 5px var(--amber-400);
            transform: scale(0.9);
            background: linear-gradient(to bottom, var(--white), var(--amber-100));
            animation: pb-swingArrow 1s ease-in-out infinite; animation-play-state: paused;
        }
        .print-btn .icon div::before {
            transform: rotate(44deg); top: 1px;
            box-shadow: 1px -2px 3px -1px var(--amber-400);
            animation: pb-rotateArrowLine 1s linear infinite; animation-play-state: paused;
        }
        .print-btn .icon div::after {
            bottom: 1px; transform: rotate(316deg);
            box-shadow: -2px 2px 3px 0 var(--amber-400);
            background: linear-gradient(200deg, var(--white), var(--amber-100));
            animation: pb-rotateArrowLine2 1s linear infinite; animation-play-state: paused;
        }
        .print-btn .path {
            position: absolute; z-index: 12; bottom: 0; left: 0; right: 0;
            stroke-dasharray: 150 480; stroke-dashoffset: 150; pointer-events: none;
        }
        .print-btn .splash {
            position: absolute; top: 0; left: 0; pointer-events: none;
            stroke-dasharray: 60 60; stroke-dashoffset: 60;
            transform: translate(-17%, -31%);
            stroke: var(--amber-300);
        }
        .print-btn:hover .wrap { transform: translate(7px, -7px); }
        .print-btn:hover .outline { opacity: 1; }
        .print-btn:hover .outline::before,
        .print-btn:hover .icon div::before,
        .print-btn:hover .icon div::after,
        .print-btn:hover .icon div { animation-play-state: running; }
        .print-btn:hover .char.state-1 span::before { animation: pb-charAppear 0.7s ease calc(var(--i) * 0.03s); }
        .print-btn:hover .char.state-1 span::after  { opacity: 1; animation: pb-charDisappear 0.7s ease calc(var(--i) * 0.03s); }
        .print-btn:active .bg::before {
            filter: blur(5px); opacity: 0.7;
            box-shadow:
                -6px 5px 0 0 rgb(255 191 0 / 40%),
                -12px 10px 0 0 rgb(255 191 0 / 25%),
                -18px 15px 4px 0 rgb(255 191 0 / 15%);
        }
        .print-btn:active .content {
            box-shadow:
                inset -1px 10px 7px -5px rgba(255,191,0, 0.4),
                inset 0px -3px 7px 0px var(--amber-200);
        }
        .print-btn:active .outline { opacity: 0; }
        .print-btn:active .wrap { transform: translate(3px, -3px); }
        .print-btn:active .splash { animation: pb-splash 0.8s cubic-bezier(0.3, 0, 0, 1) forwards 0.05s; }
        .print-btn:focus .path { animation: pb-path 1.6s ease forwards 0.2s; }
        .print-btn:focus .icon { animation: pb-arrow 1s cubic-bezier(0.7, -0.5, 0.3, 1.5) forwards; }
        .print-btn .char.state-2 span::after,
        .print-btn:focus .char.state-1 span { animation: pb-charDisappear 0.5s ease forwards calc(var(--i) * 0.03s); }
        .print-btn:focus .char.state-2 span::after { animation: pb-charAppear 1s ease backwards calc(var(--i) * 0.03s); }

        @keyframes pb-spin { 0%{transform:rotate(0)} 100%{transform:rotate(360deg)} }
        @keyframes pb-charAppear {
            0%   { transform: translateY(50%);  opacity: 0; filter: blur(20px); }
            20%  { transform: translateY(70%);  opacity: 1; }
            50%  { transform: translateY(-15%); opacity: 1; filter: blur(0); }
            100% { transform: translateY(0);    opacity: 1; }
        }
        @keyframes pb-charDisappear {
            0%   { transform: translateY(0);    opacity: 1; }
            100% { transform: translateY(-70%); opacity: 0; filter: blur(3px); }
        }
        @keyframes pb-arrow {
            0%   { opacity: 1; }
            50%  { transform: translateX(50px); opacity: 0; }
            51%  { transform: translateX(-180px); opacity: 0; }
            100% { transform: translateX(-108px); opacity: 1; }
        }
        @keyframes pb-swingArrow { 50% { transform: translateX(4px) scale(0.9); } }
        @keyframes pb-rotateArrowLine { 50% { transform: rotate(30deg); } 80% { transform: rotate(55deg); } }
        @keyframes pb-rotateArrowLine2 { 50% { transform: rotate(330deg); } 80% { transform: rotate(300deg); } }
        @keyframes pb-resetArrow { 0% { transform: translateX(-108px); } 100% { transform: translateX(0); } }
        @keyframes pb-path { from { stroke: white; } to { stroke-dashoffset: -480; stroke: #121112; } }
        @keyframes pb-splash { to { stroke-dasharray: 2 60; stroke-dashoffset: -60; } }

        /* Footer */
        .sb-footer { display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .sb-btn {
            background: transparent; border: 1px solid rgba(255,255,255,0.05);
            color: var(--sb-text-dim); padding: 0.45em 0.9em 0.45em 0.45em;
            border-radius: 2em; font-size: 0.78em; font-weight: 500;
            cursor: pointer; display: flex; align-items: center; gap: 0.5em;
            transition: all 0.3s ease; font-family: inherit; text-decoration: none;
        }
        .sb-btn:hover { background: rgba(255,255,255,0.05); color: var(--sb-text); }
        .sb-btn:active { transform: scale(0.96); }
        .sb-btn-icon {
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.08); width: 1.8em; height: 1.8em; border-radius: 8px;
        }
        .sb-btn-icon svg { width: 0.9em; height: 0.9em; }
        .sb-count { color: var(--sb-text-dim); font-size: 0.72em; }

        /* ── Sheets area ───────────────────────────────────────────────── */
        .sheets-area {
            margin-left: 220px; padding: 20px;
            display: flex; flex-direction: column; align-items: center; gap: 20px;
        }

        .sheet {
            width: 210mm; height: 297mm; background: white;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(var(--grid-rows, 3), 1fr);
            box-shadow: 0 6px 24px rgba(0,0,0,0.25);
            position: relative;
        }

        .cell {
            display: flex; align-items: center; justify-content: center;
            position: relative;
        }

        /* ── Sticker — 1590 × 2786 aspect (1.752) ────────────────────── */
        .sticker-wrap {
            position: relative;
            width:  var(--sticker-w, 56.5mm);
            height: var(--sticker-h, 99mm);
            overflow: visible;
        }

        .sticker-bg {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%; display: block;
        }

        .sticker-overlay {
            position: absolute;
            white-space: nowrap;
        }

        .sticker-plate {
            font-family: 'Arial Black', 'Impact', sans-serif;
            font-weight: 900;
            color: #ffffff;
            text-align: center;
            letter-spacing: 0.08em;
            transform: translateX(-50%);
            text-shadow: 0 2px 6px rgba(0,0,0,0.6);
        }

        /* Dynamic "SEASONAL {INSIDE|OUTSIDE} CAR PARKING" caption */
        .sticker-zone {
            font-family: 'Arial', 'Segoe UI', sans-serif;
            font-weight: 700;
            color: #ffffff;
            text-align: center;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            transform: translateX(-50%);
            text-shadow: 0 1px 4px rgba(0,0,0,0.5);
        }

        .qr-canvas {
            position: absolute;
            box-sizing: border-box;
            background: #fff;
            border-radius: 12px;
            padding: 6px;
        }
        .qr-canvas svg { display: block; width: 100%; height: 100%; }

        /* ── Print-mode visibility ─────────────────────────────────────── */
        body.mode-front .sheet-back { display: none !important; }
        body.mode-back  .sheet-front { display: none !important; }

        /* ── Print rules ───────────────────────────────────────────────── */
        @media print {
            html, body { background: white; }
            .sidebar { display: none !important; }
            .sheets-area { margin: 0; padding: 0; gap: 0; }
            .sheet { box-shadow: none; page-break-after: always; }
            .sheet:last-child { page-break-after: auto; }
            body.mode-front .sheet-back { display: none !important; }
            body.mode-back  .sheet-front { display: none !important; }
        }
    </style>
</head>
@php
    $stickerFront = parse_url(asset('seasonal_car_sticker_front.png'), PHP_URL_PATH);
    $stickerBack  = parse_url(asset('seasonal_car_sticker_back.png'),  PHP_URL_PATH);
@endphp
<body class="layout-{{ $layout }}">

    {{-- ── Sidebar ──────────────────────────────────────────────────── --}}
    <div class="sidebar">
        <div class="sidebar-inner">
            <div class="sb-progress">
                <div class="sb-dot sb-dot--active"></div>
                <div class="sb-dot {{ in_array($layout, ['4','4xl'], true) ? 'sb-dot--active' : '' }}"></div>
                <div class="sb-dot {{ $layout === '4xl' ? 'sb-dot--active' : '' }}"></div>
            </div>

            <div class="sb-header">
                <div class="sb-title">Car Stickers</div>
                <div class="sb-subtitle">{{ count($stickers) }} sticker{{ count($stickers) === 1 ? '' : 's' }} ready to print</div>
            </div>

            {{-- Layout options --}}
            <form action="{{ url()->current() }}" method="POST" class="sb-options">
                @csrf
                @method('GET')
                @foreach (request()->except(['layout', '_method', '_token']) as $key => $value)
                    @if (is_array($value))
                        @foreach ($value as $v)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}"/>
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}"/>
                    @endif
                @endforeach

                <label class="sb-option">
                    <input type="radio" name="layout" value="6"
                           {{ $perPage === 6 ? 'checked' : '' }}
                           onchange="this.form.submit()"/>
                    <div class="sb-option-content">
                        <div class="sb-option-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect>
                            </svg>
                        </div>
                        <div class="sb-option-text">
                            <span class="sb-option-title">6 per page</span>
                            <span class="sb-option-desc">2 &times; 3 grid</span>
                        </div>
                        <div class="sb-option-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></div>
                    </div>
                </label>

                <label class="sb-option">
                    <input type="radio" name="layout" value="4"
                           {{ $layout === '4' ? 'checked' : '' }}
                           onchange="this.form.submit()"/>
                    <div class="sb-option-content">
                        <div class="sb-option-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="8" height="8"></rect><rect x="13" y="3" width="8" height="8"></rect>
                                <rect x="3" y="13" width="8" height="8"></rect><rect x="13" y="13" width="8" height="8"></rect>
                            </svg>
                        </div>
                        <div class="sb-option-text">
                            <span class="sb-option-title">4 per page (B7)</span>
                            <span class="sb-option-desc">88 &times; 120 mm</span>
                        </div>
                        <div class="sb-option-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></div>
                    </div>
                </label>

                <label class="sb-option">
                    <input type="radio" name="layout" value="4xl"
                           {{ $layout === '4xl' ? 'checked' : '' }}
                           onchange="this.form.submit()"/>
                    <div class="sb-option-content">
                        <div class="sb-option-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="9" height="9"></rect><rect x="13" y="2" width="9" height="9"></rect>
                                <rect x="2" y="13" width="9" height="9"></rect><rect x="13" y="13" width="9" height="9"></rect>
                            </svg>
                        </div>
                        <div class="sb-option-text">
                            <span class="sb-option-title">4 per page (XL)</span>
                            <span class="sb-option-desc">8.6 &times; 12.7 cm</span>
                        </div>
                        <div class="sb-option-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></div>
                    </div>
                </label>
            </form>

            {{-- Print mode --}}
            <div class="sb-options">
                <label class="sb-option">
                    <input type="radio" name="print-mode" value="both" checked onchange="setPrintMode('both')"/>
                    <div class="sb-option-content">
                        <div class="sb-option-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="18" rx="2"></rect><line x1="12" y1="3" x2="12" y2="21"></line></svg></div>
                        <div class="sb-option-text"><span class="sb-option-title">Both sides</span><span class="sb-option-desc">Front + back</span></div>
                        <div class="sb-option-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></div>
                    </div>
                </label>
                <label class="sb-option">
                    <input type="radio" name="print-mode" value="front" onchange="setPrintMode('front')"/>
                    <div class="sb-option-content">
                        <div class="sb-option-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"></rect><line x1="8" y1="7" x2="16" y2="7"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg></div>
                        <div class="sb-option-text"><span class="sb-option-title">Front only</span><span class="sb-option-desc">Plate + QR side</span></div>
                        <div class="sb-option-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></div>
                    </div>
                </label>
                <label class="sb-option">
                    <input type="radio" name="print-mode" value="back" onchange="setPrintMode('back')"/>
                    <div class="sb-option-content">
                        <div class="sb-option-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"></rect><circle cx="12" cy="12" r="4"></circle></svg></div>
                        <div class="sb-option-text"><span class="sb-option-title">Back only</span><span class="sb-option-desc">Logo side</span></div>
                        <div class="sb-option-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></div>
                    </div>
                </label>
            </div>

            <div class="print-btn-wrap">
                <button class="print-btn" onclick="window.print()">
                    <div class="bg"></div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 342 208" height="208" width="342" class="splash">
                        <path stroke-linecap="round" stroke-width="3"
                              d="M54.1054 99.7837C54.1054 99.7837 40.0984 90.7874 26.6893 97.6362C13.2802 104.485 1.5 97.6362 1.5 97.6362"></path>
                        <path stroke-linecap="round" stroke-width="3"
                              d="M285.273 99.7841C285.273 99.7841 299.28 90.7879 312.689 97.6367C326.098 104.486 340.105 95.4893 340.105 95.4893"></path>
                        <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3"
                              d="M281.133 64.9917C281.133 64.9917 287.96 49.8089 302.934 48.2295C317.908 46.6501 319.712 36.5272 319.712 36.5272"></path>
                        <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3"
                              d="M281.133 138.984C281.133 138.984 287.96 154.167 302.934 155.746C317.908 157.326 319.712 167.449 319.712 167.449"></path>
                        <path stroke-linecap="round" stroke-width="3"
                              d="M230.578 57.4476C230.578 57.4476 225.785 41.5051 236.061 30.4998C246.337 19.4945 244.686 12.9998 244.686 12.9998"></path>
                        <path stroke-linecap="round" stroke-width="3"
                              d="M230.578 150.528C230.578 150.528 225.785 166.471 236.061 177.476C246.337 188.481 244.686 194.976 244.686 194.976"></path>
                        <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3"
                              d="M170.392 57.0278C170.392 57.0278 173.89 42.1322 169.571 29.54C165.252 16.9478 168.751 2.05227 168.751 2.05227"></path>
                        <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3"
                              d="M170.392 150.948C170.392 150.948 173.89 165.844 169.571 178.436C165.252 191.028 168.751 205.924 168.751 205.924"></path>
                        <path stroke-linecap="round" stroke-width="3"
                              d="M112.609 57.4476C112.609 57.4476 117.401 41.5051 107.125 30.4998C96.8492 19.4945 98.5 12.9998 98.5 12.9998"></path>
                        <path stroke-linecap="round" stroke-width="3"
                              d="M112.609 150.528C112.609 150.528 117.401 166.471 107.125 177.476C96.8492 188.481 98.5 194.976 98.5 194.976"></path>
                        <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3"
                              d="M62.2941 64.9917C62.2941 64.9917 55.4671 49.8089 40.4932 48.2295C25.5194 46.6501 23.7159 36.5272 23.7159 36.5272"></path>
                        <path stroke-linecap="round" stroke-width="3" stroke-opacity="0.3"
                              d="M62.2941 145.984C62.2941 145.984 55.4671 161.167 40.4932 162.746C25.5194 164.326 23.7159 174.449 23.7159 174.449"></path>
                    </svg>
                    <div class="wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 221 42" height="42" width="221" class="path">
                            <path stroke-linecap="round" stroke-width="3"
                                  d="M182.674 2H203C211.837 2 219 9.16344 219 18V24C219 32.8366 211.837 40 203 40H18C9.16345 40 2 32.8366 2 24V18C2 9.16344 9.16344 2 18 2H47.8855"></path>
                        </svg>
                        <div class="outline"></div>
                        <div class="content">
                            <span class="char state-1">
                                <span data-label="P" style="--i: 1">P</span>
                                <span data-label="r" style="--i: 2">r</span>
                                <span data-label="i" style="--i: 3">i</span>
                                <span data-label="n" style="--i: 4">n</span>
                                <span data-label="t" style="--i: 5">t</span>
                                <span data-label="A" style="--i: 6">A</span>
                                <span data-label="4" style="--i: 7">4</span>
                            </span>
                            <div class="icon"><div></div></div>
                            <span class="char state-2">
                                <span data-label="P" style="--i: 1">P</span>
                                <span data-label="r" style="--i: 2">r</span>
                                <span data-label="i" style="--i: 3">i</span>
                                <span data-label="n" style="--i: 4">n</span>
                                <span data-label="t" style="--i: 5">t</span>
                                <span data-label="!" style="--i: 6">!</span>
                            </span>
                        </div>
                    </div>
                </button>
            </div>

            <div class="sb-footer">
                <a class="sb-btn" href="javascript:history.back()">
                    <span class="sb-btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"></path></svg></span>
                    Back
                </a>
                <div class="sb-count">{{ count($stickers) }} sticker{{ count($stickers) === 1 ? '' : 's' }}</div>
            </div>
        </div>
    </div>

    {{-- ── Sheets ───────────────────────────────────────────────────── --}}
    <div class="sheets-area">
        @foreach (array_chunk($stickers, $perPage) as $page)
            {{-- Front sheet --}}
            <div class="sheet sheet-front">
                @foreach ($page as $idx => $s)
                    <div class="cell">
                        <div class="sticker-wrap front-sticker"
                             data-plate="{{ $s['plateNumber'] }}"
                             data-zone="{{ $s['zone'] ?? 'INSIDE' }}">
                            <img class="sticker-bg" src="{{ $stickerFront }}" alt=""/>
                            <div class="sticker-overlay sticker-plate"></div>
                            <div class="sticker-overlay sticker-zone"></div>
                            <div class="qr-canvas" id="sqr-{{ $loop->parent->index }}-{{ $idx }}"></div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        new QRCodeStyling({
                            width: 200, height: 200, type: "svg",
                            data: "SAMS|{{ $s['stickerId'] }}",
                            dotsOptions:          { color: "#000000", type: "rounded" },
                            cornersSquareOptions: { color: "#000000", type: "extra-rounded" },
                            cornersDotOptions:    { color: "#000000" },
                            backgroundOptions:    { color: "#ffffff" },
                            qrOptions:            { margin: 0 },
                        }).append(document.getElementById("sqr-{{ $loop->parent->index }}-{{ $idx }}"));
                    </script>
                @endforeach
                @for ($i = count($page); $i < $perPage; $i++)
                    <div class="cell"></div>
                @endfor
            </div>

            {{-- Back sheet --}}
            <div class="sheet sheet-back">
                @for ($i = 0; $i < $perPage; $i++)
                    <div class="cell">
                        @if (($i ^ 1) < count($page))
                            <div class="sticker-wrap">
                                <img class="sticker-bg" src="{{ $stickerBack }}" alt=""/>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        @endforeach
    </div>

    <script>
        // ── Sticker image coord system: 1590 × 2786 ─────────────────────
        const IMG_W = 1590, IMG_H = 2786;
        const PLATE_CX = 960,  PLATE_Y = 1380, PLATE_FS = 70;
        const ZONE_CX  = 960,  ZONE_Y  = 1560, ZONE_FS  = 50;
        const QR_CX    = 960,  QR_CY   = 2050, QR_SIZE  = 540;

        function positionSticker(wrap) {
            const bW = wrap.offsetWidth;
            const bH = wrap.offsetHeight;
            const scaleX = bW / IMG_W;
            const scaleY = bH / IMG_H;

            // ── Plate number ──────────────────────────────────────────
            const plate = wrap.querySelector('.sticker-plate');
            if (plate) {
                plate.textContent = wrap.dataset.plate || '';
                plate.style.left     = (PLATE_CX * scaleX).toFixed(2) + 'px';
                plate.style.top      = (PLATE_Y * scaleY).toFixed(2) + 'px';
                plate.style.fontSize = (PLATE_FS * scaleX).toFixed(2) + 'px';
            }

            // ── Parking-zone caption (SEASONAL {INSIDE|OUTSIDE} CAR PARKING) ──
            const zone = wrap.querySelector('.sticker-zone');
            if (zone) {
                const label = (wrap.dataset.zone || '').toUpperCase();
                zone.textContent = label ? `SEASONAL ${label} CAR PARKING` : '';
                zone.style.left     = (ZONE_CX * scaleX).toFixed(2) + 'px';
                zone.style.top      = (ZONE_Y * scaleY).toFixed(2) + 'px';
                zone.style.fontSize = (ZONE_FS * scaleX).toFixed(2) + 'px';
            }

            // ── QR ────────────────────────────────────────────────────
            const qr = wrap.querySelector('.qr-canvas');
            if (qr) {
                const size = Math.round(QR_SIZE * scaleX);
                qr.style.left   = Math.round((QR_CX - QR_SIZE/2) * scaleX) + 'px';
                qr.style.top    = Math.round((QR_CY - QR_SIZE/2) * scaleY) + 'px';
                qr.style.width  = size + 'px';
                qr.style.height = size + 'px';
            }
        }

        function setPrintMode(mode) {
            document.body.classList.remove('mode-front', 'mode-back');
            if (mode !== 'both') document.body.classList.add('mode-' + mode);
        }

        window.addEventListener('load', () => {
            document.querySelectorAll('.front-sticker').forEach(positionSticker);
        });
    </script>

</body>
</html>
