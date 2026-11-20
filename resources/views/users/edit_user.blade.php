<x-app-layout>
    <title>{{ env('APP_NAME') }} | Users</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="icon-home2 position-left"></i>USERS</a></li>
            <li class="active">Edit-User</li>
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
            <h6 class="panel-title text-bold">UPDATE SYSTEM USER</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>
<div class=" panel-body table-responsive">
    <div class=" form-group">
        <a href="{{ route('users.index') }}" class=" btn btn-default text-primary"><i class=" icon-arrow-left22"></i> Back</a>
    </div>


@foreach ($users as $data)
       <form action="{{ route('users.update',['user'=>$data->id]) }}" method="POST" enctype="multipart/form-data">
                   @csrf
                   @method('PATCH')
                <div class="row">
                     <div class=" form-group col-md-6">
                         <label>user name<i class=" text-danger">*</i></label>
                         <input type="text" name="user_name" class=" form-control" placeholder="user Name" value="{{ $data->user_name }}">
                         <span class="text-danger"> @error('user_name'){{ $message }}@enderror</span>
                      </div>
                      <div class=" form-group col-md-6">
                        <label>user phone<i class=" text-danger">*</i></label>
                        <input type="text" name="phone_number" class=" form-control" placeholder=" phone number" value="{{ $data->phone_number }}">
                        <span class="text-danger"> @error('phone_number'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group col-md-6">
                        <label>user Email<i class=" text-danger">*</i></label>
                        <input type="text" name="user_email" class=" form-control" placeholder=" user email" value="{{ $data->email }}">
                        <span class="text-danger"> @error('user_email'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group col-md-6">

                         <label>User Role </label>
                         @if ($data->email=='superadmin@gmail.com')
                         <input type="hidden" name="role_name" value="{{ $data->role }}" class=" form-control" readonly>
                         <input type="text" value="{{ $data->name }}" class=" form-control" readonly>
                         <span class=" text-danger">Super system admin role can not be  edited, for this user</span>
                         @else
                         <select name="role_name" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select department" >
                            <option value="{{ $data->role }}">{{ $data->name }}</option>
                            <optgroup label="User Roles">
                                @foreach ($roles as $role)
                                <option value="{{ $role->id}}">{{ $role->name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                         @endif
                        <span class="text-danger"> @error('role_name'){{ $message }}@enderror</span>
                     </div>
                     <input type="hidden" name="signature" value="{{ $data->user_signature }}">
                     <div class="form-group col-md-6">

                        <label> User Electronic signature (signature image) <i class=" text-danger">Current sign sign</i></label>
                        <br>
                        <img src="{{ asset('signatures/'.$data->user_signature) }}" height="50" width="150">
                        <span class="text-danger"> @error('signature_image'){{ $message }}@enderror</span>
                    </div>
                     @endforeach
                     <div class="form-group col-md-6">
                        <label> User Electronic signature (signature image) <i class=" text-primary">Upload new one</i></label>
                        <input type="file" name="signature_image" class="file-input" data-show-upload="false" value="{{ old('signature_image') }}">
                        <span class="text-danger"> @error('signature_image'){{ $message }}@enderror</span>
                    </div>

                </div>
                    <div class=" text-right">
                       <button type="submit" class="btn btn-success btn-ladda btn-ladda-spinner">Update user</button>
                    </div>
            </form>
        </div>
    </div>
</div>
{{-- //user registration  --}}
</x-app-layout>

