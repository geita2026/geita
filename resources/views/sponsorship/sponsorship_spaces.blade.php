<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsorship Spaces</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a><i class="icon-home9 position-left"></i>Home</a></li>
                <li class="active"> Sponsorship spaces</li>
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
                <h6 class="panel-title text-bold">SPACES FOR EXHIBITION SPONSORS</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body">

                <form action="{{ route('sponsorship_spaces.store') }}" method="POST">
                    @csrf
                    <div class=" row">
                        <div class=" form-group col-md-5">
                            <label>Available Spaces</label>
                            <div class="multi-select-full">
                                <select name="pavilion_name[]" class="multiselect-filtering" multiple="multiple">
                                    @foreach ($pavilions as $item)
                                        <option value="{{ $item->pavilionID }}">{{ $item->pavilionName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="text-danger"> @error('pavilion_category')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="form-group col-md-5">
                            <label>Package To Be mapped with<i class=" text-danger">*</i></label>
                            <div class="multi-select-full">
                                <select name="packageId" class="form-control">
                                    @foreach ($packages as $item)
                                        <option value="{{ $item->package_id }}">{{ $item->package_name }} -
                                            {{ number_format($item->package_amount) }}{{ $item->type_of_currency }}
                                            [{{ $item->packageGroupName }}]</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="text-danger"> @error('pavilion_category')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class=" text-right col-md-2 mt-20">
                            <button type="submit" class="btn btn-success btn-ladda btn-ladda-spinner">Add
                                Space</button>
                        </div>
                    </div>

                </form>


                {{-- user registration here --}}
                <div class=" row">
                    {{-- booths table --}}
                    <table
                        class="table datatable-button-html5-columns table-bordered table-striped table-responsive table-lg text-nowrap">
                        <thead>

                            <tr>
                                <th colspan="6" class=" text-center text-bold text-capitalize"> LIST OF SPACES
                                    FOR SPONSORSHIP</b>.</th>
                            </tr>

                            <tr>
                                <th class=" text-center text-bold text-capitalize">PACKAGE NAME</th>
                                <th class=" text-center text-bold text-capitalize">SPACE NAME</th>
                                <th class=" text-center text-bold text-capitalize">SPACE Size (M<sup>2</sup> /
                                    Units)</th>
                                <th class=" text-center text-bold text-capitalize">MAKED TO</th>
                                <th class=" text-center text-bold text-capitalize">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($sponsor_pavilions as $item)
                                <tr>
                                    <td>{{ $item->package_name }}</td>
                                    <td>
                                        <a href="{{ route('pavilions.edit',['pavilion'=>$item->pavilionID]) }}">
                                            <b>{{ $item->pavilionName }}</b> <span class=" text-muted">@php echo Str::limit($item->pavilionDescription, 100, '...') @endphp</span>
                                        </a>
                                    </td>
                                    <td class=" text-center">{{ number_format($item->pavilion_size, 2) }}</td>
                                    <td class=" text-right">
                                        {{ number_format($item->package_amount) }}
                                        {{ $item->type_of_currency }}</td>
                                    {{--  action selector  --}}
                                    <td class="text-center">
                                        <a href="" data-toggle="modal" data-target="#delete{{ $item->booking_id }}"
                                            class="text-danger text-center"> Unmark Space <i
                                                class=" icon-bin"></i>
                                            </a>
                                    </td>
                                    {{--  //end action selector  --}}


                                    
                                    {{-- delete modal --}}
                                    <div id="delete{{ $item->booking_id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you whant to romove
                                                        {{ $item->boothNo }} from sponsor List ?</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('sponsorship_spaces.destroy', ['sponsorship_space' => $item->booking_id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class=" text-right">
                                                            <input type="hidden" name="booth" value="booth">
                                                            <input type="hidden" name="space_id"
                                                                value="{{ $item->space_id }}">
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

                            @php
                                $i = 1;
                            @endphp

                        </tbody>

                    </table>
                    {{-- //booths table --}}
                </div>
            </div>
        </div>
        {{-- //user registration  --}}
</x-app-layout>

<script type="text/javascript" src="{{ asset('assets/js/pages/form_multiselect.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
