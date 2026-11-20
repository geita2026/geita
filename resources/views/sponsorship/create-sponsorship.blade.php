<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsorship</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class=" icon-package position-left"></i>Home</a></li>
            <li class="active">Create New sponsorship Package</li>
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
            <h6 class="panel-title text-bold">REGISTER NEW SPONSORSHIP PACKAGE</h6>
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
        <code>Note!</code> fields with " <i class=" text-danger">*</i> " mark are required
    </div>


{{-- user registration here --}}

       <form action="{{ route('sponsorships.store') }}" method="POST">
                   @csrf
                <div class="">
                     <div class=" form-group col-md-12">
                         <label>Package name<i class=" text-danger">*</i></label>
                         <input type="text" name="package_name" class=" form-control" placeholder="Sponsorship Package Name" value="{{ old('package_name') }}">
                         <span class="text-danger"> @error('package_name'){{ $message }}@enderror</span>
                      </div>
                    
                     <div class=" form-group col-md-12">
                        <label>Type Of Currency to be paid<i class=" text-danger">*</i></label>
                       <select name="type_of_currency" class="bootstrap-select" data-live-search="true" data-width="100%">
                           <option value="">Select Type of currency . . . .</option>
                           <option value="TZS">TZS</option>
                           <option value="USD">USD</option>
                       </select>
                       <span class="text-danger"> @error('type_of_currency'){{ $message }}@enderror</span>
                    </div>
                    <div class=" form-group col-md-12">
                        <label>Package Group<i
                                class=" text-danger">*</i></label>
                        <select name="packageGroup" class=" form-control"
                            >
                            <option value="">- - - Select Package
                                Group - - -</option>
                            @foreach ($packageGroups as $data)
                                <option
                                    value="{{ $data->packageGroupId }}">
                                    {{ $data->packageGroupName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class=" form-group col-md-12">
                        <label>Package Description</label>
                        <textarea name="package_description" class=" form-control" placeholder="Package Descriptions">{{ old('package_description') }}</textarea>
                        <span class="text-danger"> @error('package_description'){{ $message }}@enderror</span>
                     </div>
                </div>
                    <div class=" text-right">
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Register package</span></button>
                    </div>
            </form>
        </div>
    </div>
</div>
{{-- //user registration  --}}
</x-app-layout>


