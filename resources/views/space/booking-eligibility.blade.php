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
                                @if ($mode === 'legacy')
                                    <option value="" {{ !old('mode') ? 'selected' : '' }}>Choose a rule to replace the current entity and sector restrictions</option>
                                @endif
                                @if ($isBooth)
                                    <option value="inherit" {{ old('mode', $mode) === 'inherit' ? 'selected' : '' }}>Use pavilion eligibility</option>
                                @endif
                                <option value="any" {{ old('mode', $mode) === 'any' ? 'selected' : '' }}>Any entity</option>
                                <option value="company" {{ old('mode', $mode) === 'company' ? 'selected' : '' }}>Specific entity or customer</option>
                                <option value="sectors" {{ old('mode', $mode) === 'sectors' ? 'selected' : '' }}>Selected exhibitor sectors</option>
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
                        <div id="eligibility-sectors-field">
                            <div class="pavilion-sectors-heading">
                                <label for="eligibility-sectors">Eligible exhibitor sectors</label>
                                <label for="eligibility-sectors-all">
                                    <input type="checkbox" id="eligibility-sectors-all" aria-controls="eligibility-sectors">
                                    Select all
                                </label>
                            </div>
                            <select name="sector_ids[]" id="eligibility-sectors" class="form-control" multiple>
                                @foreach ($sectors as $sector)
                                    <option value="{{ $sector->sectorID }}"
                                        {{ in_array($sector->sectorID, (array) old('sector_ids', $sectorIds), true) ? 'selected' : '' }}>
                                        {{ $sector->sectorName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

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
            const sectorSelect = document.getElementById('eligibility-sectors');
            const selectAll = document.getElementById('eligibility-sectors-all');
            const sectorOptions = Array.from(sectorSelect.options);
            const hasSelect2 = window.jQuery && jQuery.fn.select2;

            function syncSelectAll() {
                const selected = sectorSelect.selectedOptions.length;
                selectAll.checked = sectorOptions.length > 0 && selected === sectorOptions.length;
                selectAll.indeterminate = selected > 0 && selected < sectorOptions.length;
            }

            function showFields() {
                company.hidden = mode.value !== 'company' && mode.value !== '';
                sectors.hidden = mode.value !== 'sectors' && mode.value !== '';
                company.querySelector('select').disabled = company.hidden;
                sectors.querySelector('select').disabled = sectors.hidden;
            }
            mode.addEventListener('change', showFields);
            showFields();

            selectAll.disabled = sectorOptions.length === 0;
            selectAll.addEventListener('change', function () {
                sectorOptions.forEach(option => { option.selected = selectAll.checked; });
                sectorSelect.dispatchEvent(new Event('change', {bubbles: true}));
            });
            sectorSelect.addEventListener('change', syncSelectAll);
            syncSelectAll();

            if (!hasSelect2) return;
            jQuery(sectorSelect).select2({width: '100%', placeholder: 'Select sectors'}).on('change', syncSelectAll);
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
