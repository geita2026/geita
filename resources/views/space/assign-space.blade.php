<x-app-layout>
    <!-- dashboard -->
    <title>{{ env('APP_NAME') }} | Spaces</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('assigns.index') }}"><i class=" icon-user position-left"></i>User Pavilions</a></li>
            <li class="active">View All user pevilions</li>
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

                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#add_role">Assign User To pavilion</button>
                </div>


                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Officer Name</th>
                            <th>Pavilion Allocated</th>
                            <th>Date_Of_Issue</th>
                            <th>Update </th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                    @endphp
                        @foreach ($assign as  $item)
                       <tr>
                        <td>{{ $i}}</td>
                        <th>{{ $item->user_name }}</th>
                        <td>{{ $item->pavilionName }}</td>
                        <td>{{ $item->created_at }}</td>

                        <td class=" text-center">
                            <a  data-toggle="modal" data-target="#e{{ $item->userSpaceID }}"><i class="icon-pencil7"></i></a>
                        </td>
                        <td class="text-center">
                            <a h data-toggle="modal" data-target="#delete{{ $item->userSpaceID }}"  class="text-danger ml-20"> <i class=" icon-bin"></i></a>
                        </td>
                            {{-- edit role modal --}}
                            <div id="e{{ $item->userSpaceID }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">EDIT SERVICE DETAILS.</h5>
                                        </div>


                                                <div class="modal-body">
                                                    <form action="{{ route('assigns.update',['assign'=>$item->userSpaceID]) }}" method="POST">
                                                        @csrf
                                                @method('PATCH')
                                                {{-- <div class="row"> --}}
                                                    <div class=" form-group col-xs-6">
                                                        <label>Users List</label>
                                                       <select name="user" class="form-control" data-live-search="true" data-width="100%" placehoder="Select Users">
                                                           <option value="{{ $item->user_uuid }}">{{ $item->user_name }}</option>
                                                           {{-- <optgroup label="Users List"> --}}
                                                            @foreach ($users as $user)
                                                            <option value="{{ $user->user_uuid}}" class=" text-uppercase text-bold">{{ $user->user_name}} _____ {{ $user->name }}</option>
                                                            @endforeach
                                                           {{-- </optgroup> --}}
                                                       </select>
                                                       <span class="text-danger"> @error('user'){{ $message }}@enderror</span>
                                                    </div>
                                                    <div class=" form-group col-xs-6">
                                                        <label>Pavilion List</label>
                                                       <select name="pavilion" class="form-control" data-live-search="true" data-width="100%" placehoder="Select department">
                                                           <option value="{{ $item->pavilionID }}">{{ $item->pavilionName }}</option>
                                                           {{-- <optgroup label="Space List"> --}}
                                                               @foreach ($pavilions as $pavilion)
                                                               <option value="{{ $pavilion->pavilionID}}" class=" text-uppercase">{{ $pavilion->pavilionName}}</option>
                                                               @endforeach
                                                           {{-- </optgroup> --}}
                                                       </select>
                                                       <span class="text-danger"> @error('pavilion'){{ $message }}@enderror</span>
                                                    </div>
                                                 </div>

                                            {{-- </div> --}}
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner " >Save changes</button>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                            <!-- /edit role modal -->

                             {{-- delete modal --}}
                             <div id="delete{{ $item->userSpaceID }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to remove supervision to this pavilion ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('assigns.destroy', ['assign' => $item->userSpaceID]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                               <div class=" text-right">
                                                <button type="button" data-dismiss="modal" class=" btn btn-default">Close</button>
                                                <button type="submit" class="btn btn-danger btn-ladda btn-ladda-spinner ml-20" >Yes, Remove</button>
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

            {{-- //data here --}}
        </div>
    </div>
<!-- /dashboard content -->

{{-- add role modal --}}
<form action="{{ route('assigns.store') }}" method="POST">
    @csrf
<div id="add_role" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-blue" >
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">ASSING PAVILIONS TO SAC OFFICERS FOR SUPERVISION</h5>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class=" form-group col-md-6">
                        <label>Pavilion List</label>
                       <select name="pavilion" class="bootstrap-select" data-live-search="true" data-width="100%" placehoder="Select department">
                           <option value="">Select Pavilion / Space to assign....</option>
                           <optgroup label="Space List">
                               @foreach ($pavilions as $pavilion)
                               <option value="{{ $pavilion->pavilionID}}" class=" text-uppercase">{{ $pavilion->pavilionName}}</option>
                               @endforeach
                           </optgroup>
                       </select>
                       <span class="text-danger"> @error('role'){{ $message }}@enderror</span>
                    </div>
                    <div class=" form-group col-md-6">
                        <label>Users List</label>
                       <select name="user" class="bootstrap-select" data-live-search="true" data-width="100%" placehoder="Select Users">
                           <option value="">Select User . . . .</option>
                           <optgroup label="Users List">
                            @foreach ($users as $user)
                            <option value="{{ $user->user_uuid}}" class=" text-uppercase text-bold">{{ $user->user_name}} _____ {{ $user->name }}</option>
                            @endforeach
                           </optgroup>
                       </select>
                       <span class="text-danger"> @error('user'){{ $message }}@enderror</span>
                    </div>
                 </div>

            </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span class="ladda-label">Assign Pavilion</span></button>
            </div>
        </div>
    </div>
</div>
</form>

{{-- //add modal  --}}
</x-app-layout>
<!-- Theme JS files -->
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_bootstrap_select.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>

 {{-- spine button script --}}
 <script type="text/javascript" src="{{ asset('assets/js/plugins/buttons/spin.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/plugins/buttons/ladda.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/pages/components_buttons.js') }}"></script>
 <!-- /spine button script -->
