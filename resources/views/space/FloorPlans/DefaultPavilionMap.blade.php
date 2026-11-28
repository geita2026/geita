@if ($mapPavilions->isEmpty())
    @include('space.FloorPlans.PavilionBlueprint', ['usePlannedBoothCount' => true])
@else
    <section class="pavilion-shared-map" aria-label="Pavilion map {{ $pavilion->pavilionCode }}">
        <h2>Pavilion map {{ $pavilion->pavilionCode }}</h2>
        @foreach ($mapPavilions as $mapPavilion)
            @php($currentPavilion = $mapPavilion->pavilionID === $pavilion->pavilionID)
            @include('space.FloorPlans.PavilionBlueprint', [
                'pavilion' => $mapPavilion,
                'booths' => $mapPavilion->mapBooths,
                'usePlannedBoothCount' => true,
                'interactiveBookingMap' => $currentPavilion && $interactiveBookingMap,
                'selectedBooths' => $currentPavilion ? ($selectedBooths ?? []) : [],
            ])
            @if (!$currentPavilion && !empty($showMapBookingLinks))
                <p><a href="{{ route('booking.show', $mapPavilion->pavilionID) }}">View booking for {{ $mapPavilion->pavilionName }}</a></p>
            @endif
        @endforeach
    </section>
@endif
