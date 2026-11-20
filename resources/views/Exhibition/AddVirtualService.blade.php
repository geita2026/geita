<x-app-layout>
    <title>{{ env('APP_NAME') }} | VirtualPavilion</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('virtual_pavilions.index') }}"><i class=" icon-menu10 position-left"></i> VIRTUAL PAVILIONS</a></li>
            <li class="active">View All Virtual Pavilions</li>
        </ul>
        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>
    </div>
<!-- Content area -->
<div class="content">

<div class="panel panel-flat">

    <div class=" panel-body">

               <div class=" row form-group">
                <div class=" col-md-6">
                    <a href="{{ route('virtual_pavilions.index') }}" class=" btn  btn-default border-primary text-primary"> <i class=" icon-arrow-left12"></i> Back</a>
                </div>
                </div>

                <div class=" form-group col-md-12 bg-teal-400">
                    @foreach ($pavilion as  $item)
                    <h1 class=" text-center" id="h1"> <i class=" icon-gear"></i> {{ $item->companyName }} ' S  PAVILION SET UP </h1>
                    @endforeach
                </div>

                <form action="{{ route('virtual_service.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class=" col-md-12 row form-group">
                        <div class=" form-group">
                            <label>Service Name<i class=" text-danger">*</i></label>
                            <input name="service_name" class=" form-control" placeholder="Service Name" value="{{ old('service_name') }}">
                            <span class=" text-danger">@error('service_name') {{ $message }} @enderror</span>
                        </div>

                        <div class=" form-group">
                            <label>Service Description<i class=" text-danger">*</i></label>
                            <textarea name="pavilion_descriptions" class=" form-control" rows="5" placeholder=" Service descriptions">{{ old('service_descriptions') }}</textarea>
                            <span class=" text-danger">@error('service_descriptions') {{ $message }} @enderror</span>
                        </div>

                        <div class="form-group">
                            <label >Service Image Preview<i class=" text-danger">*</i></label>
                                <input type="file" name="service_image_name" class="file-input"data-browse-class="btn btn-primary" data-remove-class="btn btn-default" accept="image/*">
                                <span class=" text-danger">@error('service_image_name') {{ $message }} @enderror</span>
                        </div>

                        <div class=" form-group text-right">
                            <button class=" btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>


                </div>
            </div>
        </div>

</x-app-layout>

<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>

	<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
	<!-- /theme JS files -->

