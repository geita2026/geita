<x-app-layout>
    <title>{{ env('APP_NAME') }} | Permissions</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permissions.index') }}"><i class="icon-home2 position-left"></i> PERMISSIONS</a></li>
                <li class="active">View All Permissions</li>
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

                <span class=" text-danger">@error('companyName') {{ $message }} @enderror</span>
                <div class=" col-md-12 table-header form-group">
                    <h4 class=" text-center">REGISTERED ROLE PERMISSIONS</h4>
                </div>
                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#addCompany">Add Exhibitor <i class=" icon-plus2"></i></button>
                </div>
                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">#</th>
                            <th class=" text-bold">Company Name</th>
                            <th class=" text-bold">Company Title</th>
                            <th class=" text-bold">Print Badges</th>
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
                            <td class=" text-uppercase">{{ $item->companyName }}</td>
                            <td>{{ $item->badgeTitle }}</td>
                            <td class=" text-center"><a href="{{ route('emergency_badges.show',['emergency_badge'=>$item->companyId]) }}" class=" btn btn-default text-primary"><i class="icon-printer"></i> Print Badges</a></td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a  data-toggle="modal" data-target="#editCompany{{ $item->companyId }}" class="text-primary">Edit Exhibitor <i class=" icon-database-edit2"></i></a>
                                            </li>
                                            <li>
                                                {{-- <form>
                                                    <button companyId="sweet_warning{{ $item->companyId }}" > Delete Permission <i class=" icon-bin"></i></button>
                                                </form> --}}
                                                <a href=""  data-toggle="modal" data-target="#deleteExhibitor{{ $item->companyId }}"  class="text-danger"> Delete Permission <i class=" icon-bin"></i></a>
                                                <a    class="text-danger btn">  </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>

                            {{-- edit role modal --}}
                            <div id="editCompany{{ $item->companyId }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Edit Exhibitor Information.</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('emergency_badges.update',['emergency_badge' => $item->companyId]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="row">
                                                    <div class=" form-group col-md-6">
                                                        <label>COMPANY NAME<i class=" text-danger">*</i></label>
                                                        <input type="text"  name="companyName" class=" form-control" placeholder=" company Name" value="{{ $item->companyName }}" required>
                                                        <span class=" text-danger"></span>
                                                    </div>
                                                    <div class=" form-group col-md-6">
                                                        <label>EXHIBITOR badgeTitle<i class=" text-danger">*</i></label>
                                                        <input type="text"  name="badgeTitle" class=" form-control" placeholder=" Exhibitor badgeTitle" value="{{ $item->badgeTitle }}" required>
                                                        <span class=" text-danger"></span>
                                                    </div>
                                                    <div class=" form-group col-md-6">
                                                        <label>EXHIBITOR EMAIL</label>
                                                        <input type="text"  name="companyEmail" class=" form-control" placeholder=" company Email" value="{{ $item->companyEmail }}">
                                                        <span class=" text-danger"></span>
                                                    </div>
                                                    <div class=" form-group col-md-6">
                                                        <label>EXHIBITOR PHONE<i class=" text-danger">*</i></label>
                                                        <input type="text"  name="companyPhone" class=" form-control" placeholder=" company Phone" value="{{ $item->companyPhone }}">
                                                        <span class=" text-danger"></span>
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
                            <div id="deleteExhibitor{{ $item->companyId }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete this Exhibitor ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('emergency_badges.destroy', ['emergency_badge' => $item->companyId]) }}" method="POST">
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
    <form data-action="{{ route('emergency_badges.store') }}" method="POST"  enctype="multipart/form-data">
        @csrf
        <div id="addCompany" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">ADD EXHIBITOR INFORMATION</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class=" form-group col-md-6">
                                <label>COMPANY NAME<i class=" text-danger">*</i></label>
                                <input type="text"  name="companyName" class=" form-control" placeholder=" company Name" value="{{ old('companyName') }}" required>
                                <span class=" text-danger"></span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>EXHIBITOR badgeTitle<i class=" text-danger">*</i></label>
                                <input type="text"  name="badgeTitle" class=" form-control" placeholder=" Exhibitor badgeTitle" value="{{ old('badgeTitle') }}" required>
                                <span class=" text-danger"></span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>EXHIBITOR EMAIL</label>
                                <input type="text"  name="companyEmail" class=" form-control" placeholder=" company Email" value="{{ old('companyEmail') }}">
                                <span class=" text-danger"></span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>EXHIBITOR PHONE<i class=" text-danger">*</i></label>
                                <input type="text"  name="companyPhone" class=" form-control" placeholder=" company Phone" value="{{ old('companyPhone') }}">
                                <span class=" text-danger"></span>
                            </div>
                           
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Register Exhibitor</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>


