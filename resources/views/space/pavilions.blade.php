<x-app-layout>
    <title>{{ config('app.name') }} | Pavilions</title>
    <link rel="stylesheet" href="{{ asset('assets/css/pavilion-editor.css') }}">
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
           @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('pavilions.index') }}"><i class=" icon-home9 position-left"></i>PAVILIONS</a></li>
                <li class="active" aria-current="page">Registered spaces</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->
    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">
            <div class="panel-body">
                <div class="pavilion-heading">
                    <h1>Registered spaces</h1>
                    <a href="{{ route('pavilions.create') }}" class="btn btn-primary">
                        <i class="icon-plus2" aria-hidden="true"></i> Register space
                    </a>
                </div>

                <div class="form-group">
                    <label for="search-select">Find a space</label>
                    <select id="search-select" class="form-control">
                        <option value=""></option>
                    </select>
                </div>

                <div class="table-responsive" tabindex="0" role="region" aria-label="Registered spaces">
                    <table data-table-paginated class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Space name</th>
                                <th scope="col">Pavilion code</th>
                                <th scope="col">Category</th>
                                <th scope="col" class="text-right">Area (m²)</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pavilions as $item)
                                <tr>
                                    <td class="text-bold">{{ $item->pavilionName }}</td>
                                    <td>{{ $item->pavilionCode ?? '—' }}</td>
                                    <td>{{ $item->category_name }}</td>
                                    <td class="text-right">{{ number_format($item->pavilion_size, 2) }}</td>
                                    <td>{{ $item->space_status === 'UNRESERVED' ? 'Open for booking' : ucfirst(strtolower($item->space_status)) }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Actions <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('pavilions.edit', $item->pavilionID) }}">View space</a></li>
                                                <li><a href="{{ route('exhibition_space_booking.edit', $item->pavilionID) }}">View floor plan</a></li>
                                                <li><a href="{{ route('space-eligibility.edit', ['pavilions', $item->pavilionID]) }}">Set booking eligibility</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">No spaces have been registered.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="text-right">
                    {{ $pavilions->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(function () {
        const editUrl = @json(route('pavilions.edit', ['pavilion' => '__PAVILION_ID__']));
        $('#search-select').select2({
            width: '100%',
            ajax: {
                url: @json(route('pavilions.create')),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {q: params.term};
                },
                processResults: function (data) {
                    const pavilions = Array.isArray(data) ? data : [];
                    return {results: pavilions.map(pavilion => ({
                        id: editUrl.replace('__PAVILION_ID__', encodeURIComponent(pavilion.pavilionID)),
                        text: [pavilion.pavilionName, pavilion.pavilionCode, pavilion.category_name].filter(Boolean).join(' · '),
                    }))};
                },
                cache: true,
            },
            placeholder: 'Search by space name, code, or category',
            minimumInputLength: 1,
        }).on('select2:select', function () {
            window.location.assign(this.value);
        });
    });
</script>
