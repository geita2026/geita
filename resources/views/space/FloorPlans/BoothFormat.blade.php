@php
    $legacyBoothArea = is_numeric($stand->boothSize)
        ? rtrim(rtrim(number_format((float) $stand->boothSize, 4, '.', ''), '0'), '.') . ' m²'
        : null;
    $legacyBoothRate = is_numeric($stand->category_cost)
        ? trim(number_format($stand->category_cost, 2) . ' ' . $stand->type_of_currency) . '/m²'
        : null;
@endphp
@if (!empty($readOnlyBookingMap))
    @php
        $mapBoothUnavailable = $stand->bookingStatus === 'RESERVED'
            || $heldSpaceIds->has($stand->boothID) || $heldSpaceIds->has($pavilion->pavilionID);
    @endphp
    <span class="booking-map-booth {{ $mapBoothUnavailable ? '' : 'is-available' }}">
        {{ $stand->boothNo }} {{ $legacyBoothArea }}
    </span>
@else
<style>
    .booth{
        font-size: 8px;
        padding:0.5%;
        border: 1px solid black;
        color:black;
    }
    .booth:hover{
        color: black;
        cursor: pointer;
        background-color: white;
    }
    .mvl{
        margin-left:3%
    }
</style>
<a href="#"
    id="booth-{{ $stand->boothID }}"
    data-booth-id="{{ $stand->boothID }}"
    data-booth-no="{{ $stand->boothNo }}"
    data-popup="popover-custom"
    title="{{ $stand->pavilionName }} ({{ $stand->boothNo }}) — ID: {{ $stand->boothID }}"
    data-trigger="hover"
    data-placement="right"
    data-content="{{ implode(' · ', array_filter([$stand->category_name, $legacyBoothArea, $legacyBoothRate])) }}"
    class="booth"
    @php
        $bookedBoothIds = isset($bookedBoothIds)
            ? $bookedBoothIds
            : $bookedBooths->pluck('boothID')->all();
        $isBooked = in_array($stand->boothID, $bookedBoothIds, true)
            || $stand->bookingStatus == 'RESERVED';
    @endphp
    @if (! $isBooked && $stand->bookingStatus != 'RESERVED')
        style="background-color:#e2f3e6"
    @else
        style="background-color:#e3e8ef"
    @endif
    >

    <label class="radio-inline radio-right">
        <input
            name="spaceName[]"
            value="{{ $stand->boothID }}"
            type="checkbox"
            id="booth-input-{{ $stand->boothID }}"
            class="styled booth-select"
            data-booth-id="{{ $stand->boothID }}"
            @if ($isBooked || $stand->bookingStatus == 'RESERVED')
                checked disabled
            @endif
        >
        <b>{{ $stand->boothNo }}</b> {{ $legacyBoothArea }}
    </label>
</a>

@endif
