
<x-app-layout>
    <title>{{ env('APP_NAME') }} | API Users</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> API REQUESTS USERS</a></li>
                <li class="active">View all registered api users</li>
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
            <div class="panel-heading">
                <h6 class="panel-title text-bold">REGISTERED API USERS</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body table-responsive">

                <span class=" text-danger">@error('permission_name') {{ $message }} @enderror</span>
                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#api_request">Add Request <i class=" icon-add"></i></button>
                </div>
                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold text-center">#</th>
                            <th class=" text-bold text-center">NAME</th>
                            <th class=" text-bold text-center">CLIENT REGISTERED EMAIL</th>
                            <th class=" text-bold text-center">ACCESS TOKEN</th>
                            <th class=" text-bold text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                        $i=1;
                        @endphp
                        @foreach ($api_requests as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            <td>{{ $item->request_name }}</td>
                            <td>{{ $item->client_email }}</td>
                            <td>{{ $item->access_token }}</td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a href=""  data-toggle="modal" data-target="#deleteRequest{{ $item->client_id }}"  class="text-danger"> Delete Permission <i class=" icon-bin"></i></a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>



                             <div id="deleteRequest{{ $item->client_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete {{ $item->request_name }} ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('api_requests.destroy', ['api_request' => $item->client_id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class=" text-right">
                                                    <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                    <button type="submit" class="btn btn-danger ml-20" >Yes, Delete</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </tr>
                        @php
                        $i++;
                        @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
    {{-- add role modal --}}

        <div id="api_request" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title text-uppercase">Add New API REQUEST</h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('api_requests.store') }}" method="POST" id="1add_api_request"  enctype="multipart/form-data">
                            @csrf
                        <div class="row">
                            <div class=" form-group col-md-12">
                                <label>Select Customeer (Company) <i class=" text-danger">*</i></label>
                                <select id="search-select" name="clientId" required>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Api Request Name<i class=" text-danger">*</i></label>
                                <input type="text"  name="request_name" class=" form-control" placeholder=" Api request Name" value="{{ old('request_name') }}">
                                <span class=" text-danger error-text request_name_error"></span>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Client Email<i class=" text-danger">*</i></label>
                                <input type="text"  name="client_email" class=" form-control" placeholder="Client Email" value="{{ old('client_email') }}">
                                <span class=" text-danger error-text client_email_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span class="ladda-label">Add Request</span></button>
                    </div>
                </form>
                </div>
            </div>
        </div>

    {{--  end  --}}
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
                                id: item.companyID,
                                text: item.registrationID + ' || ' + item.companyName +
                                    ' || ' + item.companyPhone + ' || ' + item.companyEmail,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Companies by company name or Tantrade Service Registration Number',
            minimumInputLength: 0,
        });
    });
</script>

