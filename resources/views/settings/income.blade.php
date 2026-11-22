
<x-app-layout>
    <title>{{ env('APP_NAME') }} | Service GFS Codes</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Settings</a></li>
                <li class="active">Service GFS Codes</li>
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
                <h6 class="panel-title text-bold">SERVICE GFS CODES</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body table-responsive">
                @if ($errors->default->any())
                    <div class="alert alert-danger" role="alert">
                        {{ $errors->default->first() }}
                    </div>
                @endif
                @can('CREATE_INCOME_LIST')
                    <div class="form-group text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#income">
                            Register GFS Code <i class="icon-add"></i>
                        </button>
                    </div>
                @endcan
                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">#</th>
                            <th class=" text-bold">GFS CODE</th>
                            <th class=" text-bold">SERVICE NAME</th>
                            <th class=" text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                        $i=1;
                        @endphp
                        @foreach ($incomes as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            <td>{{ $item->gfs_code }}</td>
                            <td>{{ $item->income_name }}</td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a  data-toggle="modal" data-target="#edit_income{{ $item->gfs_code }}" class="text-primary">Edit GFS Code <i class=" icon-database-edit2"></i></a>
                                            </li>
                                            <li>
                                                <a href=""  data-toggle="modal" data-target="#delete_permission{{ $item->gfs_code }}"  class="text-danger"> Delete GFS Code <i class=" icon-bin"></i></a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>

                            {{-- edit role modal --}}
                            <div id="edit_income{{ $item->gfs_code }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title text-bold text-uppercase">Edit Service GFS Code</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('income_list.update',['income_list' => $item->gfs_code]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label>GFS code<i class=" text-danger">*</i></label>
                                                        <input type="text"  name="gfs_code" class=" form-control" placeholder="GFS code" value="{{ $item->gfs_code }}" maxlength="32" required>
                                                        <span class=" text-danger text-error gfs_code_error"></span>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label>Service name<i class=" text-danger">*</i></label>
                                                        <input type="text" name="income_name" class=" form-control" placeholder="Service name" value="{{ $item->income_name }}">
                                                        <span class=" text-danger text-error income_name_error"></span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class=" form-group col-md-6">
                                                        <label>is space fee ?</label>
                                                        <select name="is_space_fee" class=" form-control">
                                                            <option value="{{ $item->is_space_fee }}">@if ($item->is_space_fee==0) NO @else YES @endif</option>
                                                            <option value="0">NO</option>
                                                            <option value="1">YES</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class=" row">
                                                    <div class="col-md-12 form-group">
                                                        <label>Description<i class=" text-danger">*</i></label>
                                                        <textarea name="description" class=" form-control" placeholder="Description">{{ $item->description }}</textarea>
                                                        <span class=" text-danger text-error description_error"></span>
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

                             <div id="delete_permission{{ $item->gfs_code }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete {{ $item->income_name }} ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('income_list.destroy', ['income_list' => $item->gfs_code]) }}" method="POST">
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
    @can('CREATE_INCOME_LIST')
        <div id="income" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gfs-create-title">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                        <h5 id="gfs-create-title" class="modal-title">Register Service GFS Code</h5>
                    </div>
                    <form action="{{ route('income_list.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            @if ($errors->newGfsCode->any())
                                <ul class="alert alert-danger" role="alert">
                                    @foreach ($errors->newGfsCode->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="form-group">
                                <label for="new-gfs-code">GFS code</label>
                                <input id="new-gfs-code" name="gfs_code" class="form-control" maxlength="32"
                                    value="{{ old('gfs_code') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="gfs-service-name">Service name</label>
                                <input id="gfs-service-name" name="income_name" class="form-control" maxlength="255"
                                    value="{{ old('income_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="gfs-space-fee">Space fee</label>
                                <select id="gfs-space-fee" name="is_space_fee" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1" {{ old('is_space_fee') == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="gfs-description">Description (optional)</label>
                                <textarea id="gfs-description" name="description" class="form-control"
                                    maxlength="255">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Register GFS Code</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
</x-app-layout>

@if ($errors->newGfsCode->any())
    <script>
        $(function () { $('#income').modal('show'); });
    </script>
@endif
