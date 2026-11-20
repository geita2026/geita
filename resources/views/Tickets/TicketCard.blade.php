@php
    // Embed the receipt background so html2canvas captures it (no CORS).
    $bgB64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/receipt-template-online.png')));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DITF Ticket</title>
    <script src="https://unpkg.com/qr-code-styling@1.9.2/lib/qr-code-styling.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #ececec;
            font-family: Arial, Helvetica, sans-serif;
            padding: 24px 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 26px;
        }

        /* ── Ticket card (receipt PNG background + overlays) ────────────── */
        .ticket {
            position: relative;
            width: 380px;
            height: 583px;                 /* 380 × (1572/1024) */
            overflow: hidden;
            background: #fff;
        }
        /* Real <img> background — html2canvas draws this from the source image
           at full resolution (a CSS background-image gets softened on capture). */
        .ticket-bg {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%; z-index: 0;
        }

        .ov { position: absolute; z-index: 2; font-weight: bold; color: #111; line-height: 1.15; }
        .ov-type { color: #1a7a1a; }

        .qr-wrap {
            position: absolute; z-index: 2; left: 50%; transform: translateX(-50%);
            background: #fff; border-radius: 10px; padding: 7px;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
        }
        .qr-wrap canvas, .qr-wrap svg { width: 100% !important; height: 100% !important; display: block; }

        /* ── Buttons ───────────────────────────────────────────────────── */
        .btns { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; }
        .btn {
            padding: 10px 18px; border: none; border-radius: 8px;
            font-size: 14px; font-weight: bold; color: #fff; cursor: pointer;
        }
        .btn-pdf   { background: #c0392b; }
        .btn-png   { background: #2980b9; }
        .btn-print { background: #555; }
        .card-wrap { display: flex; flex-direction: column; align-items: center; gap: 12px; }

        @media print {
            body { background: #fff; padding: 0; gap: 0; }
            .btns, .no-print { display: none !important; }
            .ticket { page-break-after: always; }
        }
    </style>
</head>
<body>

@foreach ($tickets as $i => $item)
    <div class="card-wrap">
        <div class="ticket" id="ticket-{{ $i }}">
            <img class="ticket-bg" src="{{ $bgB64 }}">

            {{-- Ticket type + number + amount (top-right) --}}
            <div class="ov ov-type" style="top:31.5%; right:7%; font-size:15px; text-align:right;">
                {{ strtoupper($item->ticketName) }} <span style="color:#111"># {{ $item->ticketNo }}</span>
            </div>
            <div class="ov" style="top:35.5%; right:7%; font-size:13px; text-align:right;">
                {{ number_format($item->ticketAmount) }} TZS
            </div>

            {{-- Customer / Phone / Ticket date values (on the printed labels) --}}
            <div class="ov" style="top:32%;   left:34%; font-size:12.5px;">{{ $item->customerName }}</div>
            <div class="ov" style="top:37.6%; left:34%; font-size:12.5px;">{{ $item->phone_number }}</div>
            <div class="ov" style="top:43.2%;   left:35%; font-size:12.5px;">{{ date('d-m-Y', strtotime($item->ticketDate)) }}</div>

            {{-- Smooth QR (rendered to canvas by JS below) --}}
            <div class="qr-wrap" id="qr-{{ $i }}" style="top:65%; width:150px; height:150px;"></div>
        </div>

        <div class="btns no-print">
            <button class="btn btn-pdf"   onclick="dlTicket('ticket-{{ $i }}','pdf','{{ $item->ticketNo }}')">Download PDF</button>
            <button class="btn btn-png"   onclick="dlTicket('ticket-{{ $i }}','png','{{ $item->ticketNo }}')">Download PNG</button>
            <button class="btn btn-print" onclick="window.print()">Print</button>
        </div>
    </div>
@endforeach

<script>
    // ── Render the smooth QR (canvas, so html2canvas can capture it) ──────
    @foreach ($tickets as $i => $item)
    (function () {
        var qr = new QRCodeStyling({
            width: 450, height: 450, type: "canvas",
            data: "SAMS|{{ $item->ticketId }}",
            dotsOptions:          { color: "#000000", type: "rounded" },
            cornersSquareOptions: { color: "#000000", type: "extra-rounded" },
            cornersDotOptions:    { color: "#000000" },
            backgroundOptions:    { color: "#ffffff" },
            qrOptions:            { errorCorrectionLevel: "H", margin: 0 }
        });
        qr.append(document.getElementById("qr-{{ $i }}"));
    })();
    @endforeach

    // ── Download a single ticket card as PNG or PDF ───────────────────────
    function dlTicket(elId, kind, name) {
        var el = document.getElementById(elId);
        html2canvas(el, { scale: 3, backgroundColor: '#ffffff', useCORS: true, imageTimeout: 0 }).then(function (canvas) {
            if (kind === 'png') {
                var a = document.createElement('a');
                a.href = canvas.toDataURL('image/png');
                a.download = 'ticket_' + name + '.png';
                a.click();
            } else {
                var img = canvas.toDataURL('image/png');
                var pdf = new window.jspdf.jsPDF({ orientation: 'portrait', unit: 'pt', format: [298, 457] });
                pdf.addImage(img, 'PNG', 0, 0, 298, 457);
                pdf.save('ticket_' + name + '.pdf');
            }
        });
    }
</script>
</body>
</html>
