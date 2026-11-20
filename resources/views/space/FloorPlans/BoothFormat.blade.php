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
    data-content="[ {{ $stand->category_name }} {{ number_format($stand->boothSize) }}M² , @1M² {{ number_format($stand->category_cost) }} {{ $stand->type_of_currency }} ]"
    class="booth"
    @php
        $bookedBoothIds = isset($bookedBoothIds)
            ? $bookedBoothIds
            : $bookedBooths->pluck('boothID')->all();
        $isBooked = in_array($stand->boothID, $bookedBoothIds, true)
            || $stand->bookingStatus == 'RESERVED';
    @endphp
    @if (! $isBooked && $stand->bookingStatus != 'RESERVED')
        style="background-color:RGB(0, 255, 0)"
    @else
        style="background-color:RGB(211, 211, 211);"
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
        <b>{{ $stand->boothNo }}</b>
    </label>
</a>
