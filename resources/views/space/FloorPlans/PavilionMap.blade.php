@php
    $code = $pavilion->pavilionCode;
    $customMap = is_string($code) && preg_match('/^[A-Z0-9_-]+$/D', $code)
        ? 'space.FloorPlans.' . $code
        : null;
@endphp

@if ($customMap && View::exists($customMap))
    @include($customMap)
@else
    @include('space.FloorPlans.DefaultPavilionMap')
@endif
