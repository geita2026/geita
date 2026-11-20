@if (strtoupper($pavilion->pavilionName) == 'KARUME HALL')
    @include('space.FloorPlans.KarumeHall')
@else
    <div class=" alert alert-danger">
        <b>No</b> Floor Plan registered under this Pavilion.
    </div>
@endif

