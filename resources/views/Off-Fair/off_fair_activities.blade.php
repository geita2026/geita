<x-app-layout>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
@include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>OFF FAIR ACTIVITIES</a></li>
            <li class="active">View All Off fair activities</li>
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
            <h6 class="panel-title text-bold">OFF FAIR ACTIVITIES</h6>
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
                <button class=" btn btn-default border-blue" data-toggle="modal" data-target="#new_activity">Add new Activity <i class=" icon-add"></i></button>
            </div>
         </div>

         {{-- user registration here --}}

         <div>

        {{-- New customer modal --}}
        {{-- loaded data from database --}}
        @foreach ($activities as $data)
        @endforeach
        {{-- /loaded data from database --}}
            <div id="new_activity" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title text-bold text-center">OFF FAIR ACTIVITIES</h5>
                            </div>
                            <div class="modal-body">
                                <div class="row form-group">
                                    <div class=" form-group">
                                        <code>Note!</code> fields with " <i class=" text-danger">*</i> " mark are required
                                    </div>
                                    <form action="{{ route('off-fair-activities.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                          <div class=" form-group col-md-12">
                                              <label>Activity Name<i class=" text-danger">*</i></label>
                                              <input type="text" name="activity_name" class=" form-control" placeholder="Activity Name" value="{{ old('activity_name') }}" required>
                                           </div>
                                           <div class=" form-group col-md-12">
                                             <label>Cost per 1 M<sup>2</sup><i class=" text-danger">*</i></label>
                                             <input type="number" min="0" name="activity_cost" class=" form-control" placeholder="Activity Cost" value="{{ old('Activity_cost') ?? 0 }}">
                                          </div>
                                          <div class=" form-group col-md-12">
                                             <label>Activity Descriptions</label>
                                             <textarea name="activity_descriptions" placeholder="Activity Descriptions" class=" form-control"></textarea>
                                          </div>
                                          <div class=" modal-footer">
                                            <div class="  form-group text-right col-md-12">
                                                <button class=" btn btn-warning" data-dismiss="modal"> Close Modal</button>
                                                <button type="submit" class="btn btn-primary border-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Register Off-Fair Activity</span></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {{-- // new customer modal --}}

            {{-- table for available customers in database--}}
            <div class=" form-group row">
                <div class=" col-md-12">
                    <table class=" table table-bordered table-striped datatable-save-state">
                        <thead>
                            <tr>
                                <th class=" text-bold">S/N</th>
                                <th class=" text-bold">Activity Name</th>
                                <th class=" text-bold">Activity Cost</th>
                                <th class=" text-bold">Activity Descriptions</th>
                                <th class=" text-bold">Date Of Creation</th>
                                <th class=" text-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach ($activities as $item)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $item->activity_name }}</td>
                                <td>{{ number_format($item->activity_cost,2) }}</td>
                                <td>{{ $item->activity_descriptions }}</td>
                                <td class=" text-uppercase">{{ $item->activity_created_at }}</td>

                                {{-- update customer modal --}}
                                <div id="update_activity{{ $item->activity_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h4 class="modal-title text-bold text-center">UPDATE OFF FAIR ACTIVITY DETAILS</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row form-group">
                                                        <div class=" form-group">
                                                            <code>Note!</code> fields with " <i class=" text-danger">*</i> " mark are required
                                                        </div>
                                                        <form action="{{ route('off-fair-activities.update',['off_fair_activity'=>$item->activity_id ]) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PATCH')
                                                              <div class=" form-group col-md-12">
                                                                  <label>Activity Name<i class=" text-danger">*</i></label>
                                                                  <input type="text" name="activity_name" class=" form-control" placeholder="Activity Name" value="{{ $item->activity_name }}" required>
                                                               </div>
                                                               <div class=" form-group col-md-12">
                                                                 <label>Activity Cost<i class=" text-danger">*</i></label>
                                                                 <input type="number" min="0" name="activity_cost" class=" form-control" placeholder="Activty Cost" value="{{ $item->activity_cost ?? 0 }}">
                                                              </div>
                                                              <div class=" form-group col-md-12">
                                                                 <label>Activity Descriptions</label>
                                                                 <textarea name="activity_descriptions" placeholder="Activity Descriptions" class=" form-control">{{ $item->activity_descriptions }}</textarea>
                                                              </div>
                                                              <div class=" modal-footer">
                                                                <div class="  form-group text-right col-md-12">
                                                                    <button class=" btn btn-warning" data-dismiss="modal"> Close Modal</button>
                                                                    <button type="submit" class="btn btn-primary border-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Update Activity</span></button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- /new customer modal --}}

                                    {{-- delete Customer Modal --}}
                                    <div id="delete_activity{{ $item->activity_id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <h4 class="modal-title text-bold text-center">ARE YOU SURE YOU WANT TO DELETE ACTIVITY DETAILS ? </h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="p-20">Note: This action will delete All <b>{{ $item->activity_name }}'S</b> Information Permanently.</p>
                                                        <form action="{{ route('off-fair-activities.destroy',['off_fair_activity'=>$item->activity_id ])}}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('DELETE')
                                                        </div>
                                                        <div class=" modal-footer">
                                                            <div class="  form-group text-right">
                                                                <button class=" btn btn-default border-blue" data-dismiss="modal"> Close Modal</button>
                                                                <button type="submit" class="btn btn-danger btn-ladda btn-ladda-spinner" ><span class="ladda-label">Delete Activity</span></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    {{-- //delete customer modal --}}

                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                               @can('Update Space Category')
                                            <li>
                                                <a data-toggle="modal" data-target="#update_activity{{ $item->activity_id  }}" class=" text-primary"><i class=" icon-pencil7"></i> Edit Activity Details</a>
                                            </li>
                                               @endcan

                                               @can('Delete Space Category')
                                                <li>
                                                        <a  data-toggle="modal" data-target="#delete_activity{{ $item->activity_id }}" class=" text-danger"><i class="icon-bin"></i> Delete Activity Details</a>
                                                </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
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
</div>
{{-- //user registration  --}}

<div id="myData">

</div>
<script>

</script>
</x-app-layout>
