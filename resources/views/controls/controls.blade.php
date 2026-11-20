<x-app-layout>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('roles.index') }}"><i class=" icon-accessibility position-left"></i>USER ROLES</a></li>
                <li class="active">View All user roles</li>
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


                <span class=" text-danger">@error('role_name') {{ $message }} @enderror</span>
                <div class=" form-group text-right">
                    <a href="{{ route('roles.create') }}" class=" btn btn-default border-primary" >Add Role <i class=" icon-add-to-list"></i></a>
                </div>

                <div class=" col-md-12 table-header">
                    <h4 class=" text-center">REGISTERED USER ROLES</h4>
                </div>

                <div class=" col-md-12 form-group">
                <table class="table datatable-button-html5-columns table-bordered table-striped  table-lg text-nowrap">
                    <thead>
                        <tr>
                            <th class=" text-bold text-center">#</th>
                            <th class=" text-bold text-center">ROLE NAME</th>
                            <th class=" text-bold text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @foreach ($roles as $key=> $item)
                        <tr>
                            <td class=" text-center">{{ $key+1 }}</td>
                            <th>{{ $item->name }}</th>
                           <td class=" text-center">
                            <a href="{{ route('roles.show',['role'=>$item->id]) }}" class=" btn btn-primary">Asign Permision <i class=" icon-plus2"></i></a> || 
                            <a data-toggle="modal" data-target="#delete_role{{ $item->id }}"  class=" btn btn-danger">Delete <i class=" icon-bin"></i></a>
                        </td>
                            {{--  Action selector  --}}
                            <div id="delete_role{{ $item->id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete this Role ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('roles.destroy', ['role' => $item->id]) }}" method="POST">
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
                        @endforeach
                    </tbody>
                </table>
                </div>
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

<script type="text/javascript" src="{{ asset('assets/js/pages/form_multiselect.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>


