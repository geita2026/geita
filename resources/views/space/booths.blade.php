<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booths</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home9 position-left"></i>Home</a></li>
                <li class="active">Booth / Stand registration</li>
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

            <div class=" panel-body table-responsive">
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <span class=" text-danger alert">{{ $error }}</span>
                    @endforeach
                @endif
                <div class=" panel-body">
                    <div class="row form-group">
                        <div class="form-groupcol-md-12 text-right">
                            <a data-toggle="modal" data-target="#addbooth" class=" btn btn-primary">Add Partition <i
                                    class=" icon-plus2"></i></a>
                        </div>
                    </div>

                    {{-- add booth modal --}}
                    <div id="addbooth" class="modal fade" aria-modal="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h5 class="modal-title text-uppercase text-bold">ADD BOOTH DETAILS TO THE PAVILION
                                    </h5>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('booths.store') }}" method="POST">
                                        @csrf
                                        <div class=" row form-group">
                                            <div class="  col-md-6">
                                                <label>Booth Name<i class=" text-danger">*</i></label>
                                                <input type="text" name="booth_name" class=" form-control"
                                                    placeholder="Booth Name" value="{{ old('booth_name') }}">
                                            </div>
                                            <div class="  col-md-6">
                                                <label>Booth Size<i class=" text-danger">*</i></label>
                                                <input type="text" name="booth_size" class=" form-control"
                                                    placeholder="Booth Size" value="9">
                                                <span class="text-danger"> @error('booth_size')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>
                                        <div class=" row form-group">
                                            <div class="  col-md-6">
                                                <label>Booth Category <i class=" text-danger">*</i> </label>
                                                <select name="booth_category" class="bootstrap-select "
                                                    data-live-search="true" data-width="100%"
                                                    placehoder="Select department">
                                                    <option value="">Select Booth Category . . . .</option>
                                                    @foreach ($booth_categories as $category)
                                                        <option value="{{ $category->booth_category_id }}">
                                                            {{ $category->category_name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger"> @error('pavilion_category')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>

                                            <div class=" col-md-6">
                                                <label>Partition Status <i class=" text-danger">*</i></label>
                                                <select name="partitionStatus" class=" form-control">
                                                    <option value="UNRESERVED">UNRESERVED (OPEN)</option>
                                                    <option value="RESERVED">RESERVED</option>
                                                </select>
                                                <span class="text-danger"> @error('partitionStatus')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                            <input type="hidden" name="pavilionID" value="{{ $id }}">
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
                                        <div class=" row form-group">
                                            <div class=" text-right col-md-12">
                                                <button type="button" data-dismiss="modal"
                                                    class=" btn btn-secondary">Close</button>
                                                <button type="submit"
                                                    class="btn btn-success btn-ladda btn-ladda-spinner ml-20">Register
                                                    Booth</button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>


                        </div>
                    </div>
                    {{-- delete modal --}}

                    {{-- user registration here --}}
                    <div class=" row form-group">
                        {{-- booths table --}}
                        <table data-table-paginated class="table table-bordered table-striped table-responsive table-lg text-nowrap">
                            <thead>
                                @foreach ($pav as $d)
                                    <tr>
                                        <th colspan="6" class=" text-uppercase text-center">
                                            <h3>LIST OF BOOTHS AND THEIR
                                                DETAILS FOR THE <b
                                                    class=" text-bold text-blue-800">{{ $d->pavilionName }}
                                                    PAVILION</b>.</h3>
                                        </th>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th>Partition Number</th>
                                    <th>Partition Size</th>
                                    <th>Partition Location</th>
                                    <th>Partition Rate</th>
                                    <th>Partition Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($pavilions as $item)
                                    <tr>
                                        <td>{{ $item->boothNo }}</td>
                                        <th>{{ $item->boothSize }}</th>
                                        <td>{{ $item->category_name }}</td>
                                        <td>{{ number_format($item->category_cost, 2) }}
                                            {{ $item->type_of_currency }}
                                        </td>
                                        <td class=" text-center text-bold">
                                            {{ $item->bookingStatus }}
                                        </td>

                                        {{--  action selector  --}}
                                        <td class="text-center">
                                            <ul class="icons-list">
                                                <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                        <i class="icon-menu4"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        <li>
                                                            <a data-toggle="modal" data-target="#e{{ $item->boothID }}"
                                                                class=" text-primary"> Update Booth <i
                                                                    class="icon-pencil7"></i></a>
                                                        </li>
                                                        <li>
                                                            <a href="" data-toggle="modal"
                                                                data-target="#d{{ $item->boothID }}"
                                                                class="text-danger">Delete Booth <i
                                                                    class=" icon-bin"></i></a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </td>
                                        {{--  //end action selector  --}}

                                        {{-- Edit modal --}}
                                        <div id="e{{ $item->boothID }}" class="modal fade" aria-modal="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <button type="button" class="close"
                                                            data-dismiss="modal">&times;</button>
                                                        <h5 class="modal-title text-uppercase">EDIT BOOTH / STAND
                                                            DETAILS
                                                        </h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form
                                                            action="{{ route('booths.update', ['booth' => $item->boothID]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class=" row form-group">
                                                                <div class="  col-md-6">
                                                                    <label>Booth Number<i
                                                                            class=" text-danger">*</i></label>
                                                                    <input type="text" name="booth_number"
                                                                        class=" form-control"
                                                                        placeholder="Booth Number"
                                                                        value="{{ $item->boothNo }}">
                                                                    <span class="text-danger">
                                                                        @error('booth_number')
                                                                            {{ $message }}
                                                                        @enderror
                                                                    </span>
                                                                </div>
                                                                <div class="  col-md-6">
                                                                    <label>Booth Size<i
                                                                            class=" text-danger">*</i></label>
                                                                    <input type="text" name="booth_size"
                                                                        class=" form-control" placeholder="Booth Size"
                                                                        value="{{ $item->boothSize }}">
                                                                    <span class="text-danger"> @error('booth_size')
                                                                            {{ $message }}
                                                                        @enderror
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class=" row form-group">
                                                                <div class="  col-md-6">
                                                                    <label>Booth Category <i
                                                                            class=" text-danger">*</i></label>
                                                                    <select name="booth_category"
                                                                        class=" form-control">
                                                                        <option value="{{ $item->booth_category }}">
                                                                            {{ $item->category_name }}</option>
                                                                        @foreach ($booth_categories as $category)
                                                                            <option
                                                                                value="{{ $category->booth_category_id }}">
                                                                                {{ $category->category_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <span class="text-danger">
                                                                        @error('booth_location')
                                                                            {{ $message }}
                                                                        @enderror
                                                                    </span>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label>Partition Status <i
                                                                            class=" text-danger">*</i></label>
                                                                    <select name="partitionStatus"
                                                                        class=" form-control">
                                                                        <option value="{{ $item->bookingStatus }}">
                                                                            {{ $item->bookingStatus }}</option>
                                                                        <option value="UNRESERVED">UNRESERVED
                                                                            (OPEN)</option>
                                                                        <option value="RESERVED">RESERVED</option>
                                                                    </select>
                                                                    <span class="text-danger">
                                                                        @error('booth_location')
                                                                            {{ $message }}
                                                                        @enderror
                                                                    </span>
                                                                </div>
                                                                <input type="hidden" name="pavilionID"
                                                                    value="{{ $id }}">
                                                            </div>
                                                            <div class=" row form-group">
                                                                <div class=" col-md-12">
                                                                    <label>Specify Exhibitor Sectors under this space</label>
                                                                    <select name="participants[]" multiple="multiple"
                                                                        class="form-control listbox">
                                                                        @foreach ($sectors as $data)
                                                                            <option value="{{ $data->sectorID }}"
                                                                                @foreach ($selectedSectors as $value)  
                                                                         @if ($data->sectorID == $value->participants && $value->spaceId==$item->boothID) selected="selected"  @endif @endforeach>
                                                                                {{ $data->sectorName }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class=" row form-group">
                                                                <div class=" col-md-6">
                                                                    <label>Area Latitude<i class=" text-danger">*</i></label>
                                                                    <input type="text" name="latitude" class=" form-control" value="{{ $item->latitude }}">
                                                                </div>
                                                                <div class=" col-md-6">
                                                                    <label>Area Longitude<i class=" text-danger">*</i></label>
                                                                    <input type="text" name="longitude" class=" form-control" value="{{ $item->longitude }}">
                                                                </div>
                                                            </div>
                                                            <div class=" row form-group">
                                                                <div class=" text-right col-md-12">
                                                                    <button type="button" data-dismiss="modal"
                                                                        class=" btn btn-secondary">Close</button>
                                                                    <button type="submit"
                                                                        class="btn  btn-success btn-ladda btn-ladda-spinner ml-20">Save
                                                                        Changes</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- edit modal --}}

                                        {{-- delete modal --}}
                                        <div id="d{{ $item->boothID }}" class="modal fade" aria-modal="true">
                                            <div class="modal-dialog modal-md">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <button type="button" class="close"
                                                            data-dismiss="modal">&times;</button>
                                                        <h5 class="modal-title">Are you sure you whant to delete
                                                            this
                                                            Booth ?</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form
                                                            action="{{ route('booths.destroy', ['booth' => $item->boothID]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class=" text-right">
                                                                <button type="button" data-dismiss="modal"
                                                                    class=" btn btn-secondary">Close</button>
                                                                <button type="submit"
                                                                    class="btn btn-danger ml-20">Yes,
                                                                    Delete</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- delete modal --}}
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            </tbody>

                        </table>
                        {{-- //booths table --}}
                    </div>
                    <div class=" row form-group text-right">
                        {{ $pavilions->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- //user registration  --}}
</x-app-layout>
<!-- Theme JS files -->
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/inputs/duallistbox.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_dual_listboxes.js') }}"></script>
