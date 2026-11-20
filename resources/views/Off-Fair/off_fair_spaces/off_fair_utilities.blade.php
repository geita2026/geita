<x-app-layout>
    <title>{{ env('APP_NAME') }} | Spaces</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href=""><i class=" icon-users2 position-left"></i>OFF FAIR SPACES</a></li>
            <li class="active">View-All-User</li>
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
            <h6 class="panel-title text-bold">REGISTER OFF FAIR UTILITIES</h6>
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
            <button class=" btn btn-default border-blue" data-toggle="modal" data-target="#new_utility"><i class=" icon-add"></i> Add New Utility</button>
        </div>
     </div>

    <table class="table table-bordered table-striped datatable-save-state">
        <thead>
            <tr class=" text-bold">
                 <th>S/N</th>
                <th>Utility Name</th>
                <th>Utility Priolity</th>
                <th>Utility Cost</th>
                <th>Utility Prescription</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    @php
    $i=1;
    @endphp
    @foreach ($utilities as  $item)
    <tr>
         <td>{{ $i }}</td>
         <td>{{ $item->utility_name }}</td>
        <td>{{ $item->utility_priolity }}</td>
        <td>{{ number_format($item->utility_cost) }}</td>
        <td>{{ $item->utility_description }}</td>
        {{--  action selector  --}}



        <td class="text-center">
            <ul class="icons-list">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-menu4"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a data-toggle="modal" data-target="#update_utility{{ $item->utility_id }}"  class="text-primary"><i class=" icon-pencil7"></i> Update Utility</a>


                        </li>
                        <li>
                            <a data-toggle="modal" data-target="#delete_utility{{ $item->utility_id }}" class=" text-danger text-bold"><i class=" icon-bin"></i>Delete Utility</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </td>


        {{-- edit modal --}}
        <div id="update_utility{{ $item->utility_id }}" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title text-bold text-center">UPDATE OFF FAIR SPACE</h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('Off-Fair-Utilities.update',['Off_Fair_Utility'=>$item->utility_id])}}" method="POST">
                            @csrf
                            @method('PATCH')
                          <div class=" row">
                            <div class=" form-group col-md-12">
                                <label>Utility Name</label>
                                <input type="text" name="utility_name"  class="form-control" placeholder="Average Population" value="{{ $item->utility_name }}">
                            </div>
                            <div class=" form-group col-md-12">
                                <span>Cost for the Space<i class="text-danger">*</i></span>
                                <input type="text" name="utility_cost"  class="form-control" placeholder="utility Cost in TZS" value="{{ $item->utility_cost }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Utility Priolity</label>
                               <select name="utility_priolity" class=" form-control" data-live-search="true" data-width="100%">
                                   <option value="{{ $item->utility_priolity }}">{{ $item->utility_priolity }}</option>
                                   <option value="Mandatory">Mandatory</option>
                                   <option value="Option"> Option</option>
                               </select>
                            </div>

                            <div class=" form-group col-md-12">
                                <label>Space Description</label>
                                <textarea name="utility_descriptions" placeholder="Utility Descriptions" class=" form-control" required>{{ $item->utility_description }}</textarea>
                            </div>

                            <div class="col-md-12 text-right">
                                <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                <button type="submit" class="btn btn-primary ml-20 btn-ladda btn-ladda-spinner" >Update Off Fair Utility</button>
                            </div>
                          </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{--// edit modal --}}


        {{-- delete modal --}}
        <div id="delete_utility{{ $item->utility_id }}" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Are you sure you whant to delete  <b></b> from the list ?</h5>
                    </div>
                    <div class="modal-body">
                        <p><code>Note!:</code> By deleting this space you will delete all informations relating to this space.</p>
                        <form action="{{ route('Off-Fair-Utilities.destroy',['Off_Fair_Utility'=>$item->utility_id]) }}" method="POST">
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
        <div id="new_utility" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title text-bold text-center">REGISTER NEW OFF FAIR UTILITY</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('Off-Fair-Utilities.store') }}" method="POST">
                            @csrf
                          <div class=" row">
                            <div class=" form-group col-md-12">
                                <span>Utility Name<i class="text-danger">*</i></span>
                                <input type="text" name="utility_name"  class="form-control" placeholder="Utility Name" value="{{ old('utility_name') }}">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Utility Priolity</label>
                               <select name="utility_priolity" class="bootstrap-select" data-live-search="true" data-width="100%">
                                   <option> - - - Select priolity - - -</option>
                                   <option value="Mandatory">Mandatory</option>
                                   <option value="Option"> Option</option>
                               </select>
                            </div>

                            <div class=" form-group col-md-12">
                                <label>Utility Cost</label>
                                <input type="text" name="utility_cost"  class="form-control" placeholder="Utility Cost" value="{{ old('utility_cost') }}">
                            </div>
                            <div class=" form-group col-md-12">
                                <label>Utility Description</label>
                                <textarea name="utility_descriptions" placeholder="Utility Descriptions" class=" form-control">.</textarea>
                            </div>
                            <div class="col-md-12 text-right">
                                <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                <button type="submit" class="btn btn-primary ml-20 btn-ladda btn-ladda-spinner" >Register Off Fair Utility</button>
                            </div>
                          </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- New space modal --}}
