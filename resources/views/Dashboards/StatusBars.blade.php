<h2 id="{{ $titleId }}">{{ $title }}</h2>
<p class="dashboard-muted">{{ $description }}</p>
<ul class="dashboard-bars">
    @foreach ($bars as $bar)
        <li>
            <div class="dashboard-bar-label">
                <span>{{ $bar['label'] }}</span>
                <strong>{{ number_format($bar['value']) }}</strong>
            </div>
            <div class="dashboard-track" aria-hidden="true">
                <span class="dashboard-fill" style="--dashboard-bar-size: {{ $maximum > 0 ? round($bar['value'] / $maximum * 100, 2) : 0 }}%"></span>
            </div>
        </li>
    @endforeach
</ul>
