<section class="dashboard-panel" aria-labelledby="exhibition-stats-title">
    <h2 id="exhibition-stats-title">Exhibition overview</h2>
    <ul class="dashboard-overview">
        @foreach ($exhibitionStats as [$label, $value, $route])
            <li>
                <a href="{{ route($route) }}">{{ $label }}</a>
                <strong>{{ number_format($value) }}</strong>
            </li>
        @endforeach
    </ul>
</section>
