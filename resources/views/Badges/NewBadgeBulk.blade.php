<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Exhibitor Badges</title>
    <style>
        /*
            Badge page geometry
            â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            Preview SVG coord system: 576 Ã— 864.16
            PDF page: 226.77pt Ã— 340.29pt (8cm Ã— 12cm).
            Scale factors:   sx = 226.77 / 576     = 0.39370
                             sy = 340.29 / 864.16  = 0.39378
            All overlay positions below come straight from the preview's
            JavaScript (NewBadge.blade.php) multiplied by those scales.
        */
        @page { margin: 0; size: 226.77pt 340.29pt; }
        html, body { margin: 0; padding: 0; }

        .badge-page {
            position: relative;
            width: 226.77pt;
            height: 340.29pt;
            page-break-after: always;
            overflow: hidden;
        }
        .badge-page:last-child { page-break-after: auto; }

        .bg {
            position: absolute;
            top: 0; left: 0;
            width: 226.77pt;
            height: 340.29pt;
        }

        /* â”€â”€ NAME (SVG anchor: cx=379.8, baseline y=184.31, fs=15.65) â”€â”€ */
        .name {
            position: absolute;
            left: 81.51pt;          /* cxÂ·sx âˆ’ 60% of page / 2 */
            top:  66.42pt;          /* (184.31 âˆ’ 15.65)Â·sy */
            width: 136pt;           /* 60% of 226.77pt */
            text-align: center;
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            font-size: 6.16pt;      /* 15.65Â·sx */
            color: #1b1b1b;
            letter-spacing: 0.05em;
        }

        /* â”€â”€ COMPANY (SVG anchor: cx=379.8, baseline y=193.5, fs=8) â”€â”€ */
        .company {
            position: absolute;
            left: 81.51pt;
            top:  73.01pt;          /* (193.5 âˆ’ 8)Â·sy */
            width: 136pt;
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 3.15pt;      /* 8Â·sx */
            color: #444444;
        }

        /* â”€â”€ GROUP COLOR STRIP (SVG: 267.89,195.91 Â· 223.86Ã—38.8) â”€â”€ */
        .strip {
            position: absolute;
            left:   105.47pt;       /* 267.89Â·sx */
            top:     77.14pt;       /* 195.91Â·sy */
            width:   88.13pt;       /* 223.86Â·sx */
            height:  15.28pt;       /* 38.8Â·sy */
            border-radius: 1.57pt;  /* preview's 4px Ã— sx */
        }

        /* â”€â”€ GROUP TEXT â€” positioned as SIBLING of strip, same box,
              vertically centered via line-height (reliable in DomPDF) â”€â”€ */
        .group {
            position: absolute;
            left:   105.47pt;
            top:     77.14pt;
            width:   88.13pt;
            height:  15.28pt;
            line-height: 15.28pt;   /* = height â†’ vertical center */
            text-align: center;
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            font-size: 8.44pt;      /* 21.44Â·sx */
            letter-spacing: 0.06em;
        }

        /* â”€â”€ QR â”€â”€ preview uses QR_X=306, QR_Y=298.46, size=156.49,
               then scales by QR_SCALE=1.3 and RE-CENTERS on that same anchor.
               scaled_size = 156.49Â·1.3 = 203.44
               center = (306 + 78.245,  298.46 + 78.245) = (384.245, 376.705)
               left   = (384.245 âˆ’ 101.72)Â·sx = 111.24pt
               top    = (376.705 âˆ’ 101.72)Â·sy = 108.26pt
               w = h  = 203.44Â·sx               =  80.09pt
               Preview wraps the QR in a white box: 4px pad, 6px radius.
               At print scale that's 1.57pt pad and 2.36pt radius.            */
        .qr {
            position: absolute;
            left:  111.24pt;
            top:   108.26pt;
            width:  80.09pt;
            height: 80.09pt;
            background: #ffffff;
            border-radius: 2.36pt;
            padding: 1.57pt;
            box-sizing: border-box;
        }
        .qr img {
            display: block;
            width:  100%;
            height: 100%;
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>
<body>
@foreach ($badges as $b)
    <div class="badge-page">
        <img class="bg" src="{{ $bgSrc }}" alt=""/>

        <div class="name">{{ $b['name'] }}</div>

        @if ($b['company'] !== '')
            <div class="company">{{ $b['company'] }}</div>
        @endif

        <div class="strip" style="background: {{ $b['stripColor'] }};"></div>
        <div class="group" style="color: {{ $b['textColor'] }};">{{ $b['group'] }}</div>

        <div class="qr">
            <img src="{{ $b['qrSrc'] }}" alt=""/>
        </div>
    </div>
@endforeach
</body>
</html>

