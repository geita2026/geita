{{--
    Architectural Pavilion Floor Plan — Geita CAD sheet style
    (matches the MARTIN SHIGELLA / ALEXANDER MNYETI / MOHAMED GOMBAT drawings)

    - Outer architectural sheet border + bold uppercase pavilion title
    - Top & bottom stepped corner bays + recessed 5-bay double-lined curtain wall
    - Perimeter walkway surrounding 2-row back-to-back dashed booth islands
    - Diagonal (-45deg) company names in black, red EMPTY for available booths
    - Optional two-way interactive booth selection synced with #exhibition-booking-form
--}}
@php
    $interactiveMap = !empty($interactiveBookingMap);
    $activeEventId = $event->event_id
        ?? session('liveEvent')
        ?? \App\Models\settings\events::where('event_status', 1)->value('event_id');

    $boothCollection = collect($booths ?? []);
    $spaceIdsToLookup = $boothCollection->pluck('boothID')
        ->push($pavilion->pavilionID ?? null)
        ->filter()
        ->unique()
        ->values();

    $bookedCompanyBySpace = collect();
    if ($activeEventId && $spaceIdsToLookup->isNotEmpty()) {
        $bookedCompanyBySpace = \App\Models\booking\booked_pavilions::query()
            ->leftJoin('companies', 'companies.companyID', '=', 'booked_pavilions.companyID')
            ->where('booked_pavilions.exhibition_year', $activeEventId)
            ->whereIn('booked_pavilions.pavilionID', $spaceIdsToLookup)
            ->get(['booked_pavilions.pavilionID', 'companies.companyName'])
            ->mapWithKeys(fn ($row) => [$row->pavilionID => $row->companyName ?: 'BOOKED']);
    }

    $heldLookup = isset($heldSpaceIds)
        ? collect($heldSpaceIds)
        : (isset($bookedBooths) ? collect($bookedBooths)->pluck('boothID')->flip() : collect());

    $pavilionHeld = $heldLookup->has($pavilion->pavilionID ?? '')
        || $bookedCompanyBySpace->has($pavilion->pavilionID ?? '');
    $pavilionCompany = $bookedCompanyBySpace->get($pavilion->pavilionID ?? '');

    $formatStampName = static function (?string $rawName): string {
        $clean = trim((string) $rawName);
        if ($clean === '') {
            return 'BOOKED';
        }
        $upper = mb_strtoupper($clean, 'UTF-8');
        if (mb_strlen($upper, 'UTF-8') <= 13) {
            return $upper;
        }
        $stripped = preg_replace(
            '/\b(COMPANY|LIMITED|LTD\.?|CO\.?|CORPORATION|CORP\.?|INC\.?|PLC|GROUP|ENTERPRISES|INVESTMENTS?|TANZANIA|\(T\))\b/u',
            '',
            $upper
        );
        $stripped = trim(preg_replace('/[\s,\-\.]+/u', ' ', (string) $stripped));
        if ($stripped === '') {
            $stripped = $upper;
        }
        $words = explode(' ', $stripped);
        $short = $words[0];
        if (isset($words[1]) && mb_strlen($short . ' ' . $words[1], 'UTF-8') <= 14) {
            $short .= ' ' . $words[1];
        }
        return mb_strlen($short, 'UTF-8') > 15 ? mb_substr($short, 0, 14, 'UTF-8') . '…' : $short;
    };

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

    // Wide enough to keep cells square-ish, never wider than it has to be.
    $hallMinWidth = max(720, $columnsPerIsland * 118 + 190);
@endphp

