<x-app-layout>
    <title>{{ env('APP_NAME') }} | POS</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> POS</a></li>
                <li class="active">View All POS</li>
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

                <span class=" text-danger">@error('posName') {{ $message }} @enderror</span>
                <div class=" col-md-12 table-header form-group">
                    <h4 class=" text-center">REGISTERED  POS</h4>
                </div>
                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#addPos">Add Pos <i class=" icon-plus2"></i></button>
                </div>
                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">#</th>
                            <th class=" text-bold">POS NAME</th>
                            <th class=" text-bold">POS DESCRIPTION</th>
                            <th class=" text-bold">POS STATUS</th>
                            <th class=" text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($pos as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            <td class=" text-uppercase">{{ $item->posName }}</td>
                            <td>{{ $item->posDescription }}</td>
                            <td class=" text-center">
                                @if ($item->posStatus==0)
                                    <label class=" bg-danger p-5">INACTIVE</label>
                                @elseif ($item->posStatus==1)
                                <label class=" bg-success p-5">ACTIVE</label>
                                @endif
                            </td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a  data-toggle="modal" data-target="#editPos{{ $item->posId }}" class="text-primary">Edit POS <i class=" icon-database-edit2"></i></a>
                                            </li>
                                            <li>
                                                {{-- <form>
                                                    <button id="sweet_warning{{ $item->posId }}" > Delete Permission <i class=" icon-bin"></i></button>
                                                </form> --}}
                                                <a href=""  data-toggle="modal" data-target="#deletePos{{ $item->posId }}"  class="text-danger"> Delete POS <i class=" icon-bin"></i></a>
                                                <a    class="text-danger btn">  </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>

                            {{-- edit role modal --}}
                            <div id="editPos{{ $item->posId }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Edit POS.</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('pos.update',['po' => $item->posId]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class=" form-group">
                                                    <label>POS Name<i class=" text-danger">*</i></label>
                                                    <input type="text" name="posName" class=" form-control" placeholder=" POS Name" value="{{ $item->posName }}">
                                                </div>
                                                <div class=" form-group">
                                                    <label>POS Description</label>
                                                    <textarea name="posDescription" class=" form-control" placeholder=" POS Description">{{ $item->posDescription }}</textarea>
                                                </div>

                                                <div class=" form-group">
                                                    <label>POS STATUS<i class=" text-danger">*</i></label>
                                                    <select name="posStatus" class=" form-control">
                                                        <option value="1">ACTIVE</option>
                                                        <option value="0">INACTIVE</option>
                                                    </select>
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
                            <div id="deletePos{{ $item->posId }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete this POS ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('pos.destroy', ['po' => $item->posId]) }}" method="POST">
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
                            {{-- DELETE --}}

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
    <form action="{{ route('pos.store') }}" method="POST"  enctype="multipart/form-data">
        @csrf
        <div id="addPos" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Add user POS.</h5>
                    </div>
                    <div class="modal-body">

                        <div class="">
                            <div class=" form-group">
                                <label>POS Name<i class=" text-danger">*</i></label>
                                <input type="text" id="posName" name="posName" class=" form-control" placeholder=" POS Name" value="{{ old('posName') }}" required>
                            </div>
                            <div class=" form-group">
                                <label>POS Description</label>
                                <textarea name="posDescription" class=" form-control" placeholder=" POS Description"></textarea>
                            </div>
                            <div class=" form-group">
                                <label>POS STATUS<i class=" text-danger">*</i></label>
                                <select name="posStatus" class=" form-control">
                                    <option value="1">ACTIVE</option>
                                    <option value="0">INACTIVE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Add POS</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>



