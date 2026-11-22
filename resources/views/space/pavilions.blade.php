<x-app-layout>
    <title>{{ env('APP_NAME') }} | Pavilions</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
           @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('pavilions.index') }}"><i class=" icon-home9 position-left"></i>PAVILIONS</a></li>
                <li class="active">View All pavilion</li>
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
           
            <div class=" panel-body table-responsive">
                <div class=" row">
                    
                    <div class=" col-md-12 form-group text-right">
                        <a href="{{ route('pavilions.create') }}" class="btn btn-default border-success text-bold">Register Space <i class=" icon-plus2"></i></a>
                    </div>
                </div>
                <div class=" form-group col-md-12">
                    <table data-table-paginated class="table  table-striped table-bordered  table-responsive">
                        <thead>
                            <tr>
                                <th colspan="7">
                                    <select id="search-select" name="search" class=" form-control"
                                    onchange="location = this.value;">
                                   </select>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="7" style="background-color: rgb(198, 198, 204);">
                                    <h4 class=" text-bold text-center text-uppercase">LIST OF REGISTERED SPACES.</h4>
                                </th>
                            </tr>
                            <tr>
                                {{-- <th>S/N</th> --}}
                                <th class=" text-bold text-center">SPACE NAME</th>
                                <th class=" text-bold text-center">CATEGORY NAME</th>
                                <th class=" text-bold text-center">STREET NAME</th>
                                <th class=" text-bold text-center"> SPACE SIZE (M<sup>2</sup> / Units)</th>
                                <th class=" text-bold text-center">SPACE STATUS</th>
                                <th class=" text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i=1;
                            @endphp
                            @foreach ($pavilions as  $item)
                            <tr>
                                {{-- <td>{{ $i }}</td> --}}
                                <td class=" text-uppercase">{{ $item->pavilionName }}</td>
                                <td>{{ $item->category_name }}</td>
                                <td>{{ $item->streetName }}</td>
                                <td class=" text-center">{{ number_format($item->pavilion_size,2) }} </td>
                                <td class=" text-center text-bold"><label class=" label label-info">{{ $item->space_status }}</label></td>
                                <td class=" text-center">
                                    <a href="{{ route('pavilions.edit',['pavilion'=>$item->pavilionID]) }}" title="Add booths if exists" class=" btn btn-primary">View Space <i class=" icon-arrow-right13"></i></a>
                                </td>
                                
                                 
                               
                            </tr>
                            @php
                            $i++;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class=" col-md-12 form-group text-right">
                    {{ $pavilions->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        $('#search-select').select2({
            ajax: {
                url: '{{ route('pavilions.create') }}', // URL for the AJAX request
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
                                id: '{{ env('APP_URL') }}/spaceManagement/pavilions/' +
                                    item.pavilionID+'/edit',
                                text: item.pavilionName+' || '+item.category_name+' || '+ item.space_status,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Data here . . .',
            minimumInputLength: 0,
        });
    });
</script>