<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsorship</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Home</a></li>
                <li class="active">View All Package List</li>
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
                <h6 class="panel-title text-bold">REGISTERED PACKAGES LIST</h6>
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
                <span class=" text-danger alert">{{ $error }} ,</span>
                @endforeach
                @endif
                <span class=" text-danger">@error('permission_name') {{ $message }} @enderror</span>
                <div class=" form-group">
                    <a href="{{ route('sponsorships.index') }}" class=" btn btn-default text-primary"><i class=" icon-arrow-left12"></i> Back </a>
                </div>
                <table class="table datatable-save-state table-responsive table-bordered table-striped">
                    <thead class=" bg-slate">
                        <tr>
                            <th class=" text-bold">S/N</th>
                            <th class=" text-bold">Package Name</th>
                            <th class=" text-bold">Package_Minimal_Amount</th>
                            <th class=" text-bold">Package_Maximum_Amount</th>
                            <th class=" text-bold">Package Description</th>
                            <th class=" text-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($packages as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            {{--  add content to package  --}}
                            <div id="addContents{{ $item->package_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">

                                            <h5 class="modal-title text-uppercase"><b>{{ $item->package_name }}</b> Package ITEMS (Contents)</h5>
                                        </div>
                                        <div class="modal-body">
                                                <div class="">
                                                    <div class=" form-group alert-success alert-rounded">
                                                        <p class=" p-10">The following is the list of all items(contents) found in <b>{{ $item->package_name }} Package</b></p>
                                                    </div>
                                                    <input type="hidden" name="package_id" value="{{ $item->package_id }}">
                                                    <div class=" form-group col-md-12">
                                                        @foreach($contents as $value)
                                                        @if($item->package_id==$value->package_id)
                                                        <li>{{ $value->content_name }}</li>
                                                        @endif
                                                        @endforeach
                                                     </div>
                                               </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default text-primary" data-dismiss="modal">Close <i class=" icon-close2"></i></button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            {{--  add contents to package  --}}
                            <td data-toggle="modal" data-target="#addContents{{ $item->package_id }}">
                                <a  class=" text-bold text-black" title="Click to add contents in this Package">{{ $item->package_name }}</a>
                            </td>
                            <td >{{ number_format($item->from_amount) }} {{ $item->type_of_currency }}</td>
                            <td >{{ number_format($item->to_amount) }} {{ $item->type_of_currency }}</td>
                            <td>{{ $item->package_description }}</td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a  data-toggle="modal" data-target="#edit{{ $item->package_id }}" class="text-primary"> <i class=" icon-database-edit2"></i> Edit Package</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('contents.edit',['content'=>$item->package_id]) }}" class="text-primary"><i class=" icon-add"></i> Add Content</a>
                                            </li>
                                            <li>
                                                <a href="" data-toggle="modal" data-target="#delete{{ $item->package_id }}"  class="text-danger"> Delete Package <i class=" icon-bin"></i></a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>

                            {{-- edit role modal --}}
                            <div id="edit{{ $item->package_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title text-uppercase"><b>{{ $item->package_name }}</b></h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('sponsorships.update',['sponsorship'=>$item->package_id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="">
                                                    <div class=" form-group col-md-12">
                                                        <label>Package name<i class=" text-danger">*</i></label>
                                                        <input type="text" name="package_name" class=" form-control" placeholder="Sponsorship Package Name" value="{{ $item->package_name }}">
                                                        <span class="text-danger"> @error('package_name'){{ $message }}@enderror</span>
                                                     </div>
                                                    <div class=" form-group col-md-12">
                                                       <label>Package Costs From<i class=" text-danger">*</i></label>
                                                       <input type="text" name="from_amount" class=" form-control" value="{{ $item->from_amount }}" placeholder="Enter the start cost value of package">
                                                       <span class="text-danger"> @error('from_amount'){{ $message }}@enderror</span>
                                                    </div>
                                                    <div class=" form-group col-md-12">
                                                       <label> To Cost (Value)<i class=" text-danger">*</i></label>
                                                       <input type="text" name="to_amount" class=" form-control" value="{{ $item->to_amount }}" placeholder="Enter the end cost value of package">
                                                       <span class="text-danger"> @error('to_amount'){{ $message }}@enderror</span>
                                                    </div>
                                                    <div class=" form-group col-md-12">
                                                       <label>Type Of Currency<i class=" text-danger">*</i></label>
                                                      <select name="type_of_currency" class=" form-control" placehoder="Select department">
                                                          <option value="{{ $item->type_of_currency }}">{{ $item->type_of_currency }}</option>
                                                          <option value="TZS">TZS</option>
                                                          <option value="USD">USD</option>
                                                      </select>
                                                      <span class="text-danger"> @error('type_of_currency'){{ $message }}@enderror</span>
                                                   </div>
                                                   <div class=" form-group col-md-12">
                                                       <label>Package Description</label>
                                                       <textarea name="package_description" class=" form-control" placeholder="Package Descriptions">{{ $item->package_description }}</textarea>
                                                       <span class="text-danger"> @error('package_description'){{ $message }}@enderror</span>
                                                    </div>
                                               </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- /edit role modal -->
                            {{-- delete modal --}}
                            <div id="delete{{ $item->package_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <h5 class="modal-title">Are you sure you whant to delete <b>{{ $item->package_name }}</b> Sponsorship Package ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('sponsorships.destroy',['sponsorship'=>$item->package_id]) }}" method="POST">
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
</x-app-layout>
