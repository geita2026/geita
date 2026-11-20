<x-app-layout>
    <title>{{ env('APP_NAME') }} | Ticket Packages</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="icon-home2 position-left text-uppercase"></i>TICKET PACKAGES</a></li>
            <li class="active">View All ticket packages</li>
        </ul>
        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>
    </div>
<!-- /page header -->

<!-- Content area -->
<div class="content">

<div class="panel panel-flat">
<div class=" panel-body table-responsive">

     @if ($errors->any())
     @foreach ($errors->all() as $error)
     <span class=" text-danger"><li>{{ $error }}</li></span>
     @endforeach
     @endif

     <div class=" form-group col-md-12 table-header">
        <h4 class="text-center text-bold">VIP TICKET PACKAGES LIST</h4>
    </div>

        <div class=" form-group text-right">
        <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#AddPackages">Add Package <i class=" icon-plus2"></i></button>
        </div>



        <div class=" col-md-12 form-group">
            <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                <thead>
                    <tr>
                        <th class=" text-bold text-center">#</th>
                        <th class="text-bold text-center">PACKAGE NAME</th>
                        <th class="text-bold text-center text-uppercase">PACKAGE COST</th>
                        <th class=" text-bold text-center">COLLECTION MAPPING</th>
                        <th class=" text-bold text-center">PACKAGE COLOR</th>
                        <th class="text-bold text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                    @foreach ($packages as $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ $item->package_name }}</td>
                        <td class=" text-right">{{ number_format($item->package_cost,2) }} {{ $item->currency }}</td>
                        <td class=" text-center" style="font-size:25px;">{{ $item->gfs_code }}</td>
                        <td style="background-color: {{ $item->package_color }}" class=" text-bold text-center">{{ $item->package_color }}</td>

                        <td class="text-center">
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu4"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        @can('Update Vip Package')
                                        <li>
                                            <a  data-toggle="modal" data-target="#edit{{ $item->vip_package_id }}" class=" text-primary"><i class="icon-pencil7"></i> Edit Packages</a>
                                        </li>
                                        @endcan

                                        @can('Delete Vip Package')
                                        <li>
                                            <a  data-toggle="modal" data-target="#delete{{ $item->vip_package_id }}" class="text-danger"> <i class=" icon-bin"></i> Delete Package</a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li>
                            </ul>
                        </td>

                        {{-- edit role modal --}}
                        <div id="edit{{ $item->vip_package_id }}" class="modal fade" aria-modal="true" data-backdrop="static">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h5 class="modal-title text-uppercase">Update <q>{{ $item->category_name }}</q> DETAILS.</h5>
                                    </div>
                                    <form action="{{ route('vip_ticket_packages.update',['vip_ticket_package'=>$item->vip_package_id]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class=" form-group col-md-12">
                                                  <label>Package Name<i class=" text-danger">*</i></label>
                                                     <input type="text" name="package_name" class=" form-control" placeholder=" Package Name" value="{{ $item->package_name}}" required>
                                                </div>
                                                  <div class=" form-group col-md-6">
                                                    <label>Package Cost<i class=" text-danger">*</i></label>
                                                       <input type="number" name="package_cost" class=" form-control" placeholder="Package Cost" value="{{ $item->package_cost}}" min="0" required>
                                                  </div>
                                                  <div class=" form-group col-md-6">
                                                    <label>Currency<i class=" text-danger">*</i></label>
                                                       <select name="currency" class=" form-control">
                                                        <option value="{{ $item->currency }}">{{ $item->currency }}</option>
                                                        <option value="TZS">TZS</option>
                                                        <option value="USD">USD</option>
                                                       </select>
                                                  </div>

                                                  <div class=" form-group col-md-6">
                                                    <label>Package Gfs Code Mapping<i class=" text-danger">*</i></label>
                                                       <select name="gfs_code" class=" form-control" required>
                                                        @foreach ($incomes as $value)
                                                        @if($item->gfs_code==$value->gfs_code)
                                                        <option value="{{ $value->gfs_code }}">{{ $value->income_name }} - {{ $item->gfs_code }}</option>
                                                        @else
                                                        <option value="{{ $value->gfs_code }}">{{ $value->income_name }} - {{ $value->gfs_code }}</option>
                                                        @endif
                                                        @endforeach
                                                       </select>
                                                  </div>
                                                  <div class=" form-group col-md-6">
                                                    <label>Ticket Status<i class=" text-danger">*</i></label>
                                                       <input type="text" name="packageStatus" class=" form-control" placeholder="Package Name" value="{{ $item->packageStatus}}" required>
                                                  </div>

                                                <div class=" form-group col-md-12">
                                                   <label>Package Description (English)</label>
                                                      <textarea class="form-control" name="package_descriptions" placeholder="Product description">{{ $item->package_discriptions }}</textarea>
                                                </div>

                                                <div class=" form-group col-md-12">
                                                    <label>Package Description in swahili</label>
                                                       <textarea class="form-control" name="package_description_sw" placeholder="Product description">{{ $item->package_description_sw }}</textarea>
                                                 </div>

                                                

                                             </div>
                                        </div>

                                          <div class="modal-footer">
                                            <button type="button" class="btn btn-default border-warning" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span class="ladda-label">Update Product</span></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- /edit role modal -->
                        {{-- delete modal --}}
                                 <div id="delete{{ $item->vip_package_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete <b>{{ $item->package_name }}</b> ?</h5>
                                            </div>

                                            <div class="modal-body">
                                                <form action="{{ route('vip_ticket_packages.destroy', ['vip_ticket_package' => $item->vip_package_id]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                   <div class=" text-right">
                                                    <button type="button" data-dismiss="modal" class=" btn btn-default">Close</button>
                                                    <button type="submit" class="btn btn-danger btn-ladda btn-ladda-spinner ml-20"  data-style="slide-down">Yes, Delete</button>
                                                   </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- delete modal --}}
                           </tr>
                           @php $i++; @endphp
                            @endforeach
                        </tbody>

                    </table>
        </div>

