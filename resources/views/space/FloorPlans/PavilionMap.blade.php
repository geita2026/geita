@if ($pavilion->pavilionCode === 'MARTIN_SHIGELLA_PAVILLION')
    @include('space.FloorPlans.MARTIN_SHIGELLA_PAVILLION')
@elseif ($pavilion->pavilionCode === 'MOHAMED_GOMBAT_PAVILLION')
    @include('space.FloorPlans.MOHAMED_GOMBAT_PAVILLION')
@elseif ($pavilion->pavilionCode === 'ALEXANDER_MNYETI_PAVILLION')
    @include('space.FloorPlans.ALEXANDER_MNYETI_PAVILLION')
@elseif ($pavilion->pavilionCode === 'ROBERT_GABRIEL_PAVILLION')
    @include('space.FloorPlans.ROBERT_GABRIEL_PAVILLION')
@elseif ($pavilion->pavilionCode === 'ROSEMARY_SENYAMULE_PAVILLION')
    @include('space.FloorPlans.ROSEMARY_SENYAMULE_PAVILLION')
@elseif ($pavilion->pavilionCode === 'UWANJA_WA_MEZANI')
    @include('space.FloorPlans.UWANJA_WA_MEZANI')
@elseif ($mapPavilions->isEmpty())
    @include('space.FloorPlans.PavilionBlueprint')
@else
    <section class="pavilion-shared-map" aria-label="Pavilion map {{ $pavilion->pavilionCode }}">
        <h2>Pavilion map {{ $pavilion->pavilionCode }}</h2>
        @foreach ($mapPavilions as $mapPavilion)
            @php($currentPavilion = $mapPavilion->pavilionID === $pavilion->pavilionID)
            @include('space.FloorPlans.PavilionBlueprint', [
                'pavilion' => $mapPavilion,
                'booths' => $mapPavilion->mapBooths,
                'interactiveBookingMap' => $currentPavilion && $interactiveBookingMap,
                'selectedBooths' => $currentPavilion ? ($selectedBooths ?? []) : [],
            ])
            @if (!$currentPavilion && !empty($showMapBookingLinks))
                <p><a href="{{ route('booking.show', $mapPavilion->pavilionID) }}">View booking for {{ $mapPavilion->pavilionName }}</a></p>
            @endif
        @endforeach
    </section>
@endif
