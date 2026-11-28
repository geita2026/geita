<!doctype html>
<html lang="en">
<head>
    <title>Discover exhibitors | {{ config('app.name') }}</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
</head>
<body>
    @include('FrontEnd.Public_Pages.Layouts.PageLoader')
    <header class="header">
        @include('FrontEnd.Public_Pages.Layouts.TopHeader')
        @include('FrontEnd.Public_Pages.Layouts.Header')
    </header>

    <main class="container py-5">
        <h1>Discover exhibitors</h1>
        @if ($event)
            <p>{{ $event->event_name }}</p>

            <form method="GET" action="{{ route('discover_exhibitor.index') }}" class="row g-3 align-items-end my-3">
                <div class="col-md-5">
                    <label class="form-label" for="exhibitor-search">Exhibitor name</label>
                    <input type="search" id="exhibitor-search" name="q" class="form-control" value="{{ $search }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="exhibitor-sector">Sector</label>
                    <select id="exhibitor-sector" name="sectorId" class="form-select">
                        <option value="">All sectors</option>
                        @foreach ($sectors as $sector)
                            <option value="{{ $sector->sectorID }}" @selected($sectorId === $sector->sectorID)>{{ $sector->sectorName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-auto">
                    <button class="btn btn-primary" type="submit">Search</button>
                    @if ($search !== '' || $sectorId !== '')
                        <a class="btn btn-outline-secondary" href="{{ route('discover_exhibitor.index') }}">Clear filters</a>
                    @endif
                </div>
            </form>

            <ul class="list-group mb-4">
                @forelse ($exhibitors as $exhibitor)
                    <li class="list-group-item p-0">
                        <a class="d-flex align-items-center justify-content-between gap-3 p-3 text-reset"
                           href="{{ route('discover_exhibitor.show', $exhibitor->companyID) }}">
                            <span>
                                <strong>{{ $exhibitor->companyName }}</strong>
                                @if ($exhibitor->sectorName)
                                    <span class="d-block text-muted">{{ $exhibitor->sectorName }}</span>
                                @endif
                            </span>
                            <i class="fa fa-chevron-right" aria-hidden="true"></i>
                        </a>
                    </li>
                @empty
                    <li class="list-group-item">No exhibitors found.</li>
                @endforelse
            </ul>

            {{ $exhibitors->links() }}
        @else
            <p>There is no active exhibition.</p>
        @endif
    </main>

    @include('FrontEnd.Public_Pages.Layouts.Footer')
</body>
</html>