<div class="pavilion-blueprint-scroll" role="region" aria-label="{{ $pavilion->pavilionName }} architectural floor plan" tabindex="0">
    <div class="pavilion-blueprint-sheet" style="min-width: {{ $hallMinWidth }}px;">
        <h2 class="pavilion-blueprint-title">{{ mb_strtoupper($pavilion->pavilionName ?? 'PAVILION FLOOR PLAN', 'UTF-8') }}</h2>

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
                @if ($totalBoothItems > 0)
                    @foreach ($boothIslands as $island)
                        <div class="pavilion-booth-island{{ $isOpenPlots ? ' is-open-plots' : '' }}"
                             style="--pavilion-cols: {{ max(1, (int) ceil($island->count() / $islandRows)) }}; --pavilion-rows: {{ $islandRows }};">
                            @foreach ($island as $booth)
                                @php
                                    $isReservedStatus = ($booth->bookingStatus ?? '') === 'RESERVED';
                                    $bookedCompanyFull = $bookedCompanyBySpace->get($booth->boothID) ?? $pavilionCompany;
                                    $isBooked = $heldLookup->has($booth->boothID) || $pavilionHeld || $bookedCompanyFull !== null;
                                    $isAvailable = !$isReservedStatus && !$isBooked && ($pavilion->space_status ?? 'UNRESERVED') === 'UNRESERVED';
                                    $isSelected = $isAvailable && isset($selectedSet[$booth->boothID]);

                                    if ($bookedCompanyFull) {
                                        $stampText = $formatStampName($bookedCompanyFull);
                                        $stateClass = 'is-occupied';
                                        $tooltip = $booth->boothNo . ' — ' . $bookedCompanyFull
                                            . ' (' . number_format((float) ($booth->boothSize ?? 0), 2) . ' m²)';
                                    } elseif ($isReservedStatus || !$isAvailable) {
                                        $stampText = 'RESERVED';
                                        $stateClass = 'is-occupied';
                                        $tooltip = $booth->boothNo . ' — Reserved';
                                    } elseif ($isSelected) {
                                        $stampText = 'SELECTED';
                                        $stateClass = 'is-empty is-selected';
                                        $tooltip = $booth->boothNo . ' — Selected (' . number_format((float) ($booth->boothSize ?? 0), 2) . ' m²)';
                                    } else {
                                        $stampText = 'EMPTY';
                                        $stateClass = 'is-empty';
                                        $rateLabel = ($booth->category_cost ?? null) !== null
                                            ? ' · ' . ($booth->type_of_currency ?? '') . ' ' . number_format($booth->category_cost, 2) . '/m²'
                                            : '';
                                        $tooltip = $booth->boothNo . ' — Available (' . number_format((float) ($booth->boothSize ?? 0), 2) . ' m²' . $rateLabel . ')';
                                    }

                                    // Longer names step down in size so they never clip the dashed cell edge.
                                    $stampChars = mb_strlen($stampText, 'UTF-8');
                                    $stampLengthClass = $stampChars > 11 ? ' is-xlong' : ($stampChars > 6 ? ' is-long' : '');
                                @endphp

                                @if ($interactiveMap && $isAvailable)
                                    <button
                                        type="button"
                                        class="pavilion-booth-cell {{ $stateClass }}"
                                        data-blueprint-booth="{{ $booth->boothID }}"
                                        data-booth-no="{{ $booth->boothNo }}"
                                        aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                        title="{{ $tooltip }}">
                                        <span class="pavilion-booth-no">{{ $booth->boothNo }}</span>
                                        <span class="pavilion-booth-stamp{{ $stampLengthClass }}" data-default-empty="EMPTY">{{ $stampText }}</span>
                                    </button>
                                @else
                                    <div
                                        class="pavilion-booth-cell {{ $stateClass }}"
                                        title="{{ $tooltip }}">
                                        <span class="pavilion-booth-no">{{ $booth->boothNo }}</span>
                                        <span class="pavilion-booth-stamp{{ $stampLengthClass }}">{{ $stampText }}</span>
                                        @if ($isAvailable)
                                        @endif
                                    </div>
                                @endif
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
                        $wholeLabel = $pavilionCompany
                            ? $formatStampName($pavilionCompany)
                            : ($wholeAvailable ? 'EMPTY' : 'RESERVED');
                    @endphp
                    <div class="pavilion-booth-island is-whole-space">
                        <div class="pavilion-booth-cell {{ $wholeAvailable ? 'is-empty' : 'is-occupied' }}"
                             title="{{ $pavilion->pavilionName }} — {{ $wholeAvailable ? 'Available' : 'Occupied' }} ({{ number_format((float) ($pavilion->pavilion_size ?? 0), 2) }} m²)">
                            <span class="pavilion-booth-no">{{ number_format((float) ($pavilion->pavilion_size ?? 0), 0) }} m²</span>
                            <span class="pavilion-booth-stamp{{ mb_strlen($wholeLabel, 'UTF-8') > 11 ? ' is-xlong' : (mb_strlen($wholeLabel, 'UTF-8') > 6 ? ' is-long' : '') }}">{{ $wholeLabel }}</span>
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
    <span><i class="legend-swatch is-empty" aria-hidden="true"></i> EMPTY (Available{{ $interactiveMap ? ' — click to select' : '' }})</span>
    @if ($interactiveMap)
        <span><i class="legend-swatch is-selected" aria-hidden="true"></i> Selected for booking</span>
    @endif
</div>
