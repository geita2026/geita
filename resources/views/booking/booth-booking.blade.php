<x-app-layout>
    <title>{{ config('app.name') }} | {{ $pavilion->pavilionName }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/exhibition-booking.css') }}">
    <script src="{{ asset('assets/js/core/exhibition-booking.js') }}" defer></script>
    @include('booking.navigation', [
        'bookingCategory' => ['id' => $pavilion->pavilionCategory, 'name' => $pavilion->category_name ?? 'Spaces'],
        'bookingPavilion' => $pavilion->pavilionName,
    ])

    @php
        $hasBooths = $number_of_booths > 0;
        $selectedBooths = (array) old('spaceName', []);
        $canBook = $spaceAvailable && $companies->isNotEmpty();
        $canInteractWithMap = $hasBooths && $canBook;
    @endphp

    <div class="content">
        <div class="panel panel-white">
            <div class="panel-body exhibition-booking">
                <header class="booking-heading">
                    <div>
                        <p class="text-muted">{{ $event->event_name }}</p>
                        <h1>{{ $pavilion->pavilionName }}</h1>
                        <p>
                            {{ number_format($pavilion->pavilion_size, 2) }} m²
                            · {{ $hasBooths ? number_format($availableBoothCount) . ' booths available' : 'Whole space' }}
                        </p>
                    </div>
                    <a class="btn btn-default" href="{{ $pavilion->pavilionCategory ? route('event-booking-spaces.show', $pavilion->pavilionCategory) : route('booking.index') }}">Back to spaces</a>
                </header>

                @if ($pavilion->pavilionDescription)
                    <p>{{ $pavilion->pavilionDescription }}</p>
                @endif
                @if ($eligibleSectors->isNotEmpty())
                    <details class="booking-details">
                        <summary>Eligible pavilion sectors</summary>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead><tr><th scope="col">Sector</th></tr></thead>
                                <tbody>
                                    @foreach ($eligibleSectors as $sectorName)
                                        <tr><td>{{ $sectorName }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (!$spaceAvailable)
                    <p class="alert alert-info">This space is currently unavailable for booking.</p>
                @elseif ($companies->isEmpty())
                    <p class="alert alert-info">You need an active company linked to your account before you can book this space.</p>
                @endif

                <form id="exhibition-booking-form" action="{{ route('booking.store') }}" method="POST"
                    class="{{ $canBook ? 'booking-with-sticky-footer' : '' }}" data-has-booths="{{ $hasBooths ? 'true' : 'false' }}">
                    @csrf
                    <input type="hidden" name="space" value="booth">

                    @if ($canBook)
                        <div class="form-group booking-company">
                            <label for="booking-company">Company or institution <span class="text-danger" aria-hidden="true">*</span></label>
                            <select id="booking-company" name="company_name" class="form-control" required>
                                <option value="">Select a company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->companyID }}"
                                        {{ old('company_name', $companies->count() === 1 ? $company->companyID : '') == $company->companyID ? 'selected' : '' }}>
                                        {{ $company->companyName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <section class="pavilion-blueprint-section" aria-label="Pavilion floor plan">
                        @include('space.FloorPlans.PavilionMap', [
                            'interactiveBookingMap' => $canInteractWithMap,
                            'selectedBooths' => $selectedBooths,
                            'showMapBookingLinks' => true,
                        ])
                    </section>

                    @if (!$hasBooths && $canBook)
                        <input type="hidden" name="spaceName[]" value="{{ $pavilion->pavilionID }}">
                        @if ($pavilion->cost_one_msq !== null && $pavilion->type_of_currency)
                            <p><strong>{{ $pavilion->type_of_currency }} {{ number_format($pavilion->cost_one_msq, 2) }}</strong> / m²</p>
                        @endif
                    @endif

                    @if ($canBook)
                        <div class="booking-footer">
                            <div aria-live="polite">
                                <div id="booking-selection">{{ $hasBooths ? 'Select at least one available booth.' : 'Whole space selected.' }}</div>
                                @if ($hasBooths)
                                    <div id="booking-estimate" hidden></div>
                                @endif
                            </div>
                            <div class="booking-actions">
                                @if ($hasBooths)
                                    <button type="button" id="booking-clear" class="btn btn-default" disabled>Clear selection</button>
                                @endif
                                <button type="submit" id="booking-submit" class="btn btn-primary">Add to booking cart</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
