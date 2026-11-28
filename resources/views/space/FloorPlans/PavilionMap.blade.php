@php
    $customMap = $pavilion->customFloorPlanView();
@endphp

@if ($customMap)
    @include($customMap)
@else
    @include('space.FloorPlans.DefaultPavilionMap')
@endif
