<x-app-layout>
    <title>{{ env('APP_NAME') }} | Add Package Group</title>

    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Home</a></li>
                <li><a href="{{ route('package_groups.index') }}"><i class="icon-stack position-left"></i>Package Groups</a></li>
                <li class="active">Add Package Group</li>
            </ul>
        </div>
    </div>

    <div class="content">
        @if ($errors->any())
            <div class="alert alert-danger alert-styled-left alert-arrow-left">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('fail'))
            <div class="alert alert-danger alert-styled-left alert-arrow-left">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                {{ session('fail') }}
            </div>
        @endif

        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title text-uppercase text-bold">
                    <i class="icon-plus-circle2 position-left"></i> Add New Package Group
                </h6>
                <div class="heading-elements">
                    <a href="{{ route('package_groups.index') }}" class="btn btn-primary">
                        <i class="icon-arrow-left52"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="panel-body">
                <form action="{{ route('package_groups.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Package Group Name <i class="text-danger">*</i></label>
                            <input type="text" name="packageGroupName" class="form-control input-lg"
                                   placeholder="e.g. Gold Sponsors"
                                   value="{{ old('packageGroupName') }}">
                            <span class="text-danger">@error('packageGroupName') {{ $message }} @enderror</span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Status</label>
                            <input type="text" class="form-control input-lg" value="Active (default)" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description <i class="text-danger">*</i></label>
                        <textarea name="packageGroupDescription" class="form-control" rows="4"
                                  placeholder="Describe this package group...">{{ old('packageGroupDescription') }}</textarea>
                        <span class="text-danger">@error('packageGroupDescription') {{ $message }} @enderror</span>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Background Color <i class="text-danger">*</i></label>
                            <input type="color" name="backgroundColor" class="form-control"
                                   value="{{ old('backgroundColor', '#3b82f6') }}">
                            <span class="text-danger">@error('backgroundColor') {{ $message }} @enderror</span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Text Color <i class="text-danger">*</i></label>
                            <input type="color" name="textColor" class="form-control"
                                   value="{{ old('textColor', '#ffffff') }}">
                            <span class="text-danger">@error('textColor') {{ $message }} @enderror</span>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{ route('package_groups.index') }}" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-checkmark-circle"></i> Save Package Group
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
