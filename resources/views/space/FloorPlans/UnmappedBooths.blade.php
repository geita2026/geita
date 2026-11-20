@php
    $mapMaxSlot = (int) ($mapMaxSlot ?? 9999);
    $unmapped = $booths->filter(function ($b) use ($mapMaxSlot, $booths) {
        $loc = $b->boothLocation ?? $b->location ?? null;
        if ($loc !== null && $loc !== '' && is_numeric($loc)) {
            $slot = (int) $loc;

            return $slot < 1 || $slot > $mapMaxSlot;
        }
        $position = $booths->search(fn ($row) => $row->boothID === $b->boothID) + 1;

        return $position > $mapMaxSlot;
    });
@endphp

@if ($unmapped->count() > 0)
    <div class="alert alert-warning" style="margin-top:15px;text-align:left;">
        <strong>Additional booths</strong> — these could not be placed on the fixed map grid (assign map slot in booth <em>location</em> field, 1–{{ $mapMaxSlot }}, in admin):
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:8px;justify-content:center;padding:10px;">
        @foreach ($unmapped as $stand)
            @include('space.FloorPlans.BoothFormat')
        @endforeach
    </div>
@endif
