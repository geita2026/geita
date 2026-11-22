<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsorship</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Home</a></li>
                <li class="active">View All packages</li>
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
                <span class=" text-danger">
                    @error('permission_name')
                        {{ $message }}
                    @enderror
                </span>
                <div class=" form-group text-right">
                    <a href="{{ route('sponsorships.create') }}" class=" btn btn-default text-primary"><i
                            class=" icon-plus2"></i> Add Package </a>
                </div>
                <h4 class=" text-bold">LIST OF REGISTERED SPONSORSHIP PACKAGES</h4>
                <div class=" form-group col-md-12">
                    <form action="{{ route('sponsorships.index') }}" method="POST">
                        @csrf
                        @method('GET')
                        <select name="packageGroup" class=" form-control" onchange="this.form.submit()">
                            <option value="">- - - Select Package Group - - -</option>
                            <option value="NULL">UNASSIGNED PACKAGES</option>
                            @foreach ($packageGroups as $item)
                                <option value="{{ $item->packageGroupId }}">{{ $item->packageGroupName }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class=" form-group col-md-12">
                    <table data-table-paginated
                        class="table table-sm datatable-button-html5-columns table-bordered table-striped table-responsive">
                        <thead>

                            <tr>
                                <th class=" text-bold text-center">#</th>
                                <th class=" text-bold text-center">Package Name</th>
                                <th class=" text-bold text-center"> Package Cost (TZS)</th>
                                <th class=" text-bold text-center">Package Contents</th>
                                <th class=" text-bold text-center">Package Status</th>
                                <th class=" text-bold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($packages as $key => $item)
                                <tr>

                                    <td title="{{ $item->gfs_code }}">{{ $key + 1 }}</td>
                                    <td>{{ $item->package_name }}</td>
                                    <td class=" text-bold text-center">{{ number_format($item->package_amount) }}
                                        {{ $item->type_of_currency }}</td>

                                    <td class=" text-center"><a
                                            href="{{ route('sponsorships.show', ['sponsorship' => $item->package_id]) }}"
                                            class=" btn btn-default text-primary">Benefits <i
                                                class=" icon-plus2"></i></a></td>
                                    <td class=" text-center">
                                            <label class=" label @if($item->packageStatus=='PUBLISHED') label-success @else label-default @endif  p-10">{{ $item->packageStatus }}</label>
                                        
                                    </td>
                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li>
                                                        <a data-toggle="modal"
                                                            data-target="#edit{{ $item->package_id }}"
                                                            class="text-primary"> <i class=" icon-pencil"></i>
                                                            Update Package</a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('sponsorships.show', ['sponsorship' => $item->package_id]) }}"
                                                            class="text-primary"><i class=" icon-add"></i> Add
                                                            Benefits</a>
                                                    </li>
                                                    <li>
                                                        <a href="" data-toggle="modal"
                                                            data-target="#delete{{ $item->package_id }}"
                                                            class="text-danger"> Delete Package <i
                                                                class=" icon-bin"></i></a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>

                                    {{-- edit role modal --}}
                                    <div id="edit{{ $item->package_id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title text-uppercase">
                                                        <b>{{ $item->package_name }} [ {{ number_format($item->package_amount) }} {{ $item->type_of_currency }}] - {{ $item->packageGroupName }}</b></h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('sponsorships.update', ['sponsorship' => $item->package_id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="">
                                                            <div class=" row">
                                                                <div class=" form-group col-md-6">
                                                                    <label>Package name<i
                                                                            class=" text-danger">*</i></label>
                                                                    <input type="text" name="package_name"
                                                                        class=" form-control"
                                                                        placeholder="Sponsorship Package Name"
                                                                        value="{{ $item->package_name }}">
                                                                    <span class="text-danger"> @error('package_name')
                                                                            {{ $message }}
                                                                        @enderror
                                                                    </span>
                                                                </div>
                                                                <div class=" form-group col-md-6">
                                                                    <label>Type Of Currency<i
                                                                            class=" text-danger">*</i></label>
                                                                    <select name="type_of_currency"
                                                                        class=" form-control">
                                                                        <option value="{{ $item->type_of_currency }}">
                                                                            {{ $item->type_of_currency }}</option>
                                                                        <option value="TZS">TZS</option>
                                                                        <option value="USD">USD</option>
                                                                    </select>
                                                                    <span class="text-danger">
                                                                        @error('type_of_currency')
                                                                            {{ $message }}
                                                                        @enderror
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class=" row">
                                                                <div class=" form-group col-md-6">
                                                                    <label>Package Group<i
                                                                            class=" text-danger">*</i></label>
                                                                    <select name="packageGroup" class=" form-control"
                                                                        >
                                                                        <option value="{{ $item->packageGroup }}">{{ $item->packageGroupName }}</option>
                                                                        @foreach ($packageGroups as $data)
                                                                            <option
                                                                                value="{{ $data->packageGroupId }}">
                                                                                {{ $data->packageGroupName }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class=" form-group col-md-6">
                                                                    <label>Package Status<i
                                                                            class=" text-danger">*</i></label>
                                                                    <select name="packageStatus"
                                                                        class=" form-control">
                                                                        <option value="{{ $item->packageStatus }}">{{ $item->packageStatus }} </option>
                                                                        <option value="PUBLISHED">PUBLISHED</option>
                                                                        <option value="UNPUBLISHED">UNPUBLISHED</option>
                                                                    </select>
                                                                    <span class="text-danger"> @error('packageStatus')
                                                                            {{ $message }}
                                                                        @enderror
                                                                    </span>
                                                                </div>

                                                            </div>
                                                            <div class=" row">

                                                            
                                                            <div class=" form-group col-md-12">
                                                                <label>Package Description</label>
                                                                <textarea name="package_description" class=" form-control" placeholder="Package Descriptions">{{ $item->package_description }}</textarea>
                                                                <span class="text-danger">
                                                                    @error('package_description')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>
                                                            <div class=" form-group col-md-12 text-right">
                                                                <button type="button" class="btn btn-link"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary btn-ladda btn-ladda-spinner">Save
                                                                    changes</button>
                                                            </div>
                                                        </div>
                                                        </div>

                                                </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /edit role modal -->
                                    {{-- delete modal --}}
                                    <div id="delete{{ $item->package_id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <h5 class="modal-title">Are you sure you whant to delete
                                                        <b>{{ $item->package_name }}</b> Sponsorship Package ?</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('sponsorships.destroy', ['sponsorship' => $item->package_id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
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

                <div class=" form-group col-md-12 text-right">
                    {{ $packages->withQueryString()->links() }}

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
