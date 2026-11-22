<x-app-layout>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('roles.index') }}"><i class=" icon-accessibility position-left"></i>API REQUESTS</a></li>
                <li class="active">View All API requests</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">

            <div class=" panel-body">

                <div style=" background-color:rgb(227, 232, 236)" class=" col-md-12">
                    <h4 class=" p-20 text-center">LIST OF API REQUEST RESPONSES</h4>
                </div>

                <div class="form-group">
                <table data-table-paginated class="table datatable-button-html5-columns table-bordered table-striped  table-lg text-nowrap">
                    <thead>
                        <tr>
                            <th class=" text-bold text-center">#</th>
                            <th class=" text-bold text-center">Response Details</th>
                            <th class=" text-bold text-center">Reminder</th>
                            <th class=" text-bold text-center">Date Received</th>
                            <th class=" text-bold text-center">STATUS</th>
                            <th class=" text-bold text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($response as  $item)
                        <tr>
                            <td>{{ $i }}</td>
                            <th class=" text-capitalize">{{ $item->response }}</th>
                            <td>{{ $item->reminder }}</td>
                            <td>{{ date('d-m-Y h:i:s',strtotime($item->response_date)) }}</td>
                            <td>{{ $item->status }}</td>
                             {{--  action selector  --}}
                             <td>
                                <a href="" data-toggle="modal" data-target="#delete_response{{ $item->id }}"  class="text-danger">Delete  <i class=" icon-bin"></i></a>
                             </td>

                            {{--  Action selector  --}}
                            <div id="delete_response{{ $item->id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete this Response ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('api_logs.destroy', ['api_log' => $item->id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class=" text-right">
                                                    <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                    <button type="submit" class="btn btn-danger btn-ladda btn-ladda-spinner" data-style="slide-down"><span class="ladda-label">Yes, Delete</span></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- delete modal --}}
                        </tr>
                        @php
                        $i++;
                        @endphp
                        @endforeach
                    </tbody>
                </table>
                {{ $response->withQueryString()->links() }}
            </div>
        </div>
    </div>
    <!-- /dashboard content -->


    {{-- add role modal --}}
    <form action="{{ route('roles.store') }}" method="POST">
        <div id="add_role" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Add user Roles.</h5>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="">
                            <div class=" form-group">
                                <label>Role name<i class=" text-danger">*</i></label>
                                <input type="text" name="role_name" class=" form-control" placeholder=" role Name" value="{{ old('role_name') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Add Role <i class=" icon-add-to-list"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
