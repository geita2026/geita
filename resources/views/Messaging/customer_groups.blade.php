<x-app-layout>
    <title>{{ env('APP_NAME') }} | Groups</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> </a> Customer Groups</li>
                <li class="active">View groups</li>
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

                <div class=" table-header form-group p-10">
                    <h6 class=" text-center text-uppercase text-bold">BULK SMS CUSTOMER Groups</h6>
                </div>

                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#add_role">Add new Group <i class=" icon-add-to-list"></i></button>
                </div>
                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">#</th>
                            <th class=" text-bold">Group Name</th>
                            <th class="text-bold">Group Descriptions</th>
                            <th class=" text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($groups as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            <td >{{ $item->group_name }}</td>
                            <td >{{ $item->group_descriptions }}</td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a  data-toggle="modal" data-target="#edit_group{{ $item->group_id }}" class="text-primary">Edit Group <i class=" icon-database-edit2"></i></a>
                                            </li>
                                            <li>
                                                {{-- <form>
                                                    <button id="sweet_warning{{ $item->id }}" > Delete Permission <i class=" icon-bin"></i></button>
                                                </form> --}}
                                                <a href=""  data-toggle="modal" data-target="#delete_group{{ $item->group_id }}"  class="text-danger"> Delete Group <i class=" icon-bin"></i></a>
                                                <a    class="text-danger btn">  </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>

                            {{-- edit role modal --}}
                            <div id="edit_group{{ $item->group_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title text-uppercase">Edit Customer Group.</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('customer_groups.update',['customer_group' => $item->group_id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="">
                                                    <div class=" form-group">
                                                        <label>Group Name<i class=" text-danger">*</i></label>
                                                        <input type="text" name="group_name" class=" form-control" placeholder=" Group Name" value="{{ $item->group_name }}" required>
                                                    </div>
                                                </div>
                                                <div class="">
                                                    <div class=" form-group">
                                                        <label>Group descriptions<i class=" text-danger">*</i></label>
                                                        <textarea name="group_descriptions" class=" form-control">{{ $item->group_descriptions }}</textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- /edit role modal -->
                            {{-- delete modal --}}
                            <div id="delete_group{{ $item->group_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete {{ $item->group_name }} ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('customer_groups.destroy', ['customer_group' => $item->group_id]) }}" method="POST">
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
                            {{-- delete modal --}}
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
    <form action="{{ route('customer_groups.store') }}" method="POST">
        @csrf
        <div id="add_role" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title text-uppercase">Add BADGE GROUP.</h5>
                    </div>
                    <div class="modal-body">

                        <div class="">
                            <div class=" form-group">
                                <label>Group Name<i class=" text-danger">*</i></label>
                                <input type="text" name="group_name" class=" form-control" placeholder=" Group Name" value="{{ old('group_name') }}" required>
                            </div>
                        </div>
                        <div class="">
                            <div class=" form-group">
                                <label>Group Descriptions<i class=" text-danger">*</i></label>
                                <textarea name="group_descriptions" class=" form-control" placeholder="group descriptions here"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Save New Group</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>
