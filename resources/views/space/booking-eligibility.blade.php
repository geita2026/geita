<x-app-layout>
    <title>{{ config('app.name') }} | Booking eligibility</title>
    <link rel="stylesheet" href="{{ asset('assets/css/pavilion-editor.css') }}">

    <div class="content pavilion-editor">
        <div class="panel panel-white">
            <div class="panel-body">
                <header class="pavilion-heading">
                    <h1>Booking eligibility for {{ $isBooth ? $space->boothNo : $space->pavilionName }}</h1>
                </header>

                @if (session('success'))
                    <p class="alert alert-success" role="status">{{ session('success') }}</p>
                @endif
                @if ($errors->any())
                    <ul class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form action="{{ route('space-eligibility.update', [$type, $space->getKey()]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="pavilion-fields">
                        <div>
                            <label for="eligibility-mode">Who can book</label>
                            <select name="mode" id="eligibility-mode" class="form-control" required>
                                @if ($isBooth)
                                    <option value="inherit" {{ old('mode', $mode) === 'inherit' ? 'selected' : '' }}>Use pavilion eligibility</option>
                                @endif
                                <option value="any" {{ old('mode', $mode) === 'any' ? 'selected' : '' }}>Any entity</option>
                                <option value="company" {{ old('mode', $mode) === 'company' ? 'selected' : '' }}>Specific entity or customer</option>
                                <option value="sectors" {{ old('mode', $mode) === 'sectors' ? 'selected' : '' }}>Selected exhibitor sectors</option>
                                <option value="company_and_sectors" {{ old('mode', $mode) === 'company_and_sectors' ? 'selected' : '' }}>Specific customer and selected sectors</option>
                            </select>
                        </div>
                        <div id="eligibility-company-field">
                            <label for="eligibility-company">Entity or customer</label>
                            <select name="company_id" id="eligibility-company" class="form-control">
                                <option value=""></option>
                                @if ($company)
                                    <option value="{{ $company->companyID }}" selected>{{ $company->companyName }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <section id="eligibility-sectors-field" aria-labelledby="eligibility-sectors-title" class="mt-20">
                        <h2 id="eligibility-sectors-title" class="h4">Eligible exhibitor sectors</h2>
                        <p id="eligibility-combined-note" hidden>The selected customer must belong to one of these sectors.</p>
                        <div class="table-responsive eligibility-sector-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">
                                            <label class="mb-0">
                                                <input type="checkbox" id="eligibility-sectors-all">
                                                Select all
                                            </label>
                                        </th>
                                        <th scope="col">Sector</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $selectedSectorIds = (array) old('sector_ids', old('mode') === null ? $sectorIds : []);
                                    @endphp
                                    @forelse ($sectors->sortByDesc(fn ($sector) => in_array($sector->sectorID, $selectedSectorIds, true)) as $sector)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="sector_ids[]" id="sector-{{ $loop->index }}"
                                                    value="{{ $sector->sectorID }}"
                                                    {{ in_array($sector->sectorID, $selectedSectorIds, true) ? 'checked' : '' }}>
                                            </td>
                                            <th scope="row"><label class="mb-0" for="sector-{{ $loop->index }}">{{ $sector->sectorName }}</label></th>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2">No exhibitor sectors are registered.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <div class="pavilion-actions">
                        <a href="{{ $backUrl }}" class="btn btn-default">Back to {{ $isBooth ? 'booths' : 'pavilions' }}</a>
                        <button type="submit" class="btn btn-primary">Save eligibility</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mode = document.getElementById('eligibility-mode');
            const company = document.getElementById('eligibility-company-field');
            const sectors = document.getElementById('eligibility-sectors-field');
            const sectorOptions = Array.from(sectors.querySelectorAll('input[name="sector_ids[]"]'));
            const selectAll = document.getElementById('eligibility-sectors-all');
            const combinedNote = document.getElementById('eligibility-combined-note');

            function syncSelectAll() {
                const count = sectorOptions.filter(option => option.checked).length;
                selectAll.checked = count > 0 && count === sectorOptions.length;
                selectAll.indeterminate = count > 0 && count < sectorOptions.length;
            }

            function showFields() {
                const combined = mode.value === 'company_and_sectors';
                company.hidden = mode.value !== 'company' && !combined;
                sectors.hidden = mode.value !== 'sectors' && !combined;
                combinedNote.hidden = !combined;
                company.querySelector('select').disabled = company.hidden;
                sectorOptions.forEach(option => { option.disabled = sectors.hidden; });
                selectAll.disabled = sectors.hidden || sectorOptions.length === 0;
            }

            mode.addEventListener('change', showFields);
            showFields();
            selectAll.addEventListener('change', () => {
                sectorOptions.forEach(option => { option.checked = selectAll.checked; });
            });
            sectorOptions.forEach(option => option.addEventListener('change', syncSelectAll));
            syncSelectAll();

            if (!window.jQuery || !jQuery.fn.select2) return;
            jQuery('#eligibility-company').select2({
                width: '100%',
                placeholder: 'Search by name or registration ID',
                minimumInputLength: 1,
                ajax: {
                    url: @json(route('search_companies')),
                    dataType: 'json',
                    delay: 250,
                    data: params => ({q: params.term}),
                    processResults: data => ({
                        results: (Array.isArray(data) ? data : []).map(company => ({
                            id: company.companyID,
                            text: [company.companyName, company.registrationID].filter(Boolean).join(' · '),
                        })),
                    }),
                },
            });
        });
    </script>
</x-app-layout>
