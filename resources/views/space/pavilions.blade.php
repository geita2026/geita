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
                                <th scope="col">Category</th>
                                <th scope="col">Street</th>
                                <th scope="col" class="text-right">Area (m²)</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pavilions as $item)
                                <tr>
                                    <td class="text-bold">{{ $item->pavilionName }}</td>
                                    <td>{{ $item->category_name }}</td>
                                    <td>{{ $item->streetName ?: '—' }}</td>
                                    <td class="text-right">{{ number_format($item->pavilion_size, 2) }}</td>
                                    <td>{{ $item->space_status === 'UNRESERVED' ? 'Open for booking' : ucfirst(strtolower($item->space_status)) }}</td>
                                    <td>
                                        <a href="{{ route('pavilions.edit', ['pavilion' => $item->pavilionID]) }}" class="btn btn-primary">
                                            View space <i class="icon-arrow-right13" aria-hidden="true"></i>
                                        </a>
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
                        text: [pavilion.pavilionName, pavilion.category_name].filter(Boolean).join(' · '),
                    }))};
                },
                cache: true,
            },
            placeholder: 'Search by space name or category',
            minimumInputLength: 1,
        }).on('select2:select', function () {
            window.location.assign(this.value);
        });
    });
</script>
