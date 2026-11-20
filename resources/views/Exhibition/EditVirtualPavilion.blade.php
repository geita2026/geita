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
                    @foreach ($virtual_pavilions as  $item)
                    <h1 class=" text-center" id="h2"> {{ $item->companyName }} ' S  PAVILION SET UP </h1>
                    @endforeach
                </div>

                <div class=" form-group">
                    <form action="{{ route('virtual_pavilions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class=" col-md-12  form-group">
                            <div class=" form-group">
                                <label>Pavilion Description<i class=" text-danger">*</i></label>
                                <textarea name="pavilion_descriptions" class=" form-control" rows="5" placeholder=" Pavilion descriptions">{{ old('pavilion_descriptions') ?? $item->pavilion_descriptions }}</textarea>
                                <span class=" text-danger">@error('pavilion_descriptions') {{ $message }} @enderror</span>
                            </div>
                            <div class=" form-group">
                                <label>Welcome Word<i class=" text-danger">*</i></label>
                                <input name="welcome_word" class=" form-control" placeholder="Welcome word" value="{{ old('welcome_word') ?? $item->welcome_word }}">
                                <span class=" text-danger">@error('welcome_word') {{ $message }} @enderror</span>
                            </div>
    
                            <div class=" form-group text-right">
                                <button class=" btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>



                </div>
            </div>
        </div>
</x-app-layout>

<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
	<!-- /theme JS files -->
