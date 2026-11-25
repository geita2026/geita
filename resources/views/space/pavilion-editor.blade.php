@php
    $editing = $pavilion !== null;
    $title = $editing ? 'Edit pavilion' : 'Register pavilion';
    $formAction = $editing ? route('pavilions.update', $pavilion->pavilionID) : route('pavilions.store');
    $status = old('space_status', $pavilion->space_status ?? '');
    $statuses = ['UNRESERVED' => 'Open for booking', 'RESERVED' => 'Reserved'];
    if ($status && !isset($statuses[$status])) {
        $statuses[$status] = $status === 'SUNSPENDED' ? 'Suspended' : $status;
    }
    $selectedParticipants = old('participants_present')
        ? old('participants', [])
        : ($selectedSectors ?? collect())->pluck('participants')->all();
    $company = old('whoCanBook', $pavilion->whoCanBook ?? 'ANY') ?: 'ANY';
    $descriptionField = $editing ? 'pavilionDescription' : 'pavilion_description';
    $formattedArea = preg_replace_callback('/^\d+/', static fn ($match) =>
        preg_replace('/\B(?=(?:\d{3})+$)/', ',', $match[0]), (string) ($pavilion->pavilion_size ?? ''));
@endphp

<title>{{ config('app.name') }} | {{ $title }}</title>
<link rel="stylesheet" href="{{ asset('assets/css/pavilion-editor.css') }}">

<div class="page-header">
    <div class="page-header-content">
        @include('layouts.heading-elements')
    </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('pavilions.index') }}"><i class="icon-home9 position-left" aria-hidden="true"></i>Pavilions</a></li>
            <li class="active" aria-current="page">{{ $title }}</li>
        </ul>
        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>
</div>

