<x-app-layout>
    <title>{{ env('APP_NAME') }} | Packages Benefits</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="icon-home2 position-left text-uppercase"></i>TICKET PACKAGES BENEFITS</a></li>
            <li class="active">View All ticket packages benefits</li>
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
        <h4 class="text-center text-bold">VIP TICKET PACKAGES BENEFITS LIST</h4>
    </div>

        <div class=" form-group text-right">
        <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#AddPackages">Add Benefit <i class=" icon-plus2"></i></button>
        </div>



        <div class=" col-md-12 form-group">
            <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                <thead>
                    <tr>
                        <th class=" text-bold text-center">#</th>
                        <th class="text-bold text-center">BENEFIT NAME</th>
                        <th class="text-bold text-center text-uppercase">BENEFIT COST</th>
                        <th class=" text-bold text-center">BENEFIT DESCRIPTIONS</th>
                        <th class=" text-bold text-center">BENEFIT CREATED ON</th>
                        <th class="text-bold text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                    @foreach ($benefits as $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ $item->benefit_name }}</td>

                        <td>{{ $item->benefit_descriptions }}</td>
                        <td>{{ $item->benefit_created_at }}</td>
                        <td class=" text-right">{{ $item->benefit_updated_at }}</td>
                        <td class="text-center">
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu4"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        @can('Update Vip Benefit')
                                        <li>
                                            <a  data-toggle="modal" data-target="#edit{{ $item->benefit_id }}" class=" text-primary"><i class="icon-pencil7"></i> Edit Packages</a>
                                        </li>
                                        @endcan

                                        @can('Delete Vip Benefit')
                                        <li>
                                            <a  data-toggle="modal" data-target="#delete{{ $item->benefit_id }}" class="text-danger"> <i class=" icon-bin"></i> Delete Benefit</a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li>
                            </ul>
                        </td>

                        {{-- edit role modal --}}
                        <div id="edit{{ $item->benefit_id }}" class="modal fade" aria-modal="true" data-backdrop="static">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h5 class="modal-title text-uppercase">Update <q>{{ $item->benefit_name }}</q> DETAILS.</h5>
                                    </div>
                                    <form action="{{ route('vip_packages_benefits.update',['vip_packages_benefit'=>$item->benefit_id]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class=" form-group col-md-12">
                                                  <label>Benefit Name<i class=" text-danger">*</i></label>
                                                     <input type="text" name="benefit_name" class=" form-control" placeholder=" Benefit Name" value="{{ $item->benefit_name}}" required>
                                                </div>
                                                  {{-- <div class=" form-group col-md-6">
                                                    <label>Benefit Cost<i class=" text-danger">*</i></label>
                                                       <input type="number" name="benefit_cost" class=" form-control" placeholder="Benefit Cost" value="{{ $item->benefit_cost}}" min="0" required>
                                                  </div>
                                                  <div class=" form-group col-md-6">
                                                    <label>Currency<i class=" text-danger">*</i></label>
                                                       <select name="currency" class=" form-control">
                                                        <option value="{{ $item->currency }}">{{ $item->currency }}</option>
                                                        <option value="TZS">TZS</option>
                                                        <option value="USD">USD</option>
                                                       </select>
                                                  </div> --}}
                                                <div class=" form-group col-md-12">
                                                   <label>Benefit Description</label>
                                                      <textarea class="form-control" name="benefit_descriptions" placeholder="Benefit description">{{ $item->benefit_descriptions }}</textarea>
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
                                 <div id="delete{{ $item->benefit_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete <b>{{ $item->benefit_name }}</b> ?</h5>
                                            </div>

                                            <div class="modal-body">
                                                <form action="{{ route('vip_packages_benefits.destroy', ['vip_packages_benefit' => $item->benefit_id]) }}" method="POST">
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
<form action="{{ route('vip_packages_benefits.store') }}" method="POST">
    @csrf
<div id="AddPackages" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-blue" >
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title text-center text-bold">ADD BENEFIT DETAILS</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class=" form-group col-md-12">
                      <label>Benefit Name<i class=" text-danger">*</i></label>
                         <input type="text" name="benefit_name" class=" form-control" placeholder=" Benefit Name" value="{{ old('benefit_name')}}" required>
                    </div>
                      {{-- <div class=" form-group col-md-6">
                        <label>Benefit Cost<i class=" text-danger">*</i></label>
                           <input type="number" name="benefit_cost" class=" form-control" placeholder="Benefit Cost" value="{{ old('benefit_cost')}}" min="0" required>
                      </div>
                      <div class=" form-group col-md-6">
                        <label>Currency<i class=" text-danger">*</i></label>
                           <select name="currency" class=" form-control">
                            <option value="TZS">TZS</option>
                            <option value="USD">USD</option>
                           </select>
                      </div> --}}
                    <div class=" form-group col-md-12">
                       <label>Benefit Description</label>
                          <textarea class="form-control" name="benefit_descriptions" placeholder="Benefit description">{{ old('benefit_descriptions') }}</textarea>
                    </div>
                 </div>
            </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-default border-warning" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span class="ladda-label">Register Benefit</span></button>
            </div>
        </div>
    </div>
</div>
</form>
</x-app-layout>
