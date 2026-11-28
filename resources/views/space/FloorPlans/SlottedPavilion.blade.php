@php
    $pavilionById = $mapPavilions->keyBy('pavilionID');
    $mapBooths = $mapPavilions->flatMap(fn ($member) => $member->mapBooths);
    $slotsPerIsland = $slotsPerIsland ?? 14;
    $slots = array_fill(0, $slotsPerIsland, null);
    $unplaced = [];

    foreach ($mapBooths as $booth) {
        $position = filter_var($booth->location, FILTER_VALIDATE_INT);
        if (!$position && preg_match('/^(?:' . preg_quote($boothPrefix, '/') . '-)?0*([1-9][0-9]*)$/iD', $booth->boothNo ?? '', $matches)) {
            $position = (int) $matches[1];
        }

        // Assign numbered booths first so missing records leave their positions open.
        if ($position >= 1 && $position <= count($slots) && $slots[$position - 1] === null) {
            $slots[$position - 1] = $booth;
        } else {
            $unplaced[] = $booth;
        }
    }

    // Fill remaining positions with booths that have no usable slot number.
    $next = 0;
    foreach ($unplaced as $booth) {
        while (isset($slots[$next])) {
            $next++;
        }
        $slots[$next++] = $booth;
    }

    $remainder = count($slots) % $slotsPerIsland;
    if ($remainder !== 0) {
        $slots = array_pad($slots, count($slots) + $slotsPerIsland - $remainder, null);
    }
@endphp

@include('space.FloorPlans.PavilionBlueprint', [
    'booths' => $mapBooths,
    'blueprintTitle' => $blueprintTitle,
    'blueprintClass' => $blueprintClass,
    'blueprintSlots' => $slots,
    'pavilionById' => $pavilionById,
])

@if ($mapPavilions->count() > 1 && !empty($showMapBookingLinks))
    <ul aria-label="Other pavilions on this floor plan">
        @foreach ($mapPavilions as $member)
            @if ($member->pavilionID !== $pavilion->pavilionID)
                <li><a href="{{ route('booking.show', $member->pavilionID) }}">View booking for {{ $member->pavilionName }}</a></li>
            @endif
        @endforeach
    </ul>
@endif
