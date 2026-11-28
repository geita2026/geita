@php
    $editing = $pavilion !== null;
    $title = $editing ? 'Edit pavilion' : 'Register pavilion';
    $formAction = $editing ? route('pavilions.update', $pavilion->pavilionID) : route('pavilions.store');
    $status = old('space_status', $pavilion->space_status ?? '');
    $statuses = ['UNRESERVED' => 'Open for booking', 'RESERVED' => 'Reserved'];
    if ($status && !isset($statuses[$status])) {
        $statuses[$status] = $status === 'SUNSPENDED' ? 'Suspended' : $status;
    }
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

                <div class="pavilion-fields">
                    <div>
                        <label for="pavilion-name">Space name <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="pavilion-name" class="form-control" name="pavilion_name" required
                            value="{{ old('pavilion_name', $pavilion->pavilionName ?? '') }}">
                    </div>
                    <div>
                        <label for="pavilion-code">Pavilion code</label>
                        <input id="pavilion-code" class="form-control" name="pavilion_code" maxlength="64"
                            pattern="[A-Za-z0-9_\-]+" aria-describedby="pavilion-code-help"
                            value="{{ old('pavilion_code', $pavilion->pavilionCode ?? '') }}">
                        <small id="pavilion-code-help">Use the same code for pavilions that share a map. Use letters, numbers, underscores, or hyphens.</small>
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
                            data-pavilion-number value="{{ old('numberOfPartitions', number_format($pavilion->numberOfPartitions ?? 0)) }}">
                    </div>
                    <div>
                        <label for="pavilion-description">Description</label>
                        <textarea id="pavilion-description" class="form-control" name="{{ $descriptionField }}" rows="3"
                            maxlength="2000">{{ old($descriptionField, $pavilion->pavilionDescription ?? '') }}</textarea>
                    </div>
                    <div>
                        <label for="pavilion-status">Booking status <span class="text-danger" aria-hidden="true">*</span></label>
                        <select id="pavilion-status" name="space_status" class="form-control" required>
                            <option value="">Select a booking status</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" {{ $status == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

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

        if (window.jQuery && jQuery.fn.select2) {
            jQuery('#pavilion-category').select2({width: '100%'});
        }
    });
</script>
