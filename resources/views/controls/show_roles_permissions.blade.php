<x-app-layout>
    <title>{{ env('APP_NAME') }} | Roles-Permissions</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-accessibility2 position-left"></i>ROLES PERMISSIONS</a></li>
                <li class="active">Update Permissions</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">

        <!-- Switchery toggles -->
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">REGISTED ROLE PERMISSIONS</h5>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">
                <div class="col-md-12">

                    <div class="content-group-lg">
                        {{-- assign permission form --}}
                        @foreach ($roles as $role)
                            <form action="{{ route('roles.update', ['role' => $role->id]) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class=" row">

                                    <div class=" form-group col-md-12">
                                        <label>Role name <i class=" text-danger">*</i></label>
                                        <input type="text" name="role_name" value="{{ $role->name }}"
                                            class=" form-control">
                                        <span class="text-danger"> @error('role_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                        @endforeach

                        @foreach ($allPermission as $permission)
                            <div class="checkbox checkbox-switchery switchery-lg col-md-3">
                                <label>
                                    <input type="checkbox" name="permission[]" value="{{ $permission->id }}"
                                        class=" switchery"
                                        @foreach ($myPermissions as $item)
                                @if ($item->name == $permission->name)
                                                checked   @endif @endforeach>
                                    {{ $permission->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="check" value="removal_permission">
                    <div class=" text-right form-group col-md-12">
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                class="ladda-label">Save <i class=" icon-plus2"></i></span></button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /switchery toggles -->
    </div>
</x-app-layout>

<script type="text/javascript">
    $('#selectall').change(function() {
        if ($(this).prop('checked')) {
            $('input').prop('checked', true);
        } else {
            $('input').prop('checked', false);
        }
    });
    $('#selectall').trigger('change');
</script>
