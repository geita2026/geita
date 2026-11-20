<x-app-layout>
    <title>{{ env('APP_NAME') }} | Space</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('space_size_costs.index') }}"><i class=" icon-users2 position-left"></i>SPACE SIZE RANGES COSTS </a></li>
            <li class="active">View All space Costs</li>
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
            <h6 class="panel-title text-bold">REGISTER SPACE SIZE RANGE COSTS</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>
<div class=" panel-body table-responsive">

    {{-- validation messages --}}
    @if ($errors->any())
    @foreach ($errors->all() as $error)
    <span class="alert text-danger">{{ $error }} ,</span>
    @endforeach
    @endif
    {{-- //validation messages --}}



     <div class=" row">
        <div class=" col-md-6">
            <a href="{{ route('Off-Fair.index') }}" class=" btn border-slate"><i class=" icon-arrow-left22"></i> Back</a>
            <br><br>
        </div>
        <div class=" col-md-6 text-right">
            <button class=" btn btn-default border-primary text-primary" data-toggle="modal" data-target="#new_cost">Add New Space Cost <i class=" icon-add"></i></button>
        </div>
     </div>

    <table class=" table table-bordered table-striped datatable-save-state">
        <thead>
            <tr>
                <th class=" text-bold">S/N</th>
                <th class=" text-bold">SPACE SIZE RANGE NAME</th>
                <th class=" text-bold">SPACE SIZE RANGE COST (TZS)</th>
                <th class=" text-bold">SALES STATUS</th>
                <th class=" text-bold">SPACE COST REGISTRATION DATE</th>
                <th class=" text-bold">Actions</th>
            </tr>
        </thead>
        <tbody>
    @php
    $i=1;
    @endphp
    @foreach ($costs as  $item)
    <tr>
         <td>{{ $i }}</td>
         <td>{{ $item->area_size_name }}</td>
        <td>{{ number_format($item->size_range_cost) }}</td>
        <td>@if($item->sales_status==1) SOLID AS UNIT SPACE @else SOLID PER METRE SQUARE @endif</td>
        <td>{{ $item->size_range_created_at }}</td>
        {{--  action selector  --}}



        <td class="text-center">
            <ul class="icons-list">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-menu4"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a data-toggle="modal" data-target="#update_cost{{ $item->space_size_category_cost_id}}"  class="text-primary text-bold"><i class=" icon-pencil7 "></i> Update Space Cost</a>


                        </li>
                        <li>
                            <a data-toggle="modal" data-target="#delete_cost{{ $item->space_size_category_cost_id }}" class=" text-danger text-bold"><i class=" icon-bin"></i>Delete Space Cost</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </td>


        {{-- edit modal --}}
        <div id="update_cost{{ $item->space_size_category_cost_id }}" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title text-bold text-center">UPDATE SPACE SIZE RANGE COST</h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('space_size_costs.update',['space_size_cost'=>$item->space_size_category_cost_id])}}" method="POST">
                            @csrf
                            @method('PATCH')
                          <div class=" row">
                            <div class=" form-group col-md-12">
                                <label>Area size Name<i class="text-danger">*</i></label>
                                <input type="text" name="area_size_name"  class="form-control" placeholder="Area Size Name" value="{{ $item->area_size_name }}" required>
                            </div>

                            <div class=" form-group col-md-12">
                                <label>Size range Cost<i class="text-danger">*</i></label>
                                <input type="number" name="size_range_cost"  class="form-control" placeholder="Size Range Cost" value="{{ $item->size_range_cost }}" required min="1">
                            </div>
                            <div class=" form-group col-md-12">
                                <label>How space is sold ?<i class="text-danger">*</i></label>
                                <select name="sales_status" class=" form-control">
                                    <option value="{{ $item->sales_status }}">@if($item->sales_status==1)Solid as Unit Space @else Sold on meter Square @endif</option>
                                    <option value="1">Sold as Unit space</option>
                                    <option value="0">Sold on meter Square</option>
                                </select>
                            </div>
                            <div class="col-md-12 text-right">
                                <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                <button type="submit" class="btn btn-primary ml-20 btn-ladda btn-ladda-spinner" >Update Space Cost</button>
                            </div>
                          </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{--// edit modal --}}


        {{-- delete modal --}}
        <div id="delete_cost{{ $item->space_size_category_cost_id }}" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Are you sure you whant to delete  <b>{{ $item->size_range_from }} To {{ $item->size_range_to }}</b> Size Cost from the list ?</h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('space_size_costs.destroy',['space_size_cost'=>$item->space_size_category_cost_id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                           @can('Delete Utility')
                           <div class=" form-group p-20">
                            <input type="checkbox" name="deletion_status" value="1"> Complete Delete Data
                           </div>
                           @endcan
                            <div class="  text-right">
                                <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                <button type="submit" class="btn btn-danger ml-20 btn-ladda btn-ladda-spinner" >Yes, Delete</button>
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

{{-- new space modal --}}
        <div id="new_cost" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title text-bold text-center">REGISTER NEW SPACE SIZE RANGE COST</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('space_size_costs.store') }}" method="POST">
                            @csrf
                          <div class=" row">
                            <div class=" form-group col-md-12">
                                <label> Space Size range name<i class="text-danger">*</i></label>
                                <input type="text" name="space_size_range_name"  class="form-control" placeholder="Space Size Range Name" value="{{ old('space_size_range_name') }}" required>
                            </div>

                            <div class=" form-group col-md-12">
                                <label>Size range Cost (TZS)<i class="text-danger">*</i></label>
                                <input type="number" name="size_range_cost"  class="form-control" placeholder="Size Range Cost" value="{{ old('size_range_cost') }}" required min="1">
                            </div>
                            <div class=" form-group col-md-12">
                                <label>How space is sold ?<i class="text-danger">*</i></label>
                                <select name="sales_status" class=" form-control">
                                    <option>--- Select How Space is sold ---</option>
                                    <option value="1">Sold as Unit space</option>
                                    <option value="0">Sold per meter Square</option>
                                </select>
                            </div>

                            <div class="col-md-12 text-right">
                                <button type="button" data-dismiss="modal" class=" btn btn-secondary border-danger">Close</button>
                                <button type="submit" class="btn btn-primary ml-20 btn-ladda btn-ladda-spinner" >Register space Category Cost</button>
                            </div>
                          </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- New space modal --}}
