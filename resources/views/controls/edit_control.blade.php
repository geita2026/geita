<x-app-layout>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ul>

        <ul class="breadcrumb-elements">
            <li><a href="#"><i class="icon-comment-discussion position-left"></i>Help center</a></li>
        </ul>
    </div>
    </div>
<!-- /page header -->

<!-- Content area -->
<div class="content">

<div class="panel panel-flat">
<div class=" panel-body table-responsive">

     <div class=" form-group">
        <a href="{{ route('roles.index') }}" class=" btn btn-default text-primary"><i class=" icon-arrow-left22"></i> Back</a>
    </div>
                {{-- assign permission form --}}
                @foreach ($roles as $role)
                <form action="{{ route('roles.update',['role'=>$role->id]) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    @endforeach

                <div class=" form-group col-md-6">
                    <label>Role name <i class=" text-danger">*</i></label>
                    <input type="text" name="role_name" class=" form-control" value="{{ $role->name }}">
                    <span class="text-danger"> @error('role_name'){{ $message }}@enderror</span>
                </div>



               <div class=" row">
                <div class=" table-header col-md-12 form-group">
                    <h4 class=" text-center">ROLES PERMISSION LIST</h4>
                </div>

                <div class=" col-md-12 form-group" >
                <div class="checkbox checkbox-switchery switchery-lg col-md-4">
                        <label>
                    <input type="checkbox" class="switchery" id="selectall">
                      Check All</label>
                    </div>
                </div>

                @foreach ($permissions as $permission)
                <div class="checkbox checkbox-switchery switchery-lg col-md-3">
                    <label>
                    <input type="checkbox"  name="permission[]" value="{{ $permission->id }}" class="switchery">
                    {{ $permission->name }}
                </label>
                </div>

                {{-- <div class=" col-md-3">
                     <input type="checkbox"  name="permission[]" value="{{ $permission->id }}"> <b class=" text-uppercase">{{ $permission->name }}</b>
                </div> --}}
                @endforeach

                <div class=" col-md-12 form-group text-right">
                    <button class="btn btn-primary">Give Pamission</button>
                </div>
               </div>

        </div>
</div>
</div>

    {{-- </div> --}}
<!-- /dashboard content -->
</x-app-layout>
    <script type="text/javascript">
        $('#selectall').change(function () {
            if ($(this).prop('checked')) {
            $('input').prop('checked', true);
            }
            else {
                $('input').prop('checked', false);
            }
        });
        $('#selectall').trigger('change');
        </script>
