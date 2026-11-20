<x-app-layout>
    <title>{{ env('APP_NAME') }} | Locations</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Locations</a></li>
                <li class="active">View All Locations</li>
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
                    <h4 class=" text-center">REGISTERED  LOCATIONS</h4>
                </div>
                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#addPos">Add Location <i class=" icon-plus2"></i></button>
                </div>
                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">#</th>
                            <th class=" text-bold">LOCATION NAME</th>
                            <th class=" text-bold">LOCATION DESCRIPTION</th>
                            <th class=" text-bold">LOCATION STATUS</th>
                            <th class=" text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($locations as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            <td class=" text-uppercase">{{ $item->locationName }}</td>
                            <td>{{ $item->locationDescription }}</td>
                            <td class=" text-center">
                                @if ($item->locationStatus==0)
                                    <label class=" bg-danger p-5">INACTIVE</label>
                                @elseif ($item->locationStatus==1)
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
                                                <a  data-toggle="modal" data-target="#editLocation{{ $item->locationId }}" class="text-primary">Edit Location <i class=" icon-database-edit2"></i></a>
                                            </li>
                                            <li>
                                                {{-- <form>
                                                    <button id="sweet_warning{{ $item->locationId }}" > Delete Permission <i class=" icon-bin"></i></button>
                                                </form> --}}
                                                <a href=""  data-toggle="modal" data-target="#deleteLocation{{ $item->locationId }}"  class="text-danger"> Delete Location <i class=" icon-bin"></i></a>
                                                <a    class="text-danger btn">  </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>

                            {{-- edit role modal --}}
                            <div id="editLocation{{ $item->locationId }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Edit LOCATION.</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('selling_locations.update',['selling_location' => $item->locationId]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class=" form-group">
                                                    <label>Location Name<i class=" text-danger">*</i></label>
                                                    <input type="text" name="locationName" class=" form-control" placeholder=" Location Name" value="{{ $item->locationName }}">
                                                </div>
                                                <div class=" form-group">
                                                    <label>location Descriptions<i class=" text-danger">*</i></label>
                                                    <textarea name="locationDescription" class=" form-control" placeholder="Pos Descriptions">{{ $item->locationDescription }}</textarea>
                                                </div>

                                                <div class=" form-group">
                                                    <label>location STATUS<i class=" text-danger">*</i></label>
                                                    <select name="locationStatus" class=" form-control">
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
                            <div id="deleteLocation{{ $item->locationId }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete this location ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('selling_locations.destroy', ['selling_location' => $item->locationId]) }}" method="POST">
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
    <form action="{{ route('selling_locations.store') }}" method="POST"  enctype="multipart/form-data">
        @csrf
        <div id="addPos" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Add user location.</h5>
                    </div>
                    <div class="modal-body">

                        <div class="">
                            <div class=" form-group">
                                <label>location Name<i class=" text-danger">*</i></label>
                                <input type="text"  name="locationName" class=" form-control" placeholder="location Name" value="{{ old('locationName') }}" required>
                            </div>
                            <div class=" form-group">
                                <label>location Description<i class=" text-danger">*</i></label>
                                <textarea name="locationDescription" class=" form-control" placeholder="Location Descriptions"></textarea>
                            </div>
                            <div class=" form-group">
                                <label>location STATUS<i class=" text-danger">*</i></label>
                                <select name="locationStatus" class=" form-control">
                                    <option value="1">ACTIVE</option>
                                    <option value="0">INACTIVE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Add Location</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>



