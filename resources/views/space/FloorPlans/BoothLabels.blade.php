@if ($boothName !== null && $boothName !== '')
    <span class="pavilion-booth-no">{{ $boothName }}</span>
@endif
@if ($boothArea !== null || $rateLabel !== null)
    <span class="pavilion-booth-meta">
        @if ($boothArea !== null)
            <span>{{ $boothArea }}</span>
        @endif
        @if ($rateLabel !== null)
            <span>{{ $rateLabel }}</span>
        @endif
    </span>
@endif
@if ($occupant)
    @if ($paired)
        <span class="sr-only">{{ $occupant }}</span>
    @else
        <span class="pavilion-booth-stamp{{ $stampLengthClass }}">{{ $occupant }}</span>
    @endif
@endif
