<x-app-layout>
    <title>{{ env('APP_NAME') }} | VirtualServices</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('virtual_pavilions.index') }}"><i class=" icon-menu10 position-left"></i> VIRTUAL SERVICES</a></li>
            <li class="active">View All services</li>
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
                    @foreach ($virtual_pavilions as  $item)
                    <h1 class=" text-center" id="h1"> <i class=" icon-gear"></i> {{ $item->companyName }} ' S  PAVILION SERVICE SET UP </h1>
                    @endforeach
                </div>

                <form action="{{ route('virtual_servicess.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class=" col-md-12 row form-group">
                        <div class=" form-group">
                            <label>Welcome Word<i class=" text-danger">*</i></label>
                            <input name="service_name" class=" form-control" placeholder="Welcome word" value="{{ old('service_name') }}">
                            <span class=" text-danger">@error('service_name') {{ $message }} @enderror</span>
                        </div>

                        <div class=" form-group">
                            <label>Pavilion Description<i class=" text-danger">*</i></label>
                            <textarea name="service_descriptions" class=" form-control" rows="5" placeholder=" Pavilion descriptions">{{ old('service_descriptions') }}</textarea>
                            <span class=" text-danger">@error('service_descriptions') {{ $message }} @enderror</span>
                        </div>

                        <div class="form-group">
                            <label >Service Image<i class=" text-danger">*</i></label>
                                <input type="file" name="service_image_name" class="file-input-extensions" data-browse-class="btn btn-primary" data-remove-class="btn btn-default" accept=".png,.jpg,.jpeg,.gif">
                                <span class=" text-danger">@error('service_image_name') {{ $message }} @enderror</span>
                        </div>

                        <div class=" form-group text-right">
                            <button class=" btn btn-primary">Save Changes</button>
                        </div>

                        @foreach ($virtual_pavilions as $item)
                        <div class=" form-group col-md-12">
                            <div class=" col-md-12 bg-teal-400">
                                <h1 id="h1"><marquee direction="left">{{ $item->welcome_word }}</marquee></h1>
                            </div>
                            <div class=" col-md-6">
                            <img src="{{ asset('VirtualImages/'.$item->pavilion_image_name) }}" style="width: 100%">
                            </div>
                            <div>
                                <h2>
                                    {{ $item->pavilion_descriptions }}
                                </h2>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </form>


                </div>
            </div>
        </div>
</x-app-layout>

<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>

	<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
	<!-- /theme JS files -->
