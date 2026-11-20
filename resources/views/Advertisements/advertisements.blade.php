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



                <span class=" text-danger">@error('permission_name') {{ $message }} @enderror</span>
                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#add_advert">Add new Advert <i class=" icon-plus22"></i></button>
                </div>


                @if ($errors->any())
                @foreach ($errors->all() as $error)
                <span class=" text-danger">{{ $error }} ,</span>
                @endforeach
                @endif

               <div class=" form-group">
                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">#</th>
                            <th class=" text-bold">ARDVERT TITLE</th>
                            <th class=" text-bold">ADVERT DESCRIPTIONS</th>
                            <th class=" text-bold">ADVERTISING DAYS</th>
                            <th class=" text-bold">ADVERT PREVIEW</th>

                            <th class=" text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($advertisements as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            <td >{{ $item->advertisement_title }}</td>
                            <td >{{ $item->advertisement_descriptions }}</td>
                            <td >{{ $item->advertising_days }}</td>
                            <td class=" text-center">
                                @if ($item->clip)
                                <video  height="150" autoplay muted loop>
                                    <source src="{{ url('Advertisements/'.$item->clip) }}" type="video/mp4">
                                </video>
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
                                                <a  data-toggle="modal" data-target="#edit_advert{{ $item->advertisement_id }}" class="text-primary">Edit Group <i class=" icon-database-edit2"></i></a>
                                            </li>
                                            <li>
                                                {{-- <form>
                                                    <button id="sweet_warning{{ $item->id }}" > Delete Permission <i class=" icon-bin"></i></button>
                                                </form> --}}
                                                <a href=""  data-toggle="modal" data-target="#delete_advert{{ $item->advertisement_id }}"  class="text-danger"> Delete Advert<i class=" icon-bin"></i></a>
                                                <a    class="text-danger btn">  </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>

                            {{-- edit role modal --}}
                            <div id="edit_advert{{ $item->advertisement_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Edit Badge Group.</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('advertisements.update',['advertisement' => $item->advertisement_id]) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="existing" value="{{ $item->clip }}">
                                                <div class="">
                                                    <div class=" form-group col-md-6">
                                                        <label>Advert Title<i class=" text-danger">*</i></label>
                                                        <input type="text" name="advertisement_title" class=" form-control" placeholder=" Advertisement Title" value="{{ $item->advertisement_title }}" required>
                                                    </div>
                                                </div>

                                                <div class="">
                                                    <div class=" form-group col-md-6">
                                                        <label>Advert Clip</label>
                                                        <input type="file" name="clip" class=" form-control">
                                                        <span class=" text-muted">allowed formart: mp4,gif and mp3,imv ( {{ $item->clip }} )</span>
                                                    </div>
                                                </div>
                                                <div class=" form-group col-md-12">
                                                    <label>Advert Descriptions</label>
                                                    <textarea name="advertisement_descriptions" class=" form-control" placeholder="Advertisement Descriptions">{{ $item->advertisement_descriptions ?? old('advertisement_descriptions') }}</textarea>
                                                </div>

                                                <div class=" form-group col-md-6">
                                                    <label>Advertise for how many days<i class=" text-danger">*</i></label>
                                                    <input type="number" name="advertising_days" class=" form-control" placeholder="Set number of days when this advert will be see" value="{{ $item->advertising_days ?? old('advertising_days') }}">
                                                </div>
                                                <div class=" form-group col-md-6">
                                                    <label>View Status<i class=" text-danger">*</i></label>
                                                    <select name="status" class=" form-control">
                                                        <option value="{{ $item->status }}">{{ $item->status }}</option>
                                                        <option value="Show">Show</option>
                                                        <option value="Hide">Hide</option>
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
                            <div id="delete_advert{{ $item->advertisement_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete {{ $item->advertisement_title }} ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('advertisements.destroy', ['advertisement' => $item->advertisement_id]) }}" method="POST">
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
    </div>
    <!-- /dashboard content -->
    {{-- add role modal --}}
    <form action="{{ route('advertisements.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="add_advert" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class=" close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title text-uppercase text-center text-bold"> ADD NEW ADVERT.</h5>
                    </div>
                    <div class="modal-body">

                        <div class="">
                            <div class=" form-group col-md-6">
                                <label>Advert Title<i class=" text-danger">*</i></label>
                                <input type="text" name="advertisement_title" class=" form-control" placeholder=" Advertisement Title" value="{{ old('advertisement_title') }}" required>
                            </div>
                        </div>

                        <div class="">
                            <div class=" form-group col-md-6">
                                <label>Advert Clip</label>
                                <input type="file" name="clip" class=" form-control" required>
                                <span class=" text-muted">allowed formart: mp4,gif and mp3,imv</span>
                            </div>
                        </div>
                        <div class=" form-group col-md-12">
                            <label>Advert Descriptions</label>
                            <textarea name="advertisement_descriptions" class=" form-control" placeholder="Advertisement Descriptions">{{ old('advertisement_descriptions') }}</textarea>
                        </div>

                        <div class=" form-group col-md-6">
                            <label>Advertise for how many days<i class=" text-danger">*</i></label>
                            <input type="number" name="advertising_days" class=" form-control" value="{{ old('advertising_days') }}" required>
                        </div>
                        <div class=" form-group col-md-6">
                            <label>View Status<i class=" text-danger">*</i></label>
                            <select name="status" class=" form-control">
                                <option> - - - - Select Status - - - -</option>
                                <option value="Show">Show</option>
                                <option value="Hide">Hide</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Save New Advert</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>