<div class="content pavilion-editor">
    <div class="panel panel-white">
        <div class="panel-body">
            <header class="pavilion-heading">
                <div>
                    <h1>{{ $editing ? $pavilion->pavilionName : $title }}</h1>
                    @if ($editing)
                        <p>{{ $statuses[$pavilion->space_status] ?? ($pavilion->space_status === 'SUNSPENDED' ? 'Suspended' : $pavilion->space_status) }}
                            @if ($pavilion->category_name) · {{ $pavilion->category_name }} @endif
                        </p>
                    @endif
                </div>
                @if ($editing)
                    <a class="btn btn-default" href="{{ route('booths.show', $pavilion->pavilionID) }}">
                        <i class="icon-grid6" aria-hidden="true"></i> Manage booths
                    </a>
                @endif
            </header>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>Review the following fields before saving.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ $formAction }}" method="POST">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif
                <input type="hidden" name="participants_present" value="1">

                <section class="pavilion-section" aria-labelledby="pavilion-details">
                    <h2 id="pavilion-details">Space details</h2>
                    <div class="pavilion-fields">
                        <div>
                            <label for="pavilion-name">Space name <span class="text-danger" aria-hidden="true">*</span></label>
                            <input id="pavilion-name" class="form-control" name="pavilion_name" required
                                value="{{ old('pavilion_name', $pavilion->pavilionName ?? '') }}">
                        </div>
                        <div>
                            <label for="pavilion-category">Space category <span class="text-danger" aria-hidden="true">*</span></label>
                            <select id="pavilion-category" class="form-control" name="pavilion_category" required>
                                <option value="">Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}"
                                        {{ old('pavilion_category', $pavilion->pavilionCategory ?? '') == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="pavilion-size">Area (m²) <span class="text-danger" aria-hidden="true">*</span></label>
                            <input id="pavilion-size" class="form-control" name="pavilion_size" inputmode="decimal" required
                                data-pavilion-number value="{{ old('pavilion_size', $formattedArea) }}">
                        </div>
                        <div>
                            <label for="pavilion-partitions">Number of booths</label>
                            <input id="pavilion-partitions" class="form-control" name="numberOfPartitions" inputmode="numeric"
                                data-pavilion-number value="{{ old('numberOfPartitions', number_format($pavilion->numberOfPartitions ?? 0)) }}"
                                aria-describedby="pavilion-partitions-hint">
                            <small id="pavilion-partitions-hint">Use 0 when this space has no separate booths.</small>
                        </div>
                        <div class="pavilion-wide">
                            <label for="pavilion-description">Description</label>
                            <textarea id="pavilion-description" class="form-control" name="{{ $descriptionField }}" rows="3"
                                maxlength="2000">{{ old($descriptionField, $pavilion->pavilionDescription ?? '') }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="pavilion-section" aria-labelledby="pavilion-booking">
                    <h2 id="pavilion-booking">Booking settings</h2>
                    <div class="pavilion-fields">
                        <div>
                            <label for="pavilion-status">Booking status <span class="text-danger" aria-hidden="true">*</span></label>
                            <select id="pavilion-status" name="space_status" class="form-control" required>
                                <option value="">Select a booking status</option>
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ $status == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($editing)
                            <div>
                                <label for="pavilion-company">Who can book</label>
                                <select id="pavilion-company" name="whoCanBook" class="form-control">
                                    <option value="ANY" {{ $company === 'ANY' ? 'selected' : '' }}>Any eligible company</option>
                                    @if ($company !== 'ANY')
                                        <option value="{{ $company }}" selected>
                                            {{ $company === $pavilion->whoCanBook ? ($pavilion->companyName ?: $company) : $company }}
                                        </option>
                                    @endif
                                </select>
                                <small>Search by company name or registration ID to limit access.</small>
                            </div>
                        @endif
                        <div class="pavilion-wide">
                            <label for="pavilion-sectors">Eligible exhibitor sectors</label>
                            <select id="pavilion-sectors" name="participants[]" multiple class="form-control"
                                aria-describedby="pavilion-sectors-hint">
                                @foreach ($sectors as $sector)
                                    <option value="{{ $sector->sectorID }}"
                                        {{ in_array($sector->sectorID, $selectedParticipants) ? 'selected' : '' }}>
                                        {{ $sector->sectorName }}
                                    </option>
                                @endforeach
                            </select>
                            <small id="pavilion-sectors-hint">Select the sectors that can book this space.</small>
                        </div>
                    </div>
                </section>

                <div class="pavilion-actions">
                    <a href="{{ route('pavilions.index') }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ $editing ? 'Save changes' : 'Register pavilion' }}</button>
                </div>
            </form>

            @if ($editing && $pavilion->space_status !== 'SUNSPENDED')
                @can('DELETE_PAVILION', \Spatie\Permission\Models\Role::class)
                    <section class="pavilion-danger" aria-labelledby="pavilion-suspend-title">
                        <div>
                            <h2 id="pavilion-suspend-title">Suspend pavilion</h2>
                            <p>Suspending this space removes it from the active pavilion list.</p>
                        </div>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#suspend-pavilion">Suspend pavilion</button>
                    </section>
                    <div id="suspend-pavilion" class="modal fade" role="dialog" aria-modal="true" tabindex="-1" aria-labelledby="suspend-pavilion-title">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="modal-title" id="suspend-pavilion-title">Suspend {{ $pavilion->pavilionName }}?</h2>
                                </div>
                                <form action="{{ route('pavilions.destroy', $pavilion->pavilionID) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-body">This space will disappear from the active pavilion list.</div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Suspend pavilion</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endcan
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-pavilion-number]').forEach(function (field) {
            field.addEventListener('blur', function () {
                const value = field.value.trim();
                if (/^\d+(?:\.\d+)?$/.test(value)) {
                    const [whole, fraction] = value.split('.');
                    const grouped = whole.replace(/\B(?=(\d{3})+$)/g, ',');
                    field.value = fraction === undefined ? grouped : grouped + '.' + fraction;
                }
            });
        });

        if (!window.jQuery || !jQuery.fn.select2) return;

        jQuery('#pavilion-category').select2({width: '100%'});
        jQuery('#pavilion-sectors').select2({width: '100%', placeholder: 'Select sectors'});
        @if ($editing)
            jQuery('#pavilion-company').select2({
                width: '100%',
                ajax: {
                    url: @json(route('search_companies')),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {q: params.term || ''};
                    },
                    processResults: function (data) {
                        const companies = Array.isArray(data) ? data : [];
                        return {results: [
                            {id: 'ANY', text: 'Any eligible company'},
                            ...companies.map(company => ({
                                id: company.companyID,
                                text: [company.companyName, company.registrationID].filter(Boolean).join(' · '),
                            })),
                        ]};
                    },
                    cache: true,
                },
            });
        @endif
    });
</script>
