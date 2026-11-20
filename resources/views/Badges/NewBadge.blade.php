<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Badge Preview</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #e8e8e8;
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            overflow-y: auto;
        }

        /* ── Left sidebar — action buttons ─────────────────────────── */
        .sidebar {
            position: sticky;
            top: 0;
            width: 160px;
            min-height: 100vh;
            background: #1b1b1b;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            padding: 24px 16px;
            flex-shrink: 0;
        }

        .sidebar h4 {
            color: #aaa;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
            text-align: center;
        }

        .btn {
            width: 100%;
            padding: 12px 10px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: opacity .2s;
        }
        .btn:hover { opacity: .85; }

        .btn-pdf {
            background: #13487e;
            color: #fff;
        }
        .btn-print {
            background: #e9b731;
            color: #1b1b1b;
        }
        .btn-back {
            background: #444;
            color: #fff;
            font-weight: normal;
            font-size: 12px;
        }

        /* ── Badge area — scrollable, takes remaining width ────────── */
        .badge-area {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            min-height: 100vh;
        }

        /* Badge wrapper — full viewport height */
        .badge-wrap {
            position: relative;
            /* SVG aspect ratio: 576 × 864.16 → width = height / 1.5003 */
            height: calc(100vh - 40px);
            width:  calc((100vh - 40px) / 1.5003);
            box-shadow: 0 12px 48px rgba(0,0,0,0.35);
            border-radius: 8px;
            overflow: visible;
            flex-shrink: 0;
        }

        .badge-wrap img.badge-bg {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
        }

        /*
            QR overlay — pixel-precise.
            Badge display width = min(520px, 90vw) — using 520px as base.
            Scale factor: 520 / 576 = 0.90278
            Badge height: 520 × 1.5003 = 780.15px

            QR in SVG: x=310.94, y=298.46, w=156.49, h=156.49
            In pixels (scale 0.90278):
              left  = 310.94 × 0.90278 = 280.8px
              top   = 298.46 × (780.15/864.16) = 269.4px
              size  = 156.49 × 0.90278 = 141.3px  → use 150px (slightly larger)
        */
        /* All overlays — positioned by JS */
        #qr-canvas, .badge-text {
            position: absolute;
            white-space: nowrap;
        }

        #qr-canvas svg,
        #qr-canvas canvas {
            display: block;
        }

        #badge-name {
            font-family: 'Times New Roman', serif;
            font-weight: 700;
            color: #1b1b1b;
            letter-spacing: 0.05em;
            transform: translateX(-50%);
        }

        #badge-company {
            font-family: Arial, sans-serif;
            font-weight: normal;
            color: #444444;
            transform: translateX(-50%);
        }

        #badge-strip {
            position: absolute;
            border-radius: 4px;
        }

        #badge-group {
            font-family: 'Times New Roman', serif;
            font-weight: 700;
            letter-spacing: 0.06em;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>

    {{-- ── Sidebar ──────────────────────────────────────────────── --}}
    @unless($isPdf)
    <div class="sidebar">
        <h4>Actions</h4>

        <a class="btn btn-pdf"
           href="{{ url('badgesManagement/badge_pdf/' . $memberId) }}"
           target="_blank">
            ⬇ Download PDF
        </a>

        <button class="btn btn-print" onclick="window.print()">
            🖨 Print
        </button>

        <a class="btn btn-back" href="javascript:history.back()">
            ← Go Back
        </a>
    </div>
    @endunless

    {{-- ── Badge ────────────────────────────────────────────────── --}}
    <div class="badge-area">
        <div class="badge-wrap">
            <img class="badge-bg"
                 src="{{ asset('ditf26.png') }}"
                 alt="Badge"/>
            <div id="qr-canvas"></div>
            <div id="badge-strip"></div>
            <div id="badge-name"    class="badge-text">{{ strtoupper($badgeName) }}</div>
            <div id="badge-company" class="badge-text">{{ strtoupper($badgeCompany) }}</div>
            <div id="badge-group"   class="badge-text"
                 style="color: {{ $badgeTextColor }}">{{ strtoupper($badgeGroup) }}</div>
        </div>
    </div>

    <script src="https://unpkg.com/qr-code-styling@1.5.0/lib/qr-code-styling.js"></script>
    <script>
        const SVG_W    = 576,   SVG_H    = 864.16;
        const QR_X     = 306,   QR_Y     = 298.46, QR_SIZE = 156.49;
        const PADDING  = 4;
        const QR_SCALE = 1.3;

        // ── Text anchor points (SVG coords) ──────────────────────────────
        // NAME: centered at x=379.8, baseline y=184.31, font-size=15.65
        const NAME_CX = 379.8, NAME_BY = 184.31, NAME_FS = 15.65;
        // COMPANY: centered at x=379.8, baseline y=193.5, font-size=8
        const CO_CX   = 379.8, CO_BY   = 193.5,  CO_FS   = 8;
        // GROUP STRIP: x=267.89, y=195.91, w=223.86, h=38.8
        const STRIP_X = 267.89, STRIP_Y = 195.91, STRIP_W = 223.86, STRIP_H = 38.8;
        // GROUP TEXT: centered on strip, font-size=21.44
        const GRP_CX  = STRIP_X + STRIP_W / 2,
              GRP_CY  = STRIP_Y + STRIP_H / 2,
              GRP_FS  = 21.44;

        let qrCode = null;

        function positionText(el, cx, cy, fontSize, scaleX, scaleY) {
            el.style.left     = Math.round(cx * scaleX) + 'px';
            el.style.top      = Math.round(cy * scaleY) + 'px';
            el.style.fontSize = (fontSize * scaleX).toFixed(2) + 'px';
        }

        function renderQr(badge) {
            const bW     = badge.offsetWidth;
            const bH     = badge.offsetHeight;
            const scaleX = bW / SVG_W;
            const scaleY = bH / SVG_H;

            // Base size from SVG coords
            const baseSize = QR_SIZE * scaleX;

            // QR_SCALE grows/shrinks the whole box proportionally
            const size = Math.round(baseSize * QR_SCALE);

            // Center of the original SVG placeholder area (in display pixels)
            const centerX = (QR_X + QR_SIZE / 2) * scaleX;
            const centerY = (QR_Y + QR_SIZE / 2) * scaleY;

            // Re-center after scaling so QR stays in the right spot
            const left = Math.round(centerX - size / 2);
            const top  = Math.round(centerY - size / 2);

            const canvas = document.getElementById('qr-canvas');

            canvas.style.left       = left + 'px';
            canvas.style.top        = top  + 'px';
            canvas.style.width      = size + 'px';
            canvas.style.height     = size + 'px';
            canvas.style.padding      = PADDING + 'px';
            canvas.style.background   = '#ffffff';
            canvas.style.borderRadius = '6px';

            // QR render fills the box minus padding
            canvas.innerHTML = '';
            const qrRenderSize = Math.max(1, size - PADDING * 2);
            qrCode = new QRCodeStyling({
                width:  qrRenderSize,
                height: qrRenderSize,
                type:   "svg",
                data:   "SAMS|{{ $memberId }}",
                dotsOptions:          { color: "#000000", type: "rounded" },
                cornersSquareOptions: { color: "#000000", type: "extra-rounded" },
                cornersDotOptions:    { color: "#000000" },
                backgroundOptions:    { color: "#ffffff" },
                qrOptions:            { margin: 0 },
            });
            qrCode.append(canvas);

            // ── Group colour strip ────────────────────────────────────────
            const strip = document.getElementById('badge-strip');
            strip.style.left            = Math.round(STRIP_X * scaleX) + 'px';
            strip.style.top             = Math.round(STRIP_Y * scaleY) + 'px';
            strip.style.width           = Math.round(STRIP_W * scaleX) + 'px';
            strip.style.height          = Math.round(STRIP_H * scaleY) + 'px';
            strip.style.backgroundColor = '{{ $badgeGroupColor }}';

            // ── Text overlays ─────────────────────────────────────────────
            // NAME — baseline positioning: top = baseline_y - font_size
            positionText(
                document.getElementById('badge-name'),
                NAME_CX,
                NAME_BY - NAME_FS,   // baseline → top of element
                NAME_FS, scaleX, scaleY
            );

            // COMPANY — baseline positioning
            positionText(
                document.getElementById('badge-company'),
                CO_CX,
                CO_BY - CO_FS,
                CO_FS, scaleX, scaleY
            );

            // GROUP — centered on strip (uses translate(-50%,-50%) in CSS)
            positionText(
                document.getElementById('badge-group'),
                GRP_CX,
                GRP_CY,
                GRP_FS, scaleX, scaleY
            );
        }

        window.addEventListener('load', function () {
            const badge = document.querySelector('.badge-wrap');

            // Initial render
            renderQr(badge);

            // Re-render on every resize
            new ResizeObserver(() => renderQr(badge)).observe(badge);
        });
    </script>

</body>
</html>
