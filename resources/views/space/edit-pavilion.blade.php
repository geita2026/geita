<x-app-layout>
    <title>{{ env('APP_NAME') }} | Pavilions</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home position-left"></i> Home</a></li>
                <li class="active">Edit Pavilion</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">
        <div class="panel panel-flat">
            <div class=" panel-body ">
                <div class=" panel-body">
                    @foreach ($pavilions as $item)
                        <div class=" form-group col-md-12 text-right">
                            <a href="{{ route('booths.show', ['booth' => $item->pavilionID]) }}"
                                title="Add booths if exists" class=" btn btn-primary">Manage Partition <i
                                    class=" icon-arrow-right13"></i></a> ||
                            <a href="" data-toggle="modal" data-target="#{{ $item->pavilionID }}"
                                class=" btn btn-danger"> Delete Space <i class=" icon-bin"></i></a>
                        </div>

                        {{-- delete modal --}}
                        <div id="{{ $item->pavilionID }}" class="modal fade" aria-modal="true">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h5 class="modal-title">Are you sure you whant to delete
                                            {{ $item->pavilionName }} Space ?</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form
                                            action="{{ route('pavilions.destroy', ['pavilion' => $item->pavilionID]) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class=" text-right">
                                                <button type="button" data-dismiss="modal"
                                                    class=" btn btn-secondary">Close</button>
                                                <button type="submit" class="btn btn-danger ml-20">Yes, Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- delete modal --}}

                        <div class=" col-md-12 form-group">

                            <form action="{{ route('pavilions.update', ['pavilion' => $item->pavilionID]) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class=" row ">
                                    <div class=" form-group col-md-6">
                                        <label>Pavilion Name<i class=" text-danger">*</i></label>
                                        <input type="text" name="pavilion_name" class=" form-control"
                                            placeholder="pavilion Name" value="{{ $item->pavilionName }}">
                                        <span class="text-danger"> @error('pavilion_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class=" form-group col-md-6">
                                        <label>Pavilion Category <i class="text-danger">*</i></label>
                                        <select name="pavilion_category" class="bootstrap-select "
                                            data-live-search="true" data-width="100%" placehoder="Select department">
                                            <option value="{{ $item->pavilionCategory }}">{{ $item->category_name }}
                                            </option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->category_id }}">
                                                    {{ $category->category_name }}</option>
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
                                        <label>Total Pavilion / Space Size (in meter square)<i
                                                class=" text-danger">*</i></label>
                                        <input type="text" name="pavilion_size" class=" form-control"
                                            placeholder=" Pavilion Size" value="{{ $item->pavilion_size }}">
                                        <span class="text-danger"> @error('pavilion_size')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class=" form-group col-md-6">
                                        <label>Space Status <i class=" text-danger">*</i></label>
                                        <select name="space_status" id="category" class="bootstrap-select "
                                            data-live-search="true" data-width="100%" placehoder="Select department"
                                            required>
                                            <option value="{{ $item->space_status }}">{{ $item->space_status }}
                                            </option>
                                            <option value="RESERVED">RESERVED</option>
                                            <option value="UNRESERVED">UNRESERVED (OPEN)</option>
                                        </select>
                                        <span class="text-danger"> @error('pavilion_category')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class=" row form-group">
                                    <div class=" form-group col-md-6">
                                        <label>Space Street </label>
                                        <select name="streetName" class=" form-control">
                                            <option value="{{ $item->spaceStreet }}">{{ $item->streetName }}</option>
                                            @foreach ($streets as $street)
                                                <option value="{{ $street->streetId }}">{{ $street->streetName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class=" form-group col-md-6">
                                        <label>Number of usable Partition / Booths <i class=" text-danger">*</i></label>
                                        <input type="number" name="numberOfPartitions" class=" form-control" value="{{ $item->numberOfPartitions }}">
                                    </div>
                                    <div class=" form-group col-md-6">
                                        <label>Who can book this pavilion ?</label>
                                        <select id="search-select" name="whoCanBook" class=" form-control">
                                            <option value="{{ $item->whoCanBook ?? 'ANY' }}">{{ $item->companyName ?? 'ANY' }}</option>
                                        </select>
                                    </div>
                                    <div class=" form-group col-md-6">
                                        <label>Pavilion / Space Description</label>
                                        <textarea name="pavilionDescription" class=" form-control" placeholder="Pavilion description" required>{{ $item->pavilionDescription }}</textarea>
                                        <span class="text-danger"> @error('pavilionDescription')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>

                               
                                
                                <div class=" row form-group">
                                    <div class=" col-md-12">
                                        <label>Specify Exhibitor Sectors under this space</label>
                                        <select name="participants[]" multiple="multiple" class="form-control listbox">
                                            @foreach ($sectors as $data)
                                                <option value="{{ $data->sectorID }}"
                                                    @foreach ($selectedSectors as $value)  
                                             @if ($data->sectorID == $value->participants) selected="selected"  @endif @endforeach>
                                                    {{ $data->sectorName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class=" row form-group">
                                    <div class="form-group col-md-6">
                                        <label>Pavilion Latitude</label>
                                        <input type="text" name="latitude" class=" form-control"
                                            placeholder="Pavilion Latitude" value="{{ $item->latitude }}">
                                        <span class="text-danger"> @error('latitude')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Pavilion Longitude</label>
                                        <input type="text" name="longitude" class=" form-control"
                                            placeholder="Pavilion Longitude" value="{{ $item->longitude }}">
                                        <span class="text-danger"> @error('longitude')
                                                {{ $message }}
                                            @enderror
                                        </span>

                                </div>

                                <div class=" text-right col-md-12 form-group">
                                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                            class="ladda-label">Update Space</span></button>
                                </div>
                            </form>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Theme JS files -->
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/inputs/duallistbox.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_dual_listboxes.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#search-select').select2({
            ajax: {
                url: '{{ route('search_companies') }}', // URL for the AJAX request
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // Search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.companyID,
                                text: item.registrationID+' || '+item.companyName+' || '+ item.companyPhone+' || '+ item.companyEmail,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search company',
            minimumInputLength: 0,
        });
    });
</script>