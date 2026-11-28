{{--
    Architectural Pavilion Floor Plan — Geita CAD sheet style
    (matches the MARTIN SHIGELLA / ALEXANDER MNYETI / MOHAMED GOMBAT drawings)

    - Outer architectural sheet border + bold uppercase pavilion title
    - Top & bottom stepped corner bays + recessed 5-bay double-lined curtain wall
    - Perimeter walkway surrounding 2-row back-to-back dashed booth islands
    - Booth identifiers, sizes, and booked exhibitor names from the database
    - Optional two-way interactive booth selection synced with #exhibition-booking-form
--}}
@php
    $interactiveMap = !empty($interactiveBookingMap);
    $activeEventId = $event->event_id
        ?? session('liveEvent')
        ?? \App\Models\settings\events::where('event_status', 1)->value('event_id');

    $boothCollection = collect($booths ?? []);
    if (!empty($usePlannedBoothCount)) {
        $slotCount = $pavilion->floorPlanBoothCount($boothCollection->count());
        $boothCollection = $slotCount === 0
            ? collect()
            : $boothCollection->values()->pad($slotCount, null);
    }
    $spaceIdsToLookup = $boothCollection->pluck('boothID')
        ->merge(isset($pavilionById) ? $pavilionById->keys() : [])
        ->push($pavilion->pavilionID ?? null)
        ->filter()
        ->unique()
        ->values();

    $bookingsBySpace = collect();
    if ($activeEventId && $spaceIdsToLookup->isNotEmpty()) {
        $bookingsBySpace = \App\Models\booking\booked_pavilions::query()
            ->leftJoin('companies', 'companies.companyID', '=', 'booked_pavilions.companyID')
            ->where('booked_pavilions.exhibition_year', $activeEventId)
            ->whereIn('booked_pavilions.pavilionID', $spaceIdsToLookup)
            ->get(['booked_pavilions.pavilionID', 'booked_pavilions.companyID', 'companies.companyName'])
            ->keyBy('pavilionID');
    }

    $heldLookup = isset($heldSpaceIds)
        ? collect($heldSpaceIds)
        : (isset($bookedBooths) ? collect($bookedBooths)->pluck('boothID')->flip() : collect());

    $pavilionHeld = $heldLookup->has($pavilion->pavilionID ?? '')
        || $bookingsBySpace->has($pavilion->pavilionID ?? '');
    if (!empty($usePlannedBoothCount) && $slotCount === 0 && !$pavilionHeld && $activeEventId) {
        $pavilionHeld = \App\Models\booking\booked_pavilions::where('exhibition_year', $activeEventId)
            ->whereIn('pavilionID', \App\Models\space\booths::where('pavilionID', $pavilion->pavilionID)->select('boothID'))
            ->exists();
    }
    $pavilionCompany = $bookingsBySpace->get($pavilion->pavilionID ?? '')?->companyName;
    $formatArea = static fn ($area) => is_numeric($area)
        ? rtrim(rtrim(number_format((float) $area, 4, '.', ''), '0'), '.') . ' m²'
        : null;

    $selectedSet = array_flip((array) ($selectedBooths ?? old('spaceName', [])));
    $totalBoothItems = $boothCollection->count();

    /*
     | A pavilion is drawn as 2-row back-to-back islands, column-paired, so that
     | consecutive booths [1,2], [3,4] … sit one above the other exactly as the
     | CAD sheets show. A hall wider than $maxIslandColumns is broken into
     | several islands separated by a cross walkway instead of being stretched
     | into one unreadable strip.
     */
    // Outdoor plots (Uwanja wa Mezani) are drawn as one row of numbered plots;
    // indoor pavilions as 2-row back-to-back islands.
    $categoryLabel = $pavilion->category_name
        ?? \App\Models\space\space_categories::where('category_id', $pavilion->pavilionCategory ?? '')->value('category_name')
        ?? '';
    $categoryLabel = mb_strtolower((string) $categoryLabel, 'UTF-8');
    $isOpenPlots = str_contains($categoryLabel, 'plot') || str_contains($categoryLabel, 'open space');
    $islandRows = $isOpenPlots ? 1 : 2;
    $maxIslandColumns = $isOpenPlots ? 13 : 8;

    $totalColumns = $totalBoothItems > 0 ? (int) ceil($totalBoothItems / $islandRows) : 0;
    $islandCount = $totalColumns > 0 ? (int) ceil($totalColumns / $maxIslandColumns) : 0;
    $columnsPerIsland = $islandCount > 0 ? (int) ceil($totalColumns / $islandCount) : 0;
    $boothIslands = $columnsPerIsland > 0
        ? $boothCollection->chunk($columnsPerIsland * $islandRows)
        : collect();

    if (isset($blueprintSlots)) {
        $isOpenPlots = !empty($reversePlotNumbers);
        $islandRows = $isOpenPlots ? 1 : 2;
        $columnsPerIsland = intdiv($slotsPerIsland, $islandRows);
        $boothIslands = collect($blueprintSlots)->chunk($slotsPerIsland);
        if ($isOpenPlots) {
            $boothIslands = $boothIslands->map(fn ($island) => $island->reverse());
        }
    }

    // Wide enough to keep cells square-ish, never wider than it has to be.
    $hallMinWidth = !empty($reversePlotNumbers) ? 1040 : max(720, $columnsPerIsland * 118 + 190);
    $blueprintTitle = $blueprintTitle ?? $pavilion->pavilionName ?? 'Pavilion floor plan';
