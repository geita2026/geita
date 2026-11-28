<x-app-layout>
    <title>{{ env('APP_NAME') }} | Company</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class=" icon-office position-left"></i>Home</a></li>
            <li class="active">View All Exhibitor List</li>
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
        <div class="panel-body table-responsive">
                <div class=" table-header form-group">
                    <h3 class=" text-center"><b>LIST OF MY COMPANIES / INSTITUTIONS</b></h3>
                </div>
                <div class=" form-group ">
                    <select id="search-select" name="search" class=" form-control"
                    onchange="location = this.value;">
                   </select>
                </div>
                <div class="form-group">
                    <table data-table-paginated class=" table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class=" text-center">TSRN#</th>
                                <th class=" text-bold text-center">COMPANY NAME</th>
                                <th class=" text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=0; @endphp
                          @foreach ($companies as $item)
                          <tr>
                            <td><a href="{{ route('stickers.show',['sticker'=>$item->companyID]) }}" class="">{{ $item->registrationID }}</a></td>
                           
                            <td><a href="{{ route('stickers.show',['sticker'=>$item->companyID]) }}" class=""> {{ $item->companyName }} </a></td>
                            <td class=" text-center">
                                <a href="{{ route('stickers.show',['sticker'=>$item->companyID]) }}" class=" btn btn-primary">Go to car stickers <i class=" icon-arrow-right13"></i></a>
                            </td>
                        </tr>
                        @php $i++; @endphp
                          @endforeach
                        </tbody>
                    </table>
                </div>
                <div class=" text-right">
                    {{ $companies->withQueryString()->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        $('#search-select').select2({
            ajax: {
                url: '{{ route('search_companies') }}', // URL for the AJAX request
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
                                id: '{{ env('APP_URL') }}/badgesManagement/stickers/' +
                                    item.companyID,
                                text: item.registrationID+' || '+item.companyName+' || '+ item.companyPhone+' || '+ item.companyEmail,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Companies (write company name or Service Registration Number) . . .',
            minimumInputLength: 0,
        });
    });
</script>