</div>
            {{--  end here  --}}
</div>
</div>
<!-- /dashboard content -->

{{-- add role modal --}}
<form action="{{ route('vip_ticket_packages.store') }}" method="POST">
    @csrf
<div id="AddPackages" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-blue" >
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title text-center text-bold">ADD PACKAGE DETAILS</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class=" form-group col-md-12">
                      <label>Package Name<i class=" text-danger">*</i></label>
                         <input type="text" name="package_name" class=" form-control" placeholder=" Package Name" value="{{ old('package_name')}}" required>
                    </div>
                      <div class=" form-group col-md-6">
                        <label>Package Cost<i class=" text-danger">*</i></label>
                           <input type="number" name="package_cost" class=" form-control" placeholder="Package Cost" value="{{ old('package_cost')}}" min="0" required>
                      </div>
                      <div class=" form-group col-md-6">
                        <label>Currency<i class=" text-danger">*</i></label>
                           <select name="currency" class=" form-control">
                            <option value="TZS">TZS</option>
                            <option value="USD">USD</option>
                           </select>
                      </div>
                      <div class=" form-group col-md-6">
                        <label>Package Gfs Code Mapping<i class=" text-danger">*</i></label>
                           <select name="gfs_code" class=" form-control" required>
                            @foreach ($incomes as $item)
                            <option value="{{ $item->gfs_code }}">{{ $item->income_name }} - {{ $item->gfs_code }}</option>
                            @endforeach
                           </select>
                      </div>
                      <div class=" form-group col-md-6">
                        <label>Ticket Status<i class=" text-danger">*</i></label>
                           <input type="number" name="packageStatus" class=" form-control" placeholder="Package Name" value="{{ old('packageStatus')}}" required>
                      </div>
                    <div class=" form-group col-md-12">
                       <label>Package Description (English)</label>
                          <textarea class="form-control" name="package_descriptions" placeholder="Product description">{{ old('package_descriptions') }}</textarea>
                    </div>

                    <div class=" form-group col-md-12">
                        <label>Package Description in swahili</label>
                           <textarea class="form-control" name="package_description_sw" placeholder="Product description">{{ $item->package_description_sw }}</textarea>
                     </div>

                 </div>
            </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-default border-warning" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span class="ladda-label">Register Package</span></button>
            </div>
        </div>
    </div>
</div>
</form>
</x-app-layout>
