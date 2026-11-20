<x-app-layout>
    <title>{{ env('APP_NAME') }} | Roles</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i>ROLES</a></li>
                <li class="active">Register roles</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- Content area -->
    <div class="content">
        <!-- Switchery toggles -->
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">REGISTER USER ROLES</h5>
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
                        <form action="{{ route('roles.store') }}" method="POST">
                            @csrf

                            <div class=" row">
                                <div class=" form-group col-md-8">
                                    <label>Role name <i class=" text-danger">*</i></label>
                                    <input type="text" name="role_name" class=" form-control"
                                        value="{{ old('role') }}">
                                    <span class="text-danger"> @error('role_name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>

                            <div class=" row">
                                @foreach ($allPermission as $permission)
                                    <div class="checkbox checkbox-switchery switchery-lg col-md-3">
                                        <label>
                                            <input type="checkbox" name="permission[]" value="{{ $permission->name }}"
                                                class=" switchery">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>


                            <div class=" text-right form-group col-md-12">
                                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"
                                    data-style="slide-down"><span class="ladda-label">Save</span></button>
                            </div>
                        </form>
                        {{-- assign permission form --}}
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

<script>
    /* ------------------------------------------------------------------------------
     *
     *  # Styled checkboxes, radios and file input
     *
     *  Specific JS code additions for form_checkboxes_radios.html page
     *
     *  Version: 1.0
     *  Latest update: Aug 1, 2015
     *
     * ---------------------------------------------------------------------------- */

    $(function() {


        // Switchery
        // ------------------------------

        // Initialize multiple switches
        if (Array.prototype.forEach) {
            var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
            elems.forEach(function(html) {
                var switchery = new Switchery(html);
            });
        } else {
            var elems = document.querySelectorAll('.switchery');
            for (var i = 0; i < elems.length; i++) {
                var switchery = new Switchery(elems[i]);
            }
        }

        // Colored switches
        var primary = document.querySelector('.switchery-primary');
        var switchery = new Switchery(primary, {
            color: '#2196F3'
        });

        var danger = document.querySelector('.switchery-danger');
        var switchery = new Switchery(danger, {
            color: '#EF5350'
        });

        var warning = document.querySelector('.switchery-warning');
        var switchery = new Switchery(warning, {
            color: '#FF7043'
        });

        var info = document.querySelector('.switchery-info');
        var switchery = new Switchery(info, {
            color: '#00BCD4'
        });



        // Checkboxes/radios (Uniform)
        // ------------------------------

        // Default initialization
        $(".styled, .multiselect-container input").uniform({
            radioClass: 'choice'
        });

        // File input
        $(".file-styled").uniform({
            wrapperClass: 'bg-blue',
            fileButtonHtml: '<i class="icon-file-plus"></i>'
        });


        //
        // Contextual colors
        //

        // Primary
        $(".control-primary").uniform({
            radioClass: 'choice',
            wrapperClass: 'border-primary-600 text-primary-800'
        });

        // Danger
        $(".control-danger").uniform({
            radioClass: 'choice',
            wrapperClass: 'border-danger-600 text-danger-800'
        });

        // Success
        $(".control-success").uniform({
            radioClass: 'choice',
            wrapperClass: 'border-success-600 text-success-800'
        });

        // Warning
        $(".control-warning").uniform({
            radioClass: 'choice',
            wrapperClass: 'border-warning-600 text-warning-800'
        });

        // Info
        $(".control-info").uniform({
            radioClass: 'choice',
            wrapperClass: 'border-info-600 text-info-800'
        });

        // Custom color
        $(".control-custom").uniform({
            radioClass: 'choice',
            wrapperClass: 'border-indigo-600 text-indigo-800'
        });



        // Bootstrap switch
        // ------------------------------

        $(".switch").bootstrapSwitch();

    });
</script>
