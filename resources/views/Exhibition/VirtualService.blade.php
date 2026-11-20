<x-app-layout>
    <title>{{ env('APP_NAME') }} | VirtualPavilion</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('virtual_services.index') }}"><i class=" icon-menu10 position-left"></i> VIRTUAL PAVILIONS</a></li>
            <li class="active">View All Virtual Pavilions</li>
        </ul>
        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>
    </div>
<!-- Content area -->
<div class="content">

<div class="panel panel-flat">
<div class=" panel-body">


               <div class=" row">
                <div class=" col-md-6 ">
                    <a href="{{ route('virtual_pavilions.index') }}" class=" btn  btn-default border-primary"><i class=" icon-arrow-left12"></i> Back </a>
                </div>
                    <div class=" col-md-6 text-right">
                        <a data-toggle="modal" data-target="#addService" class=" btn  btn-default border-success">Register New Service <i class=" icon-add-to-list"></i></a>
                    </div>
                </div>


                <div class=" row form-group">
                    @if ($errors->any())
                    @foreach ($errors->all() as $error)
                    <span class=" text-danger">{{ $error }} ,</span>
                    @endforeach
                    @endif
                </div>

                    <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>

                            @foreach ($pavilion as $item)
                            <tr class="table-header">
                                <th colspan="6">
                                    <h4 class=" text-center">{{ $item->companyName  }} SERVICES</h4>
                                </th>
                            </tr>
                            @endforeach

                            <tr>
                                <th class=" text-center text-bold">#</th>
                                <th class=" text-center text-bold">SERVICE NAME</th>
                                <th class=" text-center text-bold">SERVICE DESCRIPTIONS</th>
                                <th class=" text-center text-bold">SERVICE IMAGE</th>
                                <th class=" text-center text-bold">SERVICE CREATED AT</th>
                                <th class=" text-center text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i=1;
                            @endphp
                            @foreach ($virtual_service as  $item)
                           <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $item->service_name }}</td>
                            <td>
                               {{ $item->service_descriptions }}
                            </td>
                            <td class=" text-center">
                                <img src="{{ asset('VirtualImages/'.$item->service_image_name ) }}"  height="50" width="50">
                            </td>

                            <td>{{ date('d M, Y h:i',strtotime($item->service_created_at)) }}</td>

                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @can('Update Virtual Services')
                                            <li>
                                                <a data-toggle="modal" data-target="#Update{{ $item->virtual_service_id }}" class=" text-primary"><i class="icon-arrow-right13"></i>Update Service</a>
                                            </li>
                                            @endcan

                                            @can('Delete Virtual Service')
                                            <li>
                                                <a  data-toggle="modal" data-target="#delete{{ $item->virtual_service_id }}"  class=" text-danger"><i class="icon-arrow-right13"></i> Delete Service</a>
                                            </li>
                                            @endcan

                                        </ul>
                                    </li>
                                </ul>
                            </td>

                            {{-- Preview modal --}}
                            <div id="Update{{ $item->virtual_service_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <h5 class="modal-title text-center  text-uppercase" id="h2">UPDATE {{ $item->service_name }} SERVICE</h5>
                                        </div>
                                        <div class="modal-body">
                                           <div class=" row">

                                            <form action="{{ route('virtual_services.update',['virtual_service'=>$item->virtual_service_id]) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PATCH')
                                                <div class=" col-md-12 row">
                                                    <div class="form-group">
                                                        <label>Service Name<i class=" text-danger">*</i></label>
                                                        <input name="service_name" class=" form-control" placeholder="Service Name" value="{{ old('service_name') ?? $item->service_name}}">
                                                        <span class=" text-danger">@error('service_name') {{ $message }} @enderror</span>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Service Description<i class=" text-danger">*</i></label>
                                                        <textarea name="service_descriptions" class=" form-control" rows="5" placeholder=" Service descriptions">{{ old('service_descriptions') ?? $item->service_descriptions }}</textarea>
                                                        <span class=" text-danger">@error('service_descriptions') {{ $message }} @enderror</span>
                                                    </div>

                                                    <div class=" form-group">
                                                        <label >Service Image Preview<i class=" text-danger">*</i></label>
                                                        <input type="file" name="service_image_name" class=" form-control" data-browse-class="btn btn-primary" data-remove-class="btn btn-default" accept="image/*" >
                                                        <span class=" text-danger">@error('service_image_name') {{ $message }} @enderror</span>
                                                    </div>

                                                    <div class="row">
                                                        <div class=" text-right col-md-10 form-group">
                                                            <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                        </div>
                                                        <div class="  col-md-2 form-group ">
                                                            <button type="submit" class=" btn btn-primary">Save Changes</button>
                                                        </div>

                                                    </div>

                                                </div>
                                            </form>



                                           </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Preview modal --}}


                                {{-- delete modal --}}
                                <div id="delete{{ $item->virtual_service_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title">Are you sure you whant to Delete <q>{{ $item->service_name }}</q> Service ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('virtual_services.destroy', ['virtual_service' => $item->virtual_service_id]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                   <div class=" row">
                                                    <div class=" form-group col-md-12">
                                                        <b class=" text-danger">Note!</b> This process is irrevesible!
                                                    </div>
                                                   <div class="  col-md-12 text-right">
                                                    <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                    <button type="submit" class="btn btn-danger ml-20" >Yes, Delete</button>
                                                   </div>
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
</x-app-layout>


 {{-- Preview modal --}}
 <div id="addService" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-center" id="h2">HOW YOUR PAVILION IS LOOKING</h5>
            </div>
            <div class="modal-body">

                <div class=" row ">

                    <form action="{{ route('virtual_services.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class=" col-md-12 row form-group">
                            <div class=" form-group">
                                <label>Service Name<i class=" text-danger">*</i></label>
                                <input name="service_name" class=" form-control" placeholder="Service Name" value="{{ old('service_name') }}">
                                <span class=" text-danger">@error('service_name') {{ $message }} @enderror</span>
                            </div>

                            <div class=" form-group">
                                <label>Service Description<i class=" text-danger">*</i></label>
                                <textarea name="service_descriptions" class=" form-control" rows="5" placeholder=" Service descriptions">{{ old('service_descriptions') }}</textarea>
                                <span class=" text-danger">@error('service_descriptions') {{ $message }} @enderror</span>
                            </div>

                            <div class="form-group">
                                <label >Service Image Preview<i class=" text-danger">*</i></label>
                                    <input type="file" name="service_image_name" class="file-input" data-browse-class="btn btn-primary" data-remove-class="btn btn-default" accept="image/*">
                                    <span class=" text-danger">@error('service_image_name') {{ $message }} @enderror</span>
                            </div>

                            <div class=" form-group text-right">
                                <button class=" btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
{{-- Preview modal --}}

<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>

	<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
	<!-- /theme JS files -->
