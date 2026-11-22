<x-app-layout>
    <title>{{ env('APP_NAME') }} | Permit Processing</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i> HOME</a></li>
                <li class="active">View Entrance / Exit Permits</li>
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
            <div class=" panel-body ">

                <div class=" table-header form-group">
                    <h3 class=" text-center text-bold">EXHIBITION ENTRANCE / EXIT PERMITS</h3>
                </div>

                <div class=" form-group text-right">
                    <a href="{{ route('iopermits.create') }}" class=" btn btn-primary">Request Permit <i class=" icon-arrow-right13"></i></a>
                </div>
                {{-- FOR STAPH --}}
                <div class=" form-group">
                    <select id="search-select" name="search" class=" form-control"
                    onchange="location = this.value;">
                   </select>
                </div>
                <div class="form-group">
                    <table data-table-paginated class=" table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class=" text-center text-bold">#</th>
                                <th class=" text-center text-bold">Company Name</th>
                                <th class=" text-center text-bold">Type Of Permit</th>
                                <th class=" text-center text-bold">Permit Date</th>
                                <th class=" text-center text-bold">Permit Status</th>
                                <th class=" text-center text-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permits as $key=>$item)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $item->companyName }}</td>
                                    <td class=" text-center">
                                        <label class="text-bold text-primary">{{ $item->permit_type }}</label>
                                    </td>
                                    <td>{{ date('M d, Y H:i:s',strtotime($item->permit_created_at)) }}</td>

                                    <td class=" text-center">
                                        <label class=" label label-default"><b>{{ $item->permitStatus }}</b></label>
                                    </td>
                                    <td class=" text-center">
                                        <a href="{{ route('iopermits.show',['iopermit'=>$item->permit_id]) }}" class=" btn btn-primary">Preview Permit <i class=" icon-arrow-right13"></i></a>
                                        
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class=" form-group text-right">
                    {{ $permits->withQueryString()->links() }}
                </div>

            </div>
        </div>
    </div>
    <!-- /dashboard content -->


</x-app-layout>

<script>
    $(document).ready(function() {
        $('#search-select').select2({
            ajax: {
                url: '{{ route('search_permit') }}', // URL for the AJAX request
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // Search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: '{{ env('APP_URL') }}/spaceAllocation/iopermits/' +
                                    item.permit_id,
                                text: item.registrationID+' || '+item.companyName+' || '+ item.companyPhone+' || '+ item.permit_type+' || '+ item.permitStatus,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Permit . . .',
            minimumInputLength: 0,
        });
    });
</script>