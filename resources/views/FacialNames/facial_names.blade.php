<x-app-layout>
    <title>{{ env('APP_NAME') }} | Badges</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permissions.index') }}"><i class="icon-home2 position-left"></i> EXHIBITION BADGES</a></li>
                <li class="active">View All exhibitor Badges</li>
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
                <h6 class="panel-title text-bold">Exhibitor Badges</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body table-responsive">

                <div class="row form-group">
                    <div class=" col-md-6">
                        <div class=" form-group">
                            <button type="submit" class=" btn btn-default border-blue-400" data-toggle="modal" data-target="#UploadFile">Upload Bulk Facial Names <i class=" icon-upload10"></i></button>
                        </div>
                    </div>

                    <div class=" col-md-6 text-right">
                        <div class=" form-group ">
                            <a href="{{ route('facial_name_companies.create') }}" class=" btn btn-success" data-toggle="modal" >Print Facial Name <i class=" icon-printer2"></i></a>
                        </div>
                    </div>
                </div>


                <span class=" text-danger">@error('file') {{ $message }} @enderror</span>

                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">S/N</th>
                            <th class=" text-bold">Group Name</th>
                            <th class=" text-bold">Text Color</th>
                            <th class=" text-bold">BackGround Color</th>
                            <th class="text-bold">Text Preview</th>
                            <th class=" text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($companies as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            <td >{{ $item->company_name }}</td>
                            <td >{{ $item->text_color }}</td>
                            <td >{{ $item->background_color }}</td>
                            <td  style="background-color: {{ $item->background_color }};color:{{ $item->text_color }}; text-align:center;"><b>{{ $item->group_name }}</b></td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a  data-toggle="modal" data-target="#edit_permission{{ $item->company_id }}" class="text-primary">Edit Group <i class=" icon-database-edit2"></i></a>
                                            </li>
                                            <li>
                                                {{-- <form>
                                                    <button id="sweet_warning{{ $item->id }}" > Delete Permission <i class=" icon-bin"></i></button>
                                                </form> --}}
                                                <a href=""  data-toggle="modal" data-target="#delete_facial{{ $item->company_id }}"  class="text-danger"> Delete Permission <i class=" icon-bin"></i></a>
                                                <a    class="text-danger btn">  </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>

                            {{-- edit role modal --}}
                            <div id="edit_permission{{ $item->company_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Edit Badge Group.</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('facial_name_companies.update',['facial_name_company' => $item->company_id]) }}" method="POST">
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
                                                        <label>Text Color<i class=" text-danger">*</i></label>
                                                        <input type="color" name="text_color" class=" form-control" placeholder="Text color ie White" value="{{ $item->text_color }}" required>
                                                    </div>
                                                </div>
                                                <div class="">
                                                    <div class=" form-group">
                                                        <label>Background Color<i class=" text-danger">*</i></label>
                                                        <input type="color" name="background_color" class=" form-control" placeholder=" Background Color ie, Blue" value="{{ $item->group_background_color }}" required>
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
                            <div id="delete_facial{{ $item->company_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete {{ $item->company_name }} ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('facial_name_companies.destroy', ['facial_name_company' => $item->company_id]) }}" method="POST">
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
    <form action="{{ route('facial_name_companies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="UploadFile" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title text-uppercase">Add BADGE GROUP.</h5>
                    </div>
                    <div class="modal-body">

                        <div class="">
                            <div class=" form-group">
                                <label>Companies Excel File<i class=" text-danger">*</i></label>
                                <input type="file" name="file" class=" form-control" placeholder=" Group Name" value="{{ old('group_name') }}" required>
                            </div>
                            <div class=" form-group">
                                <label>Facial Name Group<i class=" text-danger">*</i></label>
                                <select name="group_id" class=" form-control">
                                    <option> - - - - - Select Facial Name Group - - - - - - - </option>
                                    @foreach ($groups as $group)
                                    <option value="{{ $group->group_id }}">{{ $group->group_name }}</option>
                                    @endforeach
                                </select>
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