@endphp

<div class="pavilion-blueprint-scroll" role="region" aria-label="{{ $blueprintTitle }} architectural floor plan" tabindex="0">
    <div class="pavilion-blueprint-sheet {{ $blueprintClass ?? '' }}" style="min-width: {{ $hallMinWidth }}px;">
        <h2 class="pavilion-blueprint-title">{{ mb_strtoupper($blueprintTitle, 'UTF-8') }}</h2>

        <div class="pavilion-blueprint-building">
            {{-- Top wall: stepped corner bays either side of a recessed 5-bay curtain --}}
            <div class="pavilion-wall pavilion-wall-top" aria-hidden="true">
                <svg class="pavilion-wall-bay" viewBox="0 0 190 64" preserveAspectRatio="none">
                    <rect x="2" y="3" width="186" height="46" fill="#ffffff" stroke="#1a1a1a" stroke-width="1.5" />
                    <line x1="2" y1="8" x2="188" y2="8" stroke="#1a1a1a" stroke-width="1" />
                    <line x1="7" y1="3" x2="7" y2="49" stroke="#1a1a1a" stroke-width="1" />
                    <line x1="143" y1="3" x2="143" y2="49" stroke="#1a1a1a" stroke-width="1.4" />
                    <line x1="146" y1="3" x2="146" y2="49" stroke="#1a1a1a" stroke-width="0.9" />
                    <line x1="0" y1="3" x2="190" y2="3" stroke="#1a1a1a" stroke-width="0.7" />
                    <line x1="0" y1="49" x2="190" y2="49" stroke="#1a1a1a" stroke-width="0.7" />
                </svg>

                <div class="pavilion-wall-curtain">
                    <span class="pavilion-wall-mullion" style="left: 20%"></span>
                    <span class="pavilion-wall-mullion" style="left: 40%"></span>
                    <span class="pavilion-wall-mullion" style="left: 60%"></span>
                    <span class="pavilion-wall-mullion" style="left: 80%"></span>
                </div>

                <svg class="pavilion-wall-bay" viewBox="0 0 190 64" preserveAspectRatio="none">
                    <rect x="2" y="3" width="186" height="46" fill="#ffffff" stroke="#1a1a1a" stroke-width="1.5" />
                    <line x1="2" y1="8" x2="188" y2="8" stroke="#1a1a1a" stroke-width="1" />
                    <line x1="183" y1="3" x2="183" y2="49" stroke="#1a1a1a" stroke-width="1" />
                    <line x1="47" y1="3" x2="47" y2="49" stroke="#1a1a1a" stroke-width="1.4" />
                    <line x1="44" y1="3" x2="44" y2="49" stroke="#1a1a1a" stroke-width="0.9" />
                    <line x1="0" y1="3" x2="190" y2="3" stroke="#1a1a1a" stroke-width="0.7" />
                    <line x1="0" y1="49" x2="190" y2="49" stroke="#1a1a1a" stroke-width="0.7" />
                </svg>
            </div>

            {{-- Interior hall floor & perimeter walkway --}}
            <div class="pavilion-hall-interior">
                @if ($boothIslands->isNotEmpty())
                    @foreach ($boothIslands as $island)
                        @php
                            $pairedLabels = [];
                            if (!empty($pairOccupantLabels)) {
                                foreach ($island->values()->chunk(2)->values() as $column => $pair) {
                                    [$top, $bottom] = array_pad($pair->values()->all(), 2, null);
                                    if (!$top || !$bottom) {
                                        continue;
                                    }
                                    $topBooking = $bookingsBySpace->get($top->boothID) ?? $bookingsBySpace->get($top->pavilionID);
                                    $bottomBooking = $bookingsBySpace->get($bottom->boothID) ?? $bookingsBySpace->get($bottom->pavilionID);
                                    if (!empty($topBooking->companyName) && $topBooking->companyID === $bottomBooking?->companyID) {
                                        $pairedLabels[$column] = trim($topBooking->companyName);
                                    }
                                }
                            }
                        @endphp
                        <div class="pavilion-booth-island{{ $isOpenPlots ? ' is-open-plots' : '' }}"
                             style="--pavilion-cols: {{ max(1, (int) ceil($island->count() / $islandRows)) }}; --pavilion-rows: {{ $islandRows }};">
                            @foreach ($island as $booth)
                                @if ($booth === null)
                                    <div class="pavilion-booth-cell is-unregistered" title="Booth details are not set">
                                        <span class="pavilion-booth-stamp">Empty</span>
                                    </div>
                                    @continue
                                @endif
                                @php
                                    $boothPavilion = isset($pavilionById)
                                        ? $pavilionById->get($booth->pavilionID, $pavilion)
                                        : $pavilion;
                                    $boothPavilionId = $boothPavilion->pavilionID;
                                    $canSelectBooth = $interactiveMap && $boothPavilionId === $pavilion->pavilionID;
                                    $isReservedStatus = ($booth->bookingStatus ?? '') === 'RESERVED';
                                    $booking = $bookingsBySpace->get($booth->boothID)
                                        ?? $bookingsBySpace->get($boothPavilionId);
                                    $isBooked = $heldLookup->has($booth->boothID) || $heldLookup->has($boothPavilionId) || $booking !== null;
                                    $isAvailable = !$isReservedStatus && !$isBooked && ($boothPavilion->space_status ?? 'UNRESERVED') === 'UNRESERVED';
                                    $isSelected = $canSelectBooth && $isAvailable && isset($selectedSet[$booth->boothID]);
                                    $stateClass = $isAvailable ? 'is-empty' : 'is-occupied';
                                    $stateClass .= $isSelected ? ' is-selected' : '';

                                    $boothName = $booth->boothNo;
                                    $boothArea = $formatArea($booth->boothSize);
                                    $rate = $booth->category_cost ?? null;
                                    $currency = trim($booth->type_of_currency ?? '');
                                    $rateLabel = is_numeric($rate)
                                        ? trim($currency . ' ' . number_format($rate, 2)) . ' / m²'
                                        : null;
                                    $estimatedCost = $boothArea !== null && $rateLabel !== null && $currency !== ''
                                        ? $booth->boothSize * $rate
                                        : null;
                                    $occupant = $booking?->companyName;
                                    $tooltip = implode(' · ', array_filter([$boothName, $boothArea, $rateLabel, $occupant], fn ($value) => $value !== null && $value !== ''));
                                    $stampChars = mb_strlen($occupant ?? '', 'UTF-8');
                                    $stampLengthClass = $stampChars > 11 ? ' is-xlong' : ($stampChars > 6 ? ' is-long' : '');
                                    $paired = isset($pairedLabels[intdiv($loop->index, 2)]);
                                @endphp

                                @if ($canSelectBooth && $isAvailable)
                                    <input type="checkbox" name="spaceName[]" value="{{ $booth->boothID }}"
                                           class="booking-booth-select" hidden @if ($isSelected) checked @endif>
                                    <button
                                        type="button"
                                        id="floor-plan-booth-{{ $booth->boothID }}"
                                        class="pavilion-booth-cell {{ $stateClass }}"
                                        data-blueprint-booth="{{ $booth->boothID }}"
                                        data-booth-no="{{ $booth->boothNo }}"
                                        @if ($estimatedCost !== null)
                                            data-estimated-cost="{{ $estimatedCost }}"
                                            data-currency="{{ $currency }}"
                                        @endif
                                        aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                        title="{{ $tooltip }}">
                                        @include('space.FloorPlans.BoothLabels')
                                    </button>
                                @else
                                    <div id="floor-plan-booth-{{ $booth->boothID }}"
                                         class="pavilion-booth-cell {{ $stateClass }}" title="{{ $tooltip }}">
                                        @include('space.FloorPlans.BoothLabels')
                                    </div>
                                @endif
                            @endforeach
                            @foreach ($pairedLabels as $column => $label)
                                <div class="pavilion-paired-label" style="grid-column: {{ $column + 1 }}" aria-hidden="true">
                                    <span class="pavilion-booth-stamp{{ mb_strlen($label, 'UTF-8') > 14 ? ' is-long' : '' }}">{{ $label }}</span>
                                </div>
                            @endforeach

                            {{-- Keep the island a clean rectangle when the last column is half empty --}}
                            @if ($islandRows === 2 && $island->count() > 1 && $island->count() % 2 === 1)
                                <div class="pavilion-booth-cell is-void" aria-hidden="true"></div>
                            @endif
                        </div>
                    @endforeach
                @else
                    {{-- Whole-pavilion space (no sub-booths) --}}
                    @php
                        $wholeAvailable = ($pavilion->space_status ?? 'UNRESERVED') === 'UNRESERVED' && !$pavilionHeld;
                        $pavilionArea = $formatArea($pavilion->pavilion_size);
                    @endphp
                    <div class="pavilion-booth-island is-whole-space">
                        <div class="pavilion-booth-cell {{ $wholeAvailable ? 'is-empty' : 'is-occupied' }}"
                             title="{{ implode(' · ', array_filter([$pavilion->pavilionName, $pavilionArea, $pavilionCompany])) }}">
                            @if ($pavilionArea !== null)
                                <span class="pavilion-booth-meta">{{ $pavilionArea }}</span>
                            @endif
                            @if ($pavilionCompany)
                                <span class="pavilion-booth-stamp">{{ $pavilionCompany }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Bottom wall: mirror of the top --}}
            <div class="pavilion-wall pavilion-wall-bottom" aria-hidden="true">
                <svg class="pavilion-wall-bay" viewBox="0 0 190 64" preserveAspectRatio="none">
                    <rect x="2" y="15" width="186" height="46" fill="#ffffff" stroke="#1a1a1a" stroke-width="1.5" />
                    <line x1="2" y1="56" x2="188" y2="56" stroke="#1a1a1a" stroke-width="1" />
                    <line x1="7" y1="15" x2="7" y2="61" stroke="#1a1a1a" stroke-width="1" />
                    <line x1="143" y1="15" x2="143" y2="61" stroke="#1a1a1a" stroke-width="1.4" />
                    <line x1="146" y1="15" x2="146" y2="61" stroke="#1a1a1a" stroke-width="0.9" />
                    <line x1="0" y1="15" x2="190" y2="15" stroke="#1a1a1a" stroke-width="0.7" />
                    <line x1="0" y1="61" x2="190" y2="61" stroke="#1a1a1a" stroke-width="0.7" />
                </svg>

                <div class="pavilion-wall-curtain">
                    <span class="pavilion-wall-mullion" style="left: 20%"></span>
                    <span class="pavilion-wall-mullion" style="left: 40%"></span>
                    <span class="pavilion-wall-mullion" style="left: 60%"></span>
                    <span class="pavilion-wall-mullion" style="left: 80%"></span>
                </div>

                <svg class="pavilion-wall-bay" viewBox="0 0 190 64" preserveAspectRatio="none">
                    <rect x="2" y="15" width="186" height="46" fill="#ffffff" stroke="#1a1a1a" stroke-width="1.5" />
                    <line x1="2" y1="56" x2="188" y2="56" stroke="#1a1a1a" stroke-width="1" />
                    <line x1="183" y1="15" x2="183" y2="61" stroke="#1a1a1a" stroke-width="1" />
                    <line x1="47" y1="15" x2="47" y2="61" stroke="#1a1a1a" stroke-width="1.4" />
                    <line x1="44" y1="15" x2="44" y2="61" stroke="#1a1a1a" stroke-width="0.9" />
                    <line x1="0" y1="15" x2="190" y2="15" stroke="#1a1a1a" stroke-width="0.7" />
                    <line x1="0" y1="61" x2="190" y2="61" stroke="#1a1a1a" stroke-width="0.7" />
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="pavilion-blueprint-legend" aria-label="Floor plan legend">
    <span><i class="legend-swatch is-occupied" aria-hidden="true"></i> Occupied / Reserved</span>
    <span><i class="legend-swatch is-empty" aria-hidden="true"></i> Available{{ $interactiveMap ? ' — click to select' : '' }}</span>
    @if ($interactiveMap)
        <span><i class="legend-swatch is-selected" aria-hidden="true"></i> Selected for booking</span>
    @endif
</div>
