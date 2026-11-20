<x-app-layout>
    <title>{{ env('APP_NAME') }} | Spaces</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title">

            </div>
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('Off-Fair-Spaces.index') }}"><i class=" icon-users2 position-left"></i>OFF FAIR
                        SPACES</a></li>
                <li class="active">View spaces</li>
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
                <h6 class="panel-title text-bold">REGISTERED SPACES FOR OFF FAIR SPACES</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body table-responsive">


                {{-- validation messages --}}
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <span class="alert text-danger">{{ $error }} ,</span>
                    @endforeach
                @endif
                {{-- //validation messages --}}

                <div class=" row">

                    <div class=" col-md-12 text-right">
                        <button class=" btn btn-default border-blue" data-toggle="modal" data-target="#new_space"><i
                                class=" icon-add"></i> Add New Space</button>
                    </div>
                </div>

                <table class=" table table-bordered table-striped datatable-save-state">
                    <thead>
                        <tr class=" text-bold">
                            <th>Space Name</th>
                            <th>Space Size / Unit</th>
                            <th>Space Category</th>
                            <th>Space Cost</th>
                            <th>Off_Fair_Space_Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($spaces as $item)
                            <tr>
                                <td>{{ $item->space_name }}</td>
                                <td>{{ number_format($item->space_size) }}</td>
                                <td>{{ $item->category_name }}</td>
                                <td>{{ number_format($item->space_cost) }}  ~ {{ number_format($item->spaceCostWeekend) }} TZS  @ Unit / M <sup>2</sup></td>
                                <td>{{ $item->space_descriptions }} </td>
                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">

                                                <li>
                                                    <a data-toggle="modal"
                                                        data-target="#update_space{{ $item->space_id }}"
                                                        class="text-primary"><i class=" icon-pencil7"></i> Edit
                                                        Space</a>
                                                </li>

                                                <li>
                                                    <a data-toggle="modal"
                                                        data-target="#delete_space{{ $item->space_id }}"
                                                        class=" text-danger text-bold"><i class=" icon-bin"></i>Delete
                                                        Space</a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>


                                {{-- edit modal --}}
                                <div id="update_space{{ $item->space_id }}" class="modal fade" aria-modal="true"
                                    data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h4 class="modal-title text-bold text-center">UPDATE OFF FAIR SPACE</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('Off-Fair-Spaces.update', ['Off_Fair_Space' => $item->space_id]) }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class=" row">
                                                        <div class=" form-group col-md-6">
                                                            <label>Space Name<i class="text-danger">*</i></label>
                                                            <input type="text" name="space_name" class="form-control"
                                                                placeholder="Space Name"
                                                                value="{{ $item->space_name }}" required>
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Space Size(M<sup>2</sup>) / Unit<i
                                                                    class="text-danger">*</i></label>
                                                            <input type="number" name="space_size" class="form-control"
                                                                placeholder="Space Name"
                                                                value="{{ $item->space_size }}" min="0">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label>Space Category<i class=" text-danger">*</i></label>
                                                            <select name="space_category" class=" form-control">
                                                                <option value="{{ $item->space_category }}">
                                                                    {{ $item->category_name }}</option>
                                                                @foreach ($categories as $a)
                                                                    <option value="{{ $a->space_category_id }}">
                                                                        {{ $a->category_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Space Image</label>
                                                            <input type="file" name="spaceImage"
                                                                placeholder="Space Image" class=" form-control">
                                                        </div>
                                                        <div class=" form-group col-md-6" id="unit_cost1"
                                                            style="display: hidden">
                                                            <span>Cost for 1 Unit / M<sup>2</sup> <span
                                                                    class=" text-muted">(For Noramal Days)</span><i
                                                                    class="text-danger">*</i> </span>
                                                            <input type="number" name="space_cost" class="form-control"
                                                                placeholder="Space Cost in TZS"
                                                                value="{{ $item->space_cost }}" min="0">
                                                        </div>
                                                        <div class=" form-group col-md-6" id="unit_cost1"
                                                            style="display: hidden">
                                                            <span>Cost for 1 Unit / M<sup>2</sup> <i
                                                                    class="text-danger">*</i> <span
                                                                    class=" text-muted">(For Weekend Days)</span></span>
                                                            <input type="number" name="spaceCostWeekend"
                                                                class="form-control" placeholder="Space Cost in TZS"
                                                                value="{{ $item->spaceCostWeekend }}" min="0">
                                                        </div>

                                                        <div class=" form-group col-md-12">
                                                            <label>Space Description</label>
                                                            <textarea name="space_descriptions" placeholder="Space Descriptions" class=" form-control">{{ $item->space_descriptions }}</textarea>
                                                        </div>
                                                        <div class=" form-group col-md-12 text-center">
                                                            <img src="{{ $item->spaceImage ?? 'sas' }}"
                                                                height="50" width="100">
                                                        </div>

                                                        <div class="col-md-12 text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-secondary">Close</button>
                                                            <button type="submit"
                                                                class="btn btn-primary ml-20 btn-ladda btn-ladda-spinner">Update
                                                                Space</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- // edit modal --}}


                                {{--  //end action selector  --}}
                                {{-- delete modal --}}
                                <div id="delete_space{{ $item->space_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete <b></b> from
                                                    the list ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <p><code>Note!:</code> By deleting this space you will delete all
                                                    informations relating to this space.</p>
                                                <form
                                                    action="{{ route('Off-Fair-Spaces.destroy', ['Off_Fair_Space' => $item->space_id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="checkbox" name="type_delete" checked> Partial Delete
                                                    Data
                                                    <div class=" text-right">
                                                        <button type="button" data-dismiss="modal"
                                                            class=" btn btn-secondary">Close</button>
                                                        <button type="submit" class="btn btn-danger ml-20">Yes,
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
            </div>
        </div>
    </div>
</x-app-layout>

{{-- new space modal --}}
<div id="new_space" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title text-bold text-center">REGISTER NEW OFF FAIR SPACES</h3>
            </div>
            <div class="modal-body">
                <form action="{{ route('Off-Fair-Spaces.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class=" form-group col-md-6">
                            <label>Space Name<i class="text-danger">*</i></label>
                            <input type="text" name="space_name" class="form-control" placeholder="Space Name"
                                value="{{ old('space_name') }}" required>
                        </div>
                        <div class=" form-group col-md-6">
                            <label>Space Size(M<sup>2</sup>) / Unit<i class="text-danger">*</i></label>
                            <input type="number" name="space_size" class="form-control" placeholder="Space Size"
                                value="1" min="1">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Space Category<i class=" text-danger">*</i></label>
                            <select name="space_category" id="space_categories" class="bootstrap-select "
                                data-live-search="true" data-width="100%">
                                <option value="">- - - Select space category from list - - -</option>
                                @foreach ($categories as $item)
                                    <option value="{{ $item->space_category_id }}">{{ $item->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class=" form-group col-md-6">
                            <label>Space Image</label>
                            <input type="file" name="spaceImage" placeholder="Space Image" class=" form-control">
                        </div>

                        <div class=" form-group col-md-6" id="average_population">
                            <label>Average Capacity (people can carry) <i class=" text-danger">*</i></label>
                            <input type="text" name="average_population" class="form-control"
                                placeholder="Average Population" value="{{ $item->average_population }}">
                        </div>

                        <div class=" form-group col-md-6" id="unit_cost1" style="display: hidden">
                            <span>Cost for 1 Unit / M<sup>2</sup> <span class=" text-muted">(For Noramal Days)</span><i
                                    class="text-danger">*</i> </span>
                            <input type="number" name="space_cost" class="form-control"
                                placeholder="Space Cost in TZS"  min="0">
                        </div>
                        <div class=" form-group col-md-6" id="unit_cost1" style="display: hidden">
                            <span>Cost for 1 Unit / M<sup>2</sup> <i class="text-danger">*</i> <span
                                    class=" text-muted">(For Weekend Days)</span></span>
                            <input type="number" name="spaceCostWeekend" class="form-control"
                                placeholder="Space Cost in TZS"  min="0">
                        </div>


                        
                        <div class=" form-group col-md-12">
                            <label>Space Description</label>
                            <textarea name="space_descriptions" placeholder=" Space Descriptions" class=" form-control"></textarea>
                        </div>

                        <div class="col-md-12 text-right">
                            <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                            <button type="submit" class="btn btn-primary ml-20 btn-ladda btn-ladda-spinner">Register
                                Space</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- New space modal --}}
<script>
    $("#space_categories").change(function() {
        if ($(this).val() == "e3be1c56-c52c-4715-8875-99c8c33660c6") { //for functional Halls
            $("#cost_categories").hide();
            $("#average_population").show();
            $("#unit_cost").show();
        } else {
            $("#cost_categories").show();
            $("#average_population").hide();
            $("#unit_cost").hide();
        }
    });

    $("#space_categories1").change(function() {
        if ($(this).val() == "e3be1c56-c52c-4715-8875-99c8c33660c6") { //for functional Halls
            $("#cost_categories1").hide();
            $("#average_population1").show();
            $("#unit_cost1").show();
        } else {
            $("#cost_categories1").show();
            $("#average_population1").hide();
            $("#unit_cost1").hide();
        }
    });
</script>
