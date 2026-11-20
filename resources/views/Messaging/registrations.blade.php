<x-app-layout>
    <title>{{ env('APP_NAME') }} | Badges</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permissions.index') }}"><i class="icon-home2 position-left"></i>BULK SMS CUSTOMER REGISTRATIONS</a></li>
                <li class="active">View registered customers</li>
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
                <h6 class="panel-title text-bold">BULK SMS CUSTOMERS</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body table-responsive">

                @if ($errors->any())
                @foreach ($errors->all() as $error)
                <span class=" text-danger">{{ $error }} ,</span>
                @endforeach
                @endif

                <div class="row form-group">
                    <div class=" col-md-3">
                        <div class=" form-group">
                            <button type="submit" class=" btn btn-default border-blue-400" data-toggle="modal" data-target="#UploadFile">Upload bulk customer Phone Numbers <i class=" icon-upload10"></i></button>
                        </div>
                    </div>

                        <form action="{{ route('customer_reg.index') }}" method="POST">
                            @csrf
                            @method('GET')
                            <div class="col-md-7">
                            <div class=" form-group">
                                <select name="group_name" class="bootstrap-select" data-live-search="true" data-width="100%">
                                    <option value=""> - - - - - Select Customer Group - - - - - - - </option>
                                    @foreach ($groups as $group)
                                    <option value="{{ $group->group_id }}">{{ $group->group_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <div class=" form-group">
                                <button type="submit" class=" btn btn-default border-primary">Load Data <i class=" icon-database-refresh"></i></button>
                            </div>
                            </div>
                        </form>


                </div>


                <span class=" text-danger">@error('file') {{ $message }} @enderror</span>



                <div class=" form-group">
                    <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                        <thead>
                            <tr>
                                <th colspan="4"><h5 class=" text-center text-bold">CUSTOMER LIST FOR @foreach ($receivers as $data)@endforeach {{ $data->group_name ?? '' }} GROUP</h5></th>
                            </tr>
                            <tr>
                                <th class=" text-bold text-center text-uppercase">#</th>
                                <th class=" text-bold text-center text-uppercase ">Customer Name</th>
                                <th class=" text-bold text-center text-uppercase">Phone Number</th>
                                <th class=" text-bold text-center text-uppercase">Group Name</th>
                                <th class=" text-bold text-center text-uppercase">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i=1;
                            @endphp
                            @foreach ($receivers as  $item)
                            <tr>
                                <td>{{ $i}}</td>
                                <td >{{ $item->customer_name }}</td>
                                <td >{{ $item->phone_number }}</td>
                                <td>{{ $item->group_name }}</td>
                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a href=""  data-toggle="modal" data-target="#edit_customer{{ $item->receiver_id }}"  class="text-primary"> Edit Customer <i class=" icon-pencil7"></i></a>
                                                </li>
                                                <li>
                                                    <a href=""  data-toggle="modal" data-target="#delete_customer{{ $item->receiver_id }}"  class="text-danger"> Delete Customer <i class=" icon-bin"></i></a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>

                                {{-- edit role modal --}}
                                <div id="edit_customer{{ $item->receiver_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title text-uppercase text-bold">Edit <b class=" text-default">{{ $item->customer_name }}</b> Customer.</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('customer_reg.update',['customer_reg' => $item->receiver_id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="">
                                                        <div class=" form-group">
                                                            <label>Customer Name<i class=" text-danger">*</i></label>
                                                            <input type="text" name="customer_name" class=" form-control" placeholder="Text color ie White" value="{{ $item->customer_name }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="">
                                                        <div class=" form-group">
                                                            <label>Phone Number<i class=" text-danger">*</i></label>
                                                            <input type="text" name="phone_number" class=" form-control" placeholder=" Group Name" value="{{ $item->phone_number }}" required>
                                                            <span class=" text-muted text-small">start with international Code without +, eg 255753427137</span>
                                                        </div>
                                                    </div>
                                                    <div class="">
                                                        <div class=" form-group">
                                                            <label>Customer Group<i class=" text-danger">*</i></label>
                                                            <input type="text" name="background_color" class=" form-control" placeholder=" Background Color ie, Blue" value="{{ $item->group_name }}" readonly>
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
                                <div id="delete_customer{{ $item->receiver_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete {{ $item->company_name }} ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('customer_reg.destroy', ['customer_reg' => $item->receiver_id]) }}" method="POST">
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
    <form action="{{ route('customer_reg.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="UploadFile" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title text-uppercase">UPLOAD CUSTOMER GROUP EXCEL.</h5>
                    </div>
                    <div class="modal-body">

                        <div class="">
                            <div class=" form-group">
                                <label>Companies Excel File<i class=" text-danger">*</i></label>
                                <input type="file" name="file" class=" form-control" placeholder=" Group Name" value="{{ old('group_name') }}" required>
                            </div>
                            <div class=" form-group">
                                <label>Customer Group<i class=" text-danger">*</i></label>
                                <select name="customer_group" class="bootstrap-select" data-live-search="true" data-width="100%" required>
                                    <option value=""> - - - - - Select Customer Group - - - - - - - </option>
                                    @foreach ($groups as $group)
                                    <option value="{{ $group->group_id }}">{{ $group->group_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Upload</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>
