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
                <li class="active">View all booth categories</li>
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
            <div class=" panel-body table-responsive">
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <span class=" text-danger alert">{{ $error }} </span>
                    @endforeach
                @endif

                <div class=" panel-body">
                    <div class="row">
                        <div class="form-group text-right">
                            <a data-toggle="modal" data-target="#booth_category" class=" btn btn-primary">Add Booth
                                Category <i class=" icon-plus2"></i></a>
                        </div>
                    </div>

                    {{-- add booth modal --}}
                    <div id="booth_category" class="modal fade" aria-modal="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h5 class="modal-title text-uppercase text-bold text-center">REGISTER BOOTH CATEGORY
                                    </h5>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('boothCategories.store') }}" method="POST">
                                        @csrf
                                        <div class=" row">
                                            <div class=" form-group col-md-6">
                                                <label>Booth Category Name<i class=" text-danger">*</i></label>
                                                <input type="text" name="category_name" class=" form-control"
                                                    placeholder="Booth Category Name"
                                                    value="{{ old('booth_category') }}">
                                            </div>
                                            <div class=" form-group col-md-6">
                                                <label>Booth Cost (Per 1 SQM)<i class=" text-danger">*</i></label>
                                                <input type="text" name="booth_cost" class=" form-control"
                                                    placeholder="Booth Cost" value="{{ old('booth_cost') }}">
                                            </div>
                                            <div class=" form-group col-md-6">
                                                <label>Type OF Currency<i class=" text-danger">*</i></label>
                                                <select name="type_of_currency" class=" form-control">
                                                    <option value="">Select Currency . . . </option>
                                                    <option value="TZS">Tanzania Shiling (TZS)</option>
                                                    <option value="USD">United State Dollar (USD)</option>
                                                </select>
                                            </div>
                                            <div class=" form-group col-md-6">
                                                <label>Booth Category Code<i class=" text-danger">*</i></label>
                                                <input type="text" name="categoryCode" class=" form-control"
                                                    placeholder="Booth category Code" value="{{ old('categoryCode') }}">
                                            </div>
                                        </div>
                                        <div class=" text-right">
                                            <button type="button" data-dismiss="modal"
                                                class=" btn btn-secondary">Close</button>
                                            <button type="submit"
                                                class=" btn btn-primary btn-ladda btn-ladda-spinner ml-20">Register
                                                Booth Category</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- delete modal --}}


                    {{-- user registration here --}}
                    <div class=" row">
                        {{-- booths table --}}
                        <table
                            class="table datatable-button-html5-columns table-striped table-bordered  table-responsive">
                            <thead>

                                <tr>
                                    <th colspan="6" class="text-bold text-uppercase text-center"
                                        style="background-color: rgb(195, 195, 201);"> LIST OF BOOTH CATEGORIES AND
                                        THEIR DETAILS </th>
                                </tr>

                                <tr>
                                    <th class=" text-bold text-center">#</th>
                                    <th class=" text-bold text-center">Booth Category Name</th>
                                    <th class=" text-bold text-center">Booth Category Cost</th>
                                    <th class=" text-bold text-center">BoothCategoryCode</th>
                                    <th class=" text-bold text-center">Updated At</th>
                                    <th class=" text-bold text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($boothCategories as $item)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $item->category_name }}</td>
                                        <td class=" text-right">{{ number_format($item->category_cost, 2) }}
                                            {{ $item->type_of_currency }}</th>
                                        <td class=" text-center">{{ $item->categoryCode }}</td>
                                        <td class=" text-center">
                                            @if ($item->updated_at)
                                                {{ date('d-m-Y', strtotime($item->updated_at)) }}
                                            @endif
                                        </td>

                                        <td class=" text-center">
                                            <a data-toggle="modal" data-target="#e{{ $item->booth_category_id }}"> <i
                                                    class="icon-pencil7"></i></a>
                                            <a href="" data-toggle="modal"
                                                data-target="#d{{ $item->booth_category_id }}"
                                                class="text-danger ml-20"> <i class=" icon-bin"></i></a>
                                        </td>

                                        {{-- Edit modal --}}
                                        <div id="e{{ $item->booth_category_id }}" class="modal fade" aria-modal="true">

                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <button type="button" class="close"
                                                            data-dismiss="modal">&times;</button>
                                                        <h5 class="modal-title text-uppercase">EDIT BOOTH CATEGORY
                                                            DETAILS</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form
                                                            action="{{ route('boothCategories.update', ['boothCategory' => $item->booth_category_id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class=" row">
                                                                <div class=" form-group  col-md-6">
                                                                    <label>Booth Category Name<i
                                                                            class=" text-danger">*</i></label>
                                                                    <input type="text" name="category_name"
                                                                        class=" form-control"
                                                                        placeholder="Booth Category"
                                                                        value="{{ $item->category_name }}">
                                                                </div>
                                                                <div class=" form-group col-md-6">
                                                                    <label>Cost One Unit<i
                                                                            class=" text-danger">*</i></label>
                                                                    <input type="text" name="booth_cost"
                                                                        class=" form-control" placeholder="Booth Cost"
                                                                        value="{{ $item->category_cost }}">
                                                                </div>
                                                                <div class=" form-group col-md-6">
                                                                    <label>Type Of Currency<i
                                                                            class=" text-danger">*</i></label>
                                                                    <select name="type_of_currency"
                                                                        class=" form-control">
                                                                        <option value="{{ $item->type_of_currency }}">
                                                                            {{ $item->type_of_currency }}</option>
                                                                        <option value="TZS">Tanzania Shiling (TZS)
                                                                        </option>
                                                                        <option value="USD">United State Dollar
                                                                            (USD)</option>
                                                                    </select>
                                                                </div>
                                                                <div class=" form-group col-md-6">
                                                                    <label>Booth Category Code<i
                                                                            class=" text-danger">*</i></label>
                                                                    <input type="text" name="categoryCode"
                                                                        class=" form-control"
                                                                        placeholder="Booth category Code"
                                                                        value="{{ $item->categoryCode }}">
                                                                </div>
                                                            </div>
                                                            <div class=" text-right">
                                                                <button type="button" data-dismiss="modal"
                                                                    class=" btn btn-secondary">Close</button>
                                                                <button type="submit"
                                                                    class="btn  btn-success btn-ladda btn-ladda-spinner ml-20">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- edit modal --}}

                                        {{-- delete modal --}}
                                        <div id="d{{ $item->booth_category_id }}" class="modal fade"
                                            aria-modal="true">
                                            <div class="modal-dialog modal-md">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <button type="button" class="close"
                                                            data-dismiss="modal">&times;</button>
                                                        <h5 class="modal-title">Are you sure you whant to delete this
                                                            Category ?</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form
                                                            action="{{ route('boothCategories.destroy', ['boothCategory' => $item->booth_category_id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class=" text-right">
                                                                <button type="button" data-dismiss="modal"
                                                                    class=" btn btn-secondary">Close</button>
                                                                <button type="submit"
                                                                    class="btn btn-danger ml-20">Yes, Delete</button>
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
                </div>
            </div>
        </div>
        {{-- //user registration  --}}
</x-app-layout>
