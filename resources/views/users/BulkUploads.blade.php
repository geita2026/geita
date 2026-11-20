<x-app-layout>
    <!-- dashboard -->
    <title>{{ env('APP_NAME') }} | Users</title>
    <div class="page-header">
        <div class="page-header-content">
           @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('users.index') }}"><i class=" icon-users2 position-left"></i>Users</a></li>
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
        <div class=" panel-body">


            <form action="{{ route('bulkUpload.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class=" form-group col-md-12">
                    <input type="hidden" name="ok" value="ok">
                    <label>Choose File to Upload Permissions</label>
                    <input type="file" name="my_file" class="file-input" data-show-caption="false" data-show-upload="false">
                </div>
                <div class=" form-group col-md-12 text-right">
                    <button type="submit" class=" btn btn-primary">Upload</button>
                </div>
            </form>

        </div>

    </div>

</x-app-layout>


<!-- Theme JS files -->
<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
<!-- /theme JS files -->
