{{--
    Receipt-style ticket (dompdf). Full-page receipt background PNG with the
    dynamic values overlaid onto the printed labels, and the QR code in the
    open middle area just above "END OF TICKET".

    Page is sized to the PNG aspect ratio (1024x1572) via setPaper([0,0,298,457])
    in VipTicketSuppyController@show. To REVERT to the original design, swap the
    two PDF::loadView lines in that controller (the original is kept commented).

    All overlay positions are in $P below (points). Tweak them to fine-tune the
    alignment with the template — nothing else needs to change.
--}}
@php
    $W = 298; $H = 457; // page size (pt) = receipt PNG aspect ratio
    // Embed the PNG directly (no Image::make re-encode — that throws on large RGBA PNGs).
    $bgB64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/receipt-template-online.png')));

    // ===== Overlay positions (points). Adjust to align with the template. =====
    $P = [
        'type'  => ['top' => 150, 'left' => 100, 'w' => $W],  // type + number + amount
        'cust'  => ['top' => 146.5, 'left' => 100],             // "Online Ticket" → next to "Customer:"
        'phone' => ['top' => 172.5, 'left' => 100],             // phone → next to "Phone no:"
        'date'  => ['top' => 198.3, 'left' => 102],             // date → next to "Ticket date:"
        'stamp' => ['top' => 198, 'left' => 104, 'w' => 90],  // PAID / USED stamp
        'qr'    => ['top' => 290, 'left' => 0,   'w' => $W],  // QR just above END OF TICKET
    ];
@endphp

<style>
    @page { margin: 0; }
    html, body { margin: 0; padding: 0; }
</style>

@foreach ($tickets as $item)
    @php
        // Paid tickets already show a PAID stamp baked into the template image,
        // so NO overlay for them. Only used (0) / blocked (2) get an overlay.
        $stampB64 = null;
        if ($item->ticketStatus == 0) {
            $stampB64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/used.png')));
        } elseif ($item->ticketStatus == 2) {
            $stampB64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents(public_path('images/blocked.jpeg')));
        }
    @endphp

    <div style="position:relative; width:{{ $W }}pt; height:{{ $H }}pt; margin:0; font-family:Arial, Helvetica, sans-serif;">

        {{-- Receipt background --}}
        <img src="{{ $bgB64 }}" style="position:absolute; top:0; left:0; width:{{ $W }}pt; height:{{ $H }}pt;">

        {{-- Ticket type + number + amount --}}
        <div style="position:absolute; top:{{ $P['type']['top'] }}pt; left:{{ $P['type']['left'] }}pt; width:{{ $P['type']['w'] }}pt; text-align:center;">
            <span style="color:#1a7a1a; font-weight:bold; font-size:13px; text-transform:uppercase;">{{ $item->ticketName }}</span>
            <span style="font-weight:bold; font-size:13px;"># {{ $item->ticketNo }}</span><br>
            <span style="font-weight:bold; font-size:11px;">{{ number_format($item->ticketAmount) }} TZS
                @if ($item->plateNumber) &nbsp;&middot;&nbsp; {{ $item->plateNumber }} @endif
            </span>
        </div>

        {{-- Customer / Phone / Ticket date values (aligned to the printed labels) --}}
        <div style="position:absolute; top:{{ $P['cust']['top'] }}pt; left:{{ $P['cust']['left'] }}pt; font-size:11px; font-weight:bold;">{{ $item->customerName }}</div>
        <div style="position:absolute; top:{{ $P['phone']['top'] }}pt; left:{{ $P['phone']['left'] }}pt; font-size:11px; font-weight:bold;">{{ $item->phone_number }}</div>
        <div style="position:absolute; top:{{ $P['date']['top'] }}pt; left:{{ $P['date']['left'] }}pt; font-size:11px; font-weight:bold;">{{ date('d-m-Y', strtotime($item->ticketDate)) }}</div>

        {{-- USED / blocked stamp only (paid tickets use the template's baked-in PAID) --}}
        @if ($stampB64)
            <img src="{{ $stampB64 }}" style="position:absolute; top:{{ $P['stamp']['top'] }}pt; left:{{ $P['stamp']['left'] }}pt; width:{{ $P['stamp']['w'] }}pt; opacity:0.85;">
        @endif

        {{-- QR code just above END OF TICKET — badge-style rounded dots (imagick PNG) --}}
        <div style="position:absolute; top:{{ $P['qr']['top'] }}pt; left:{{ $P['qr']['left'] }}pt; width:{{ $P['qr']['w'] }}pt; text-align:center;">
            <img src="data:image/png;base64,{{ base64_encode(QrCode::format('png')->size(400)->margin(0)->style('dot')->eye('circle')->errorCorrection('H')->generate('SAMS|' . $item->ticketId)) }}"
                 style="width:100pt; height:100pt;">
        </div>
    </div>

    @if (!$loop->last)
        <div style="page-break-after: always;"></div>
    @endif
@endforeach
