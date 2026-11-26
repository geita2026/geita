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

                        <div id="permission-search-controls" class="col-md-12 form-group hidden">
                            <label for="permission-search">Search permissions</label>
                            <input type="search" id="permission-search" class="form-control"
                                placeholder="Search by permission name" autocomplete="off">
                            <p id="permission-search-empty" class="help-block hidden" role="status">
                                No matching permissions.
                            </p>
                        </div>

                        @foreach ($allPermission as $permission)
                            <div class="checkbox checkbox-switchery switchery-lg col-md-3"
                                data-permission-name="{{ $permission->name }}">
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

<script>
    (() => {
        const controls = document.getElementById('permission-search-controls');
        const search = document.getElementById('permission-search');
        const empty = document.getElementById('permission-search-empty');
        const permissions = document.querySelectorAll('[data-permission-name]');
        const normalize = value => value.toLowerCase().replace(/[_-]/g, ' ').trim();
        const entries = Array.from(permissions, row => ({
            row,
            name: normalize(row.dataset.permissionName),
        }));

        search.addEventListener('input', () => {
            const terms = normalize(search.value).split(/\s+/).filter(Boolean);
            let matches = 0;

            for (const { row, name } of entries) {
                const visible = terms.every(term => name.includes(term));
                row.classList.toggle('hidden', !visible);
                matches += Number(visible);
            }

            empty.classList.toggle('hidden', matches > 0 || terms.length === 0);
        });

        search.addEventListener('keydown', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });

        controls.classList.remove('hidden');
    })();
</script>
