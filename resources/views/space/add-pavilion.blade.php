<x-app-layout>
    <title>{{ env('APP_NAME') }} | Space</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home9 position-left"></i>SPACE REGISTRATION</a></li>
                <li class="active">pavilion registration</li>
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
            <div class="panel-heading table-header">
                <h6 class="panel-title text-bold">REGISTER EXHIBITION SPACE </h6>
            </div>
            <div class=" panel-body">
                <form action="{{ route('pavilions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class=" row">
                        <div class=" form-group col-md-6">
                            <label>Space Name<i class=" text-danger">*</i></label>
                            <input type="text" name="pavilion_name" class=" form-control" placeholder="Space Name"
                                value="{{ old('pavilion_name') }}" required>
                            <span class="text-danger"> @error('pavilion_name')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class=" form-group col-md-6">
                            <label>Pavilion / Space Category <i class=" text-danger">*</i></label>
                            <select name="pavilion_category" id="category" class="bootstrap-select "
                                data-live-search="true" data-width="100%" placehoder="Select department" required>
                                <option value="">Select Space Category....</option>
                                
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->category_name }}
                                        </option>
                                    @endforeach

                                
                            </select>
                            <span class="text-danger"> @error('pavilion_category')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                    </div>

                    <div class=" row">
                        <div class=" form-group col-md-6">
                            <label>Unit or Space Size (in meter square)<i class=" text-danger">*</i></label>
                            <input type="text" name="pavilion_size" class=" form-control"
                                placeholder="Space size in (msq) or Unit" value="{{ old('pavilion_size') }}" required>
                            <span class="text-danger"> @error('pavilion_size')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class=" form-group col-md-6">
                            <label>Space Street </label>
                            <select name="streetName" class=" form-control">
                                <option> - - - Select Street - - -</option>
                                @foreach ($streets as $item)
                                    <option value="{{ $item->streetId }}">{{ $item->streetName }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class=" row form-group">
                        <div class=" form-group col-md-6">
                            <label>Pavilion / Space Image</label>
                            <input type="file" name="floorPlan" class=" form-control">
                        </div>
                        <div class=" form-group col-md-6">
                            <label>Space Status <i class=" text-danger">*</i></label>
                            <select name="space_status" id="category" class=" form-control" required>
                                <option value="">- - - Space Status - - -</option>
                                    <option value="UNRESERVED">UNRESERVED (OPEN)</option>
                                    <option value="RESERVED">RESERVED</option>
                            </select>
                            <span class="text-danger"> @error('pavilion_category')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                       

                        <div class=" form-group col-md-6">
                            <label>Number of usable Partition / Booths <i class=" text-danger">*</i></label>
                            <input type="number" name="numberOfPartitions" class=" form-control" value="0">
                        </div>
                        <div class=" form-group col-md-6">
                            <label>Pavilion / Space Description</label>
                            <textarea name="pavilion_description" class=" form-control" placeholder="Pavilion description"></textarea>
                            <span class="text-danger"> @error('pavilion_description')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                    </div>
                   
                    <div class=" row form-group">
                        <div class=" col-md-12">
                            <label>Specify Exhibitor Sectors under this space</label>
                            <select name="participants[]" multiple="multiple"
                                class="form-control listbox">
                                @foreach ($sectors as $data)
                                    <option value="{{ $data->sectorID }}">
                                        {{ $data->sectorName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class=" text-right">
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                class="ladda-label">Register Space</span></button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    {{-- //user registration  --}}
</x-app-layout>
<!-- Theme JS files -->
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/inputs/duallistbox.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_dual_listboxes.js') }}"></script>