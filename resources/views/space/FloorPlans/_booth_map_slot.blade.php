{{-- Sets $i (map slot index) from booths.location (exposed as boothLocation) or loop order. --}}
@php
    $i = ($loop->iteration ?? 1);
    $mapLocation = $stand->boothLocation ?? $stand->location ?? null;
    if ($mapLocation !== null && $mapLocation !== '' && is_numeric($mapLocation)) {
        $mapSlot = (int) $mapLocation;
        if ($mapSlot > 0) {
            $i = $mapSlot;
        }
    }
@endphp
