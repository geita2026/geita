<x-app-layout>
    <title> {{ env('APP_NAME') }} | Countries </title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('countries.index') }}"><i class=" icon-accessibility position-left"></i> Countries</a></li>
                <li class="active">View All  countries</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">

            <div class=" panel-body">
                <div class=" form-group table-header text-center">
                    <h6 class=" text-bold">REGISTERED  COUNTRIES</h6>
                </div>
                <div class=" form-group text-right">
                    <a  data-toggle="modal" data-target="#addCountry" class=" btn btn-success">Add Country <i class=" icon-add-to-list"></i></a>
                </div>

                <table class="table datatable-button-html5-columns table-bordered table-striped  table-lg text-nowrap">
                    <thead>
                        <tr>
                            <th class=" text-bold text-center">#</th>
                            <th class=" text-bold text-center">Country NAME</th>
                            <th class=" text-bold text-center">SHORT CODE</th>
                            <th class=" text-bold text-center">PHONE CODE</th>
                            <th class=" text-bold text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($countries as  $item)
                        <tr>
                            <td>{{ $i }}</td>
                            <th class=" text-capitalize">{{ $item->country_name }}</th>
                            <td>{{ $item->country_short_code }}</td>
                            <td class=" text-center">{{ $item->phone_code }}</td>
                             {{--  action selector  --}}
                             <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a data-toggle="modal" data-target="#editCountry{{ $item->country_id }}" class=" text-primary">Edit Country <i class=" icon-pencil7"></i></a>
                                            </li>
                                            <li>
                                                <a href="" data-toggle="modal" data-target="#delete_role{{ $item->country_id }}"  class="text-danger">Delete Role <i class=" icon-bin"></i></a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>

                             {{-- edit country modal --}}

        <div id="editCountry{{ $item->country_id }}" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-blue">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title text-uppercase text-center">Edit {{ $item->country_name }}</h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('countries.update',['country'=>$item->country_id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                        <div class=" row form-group">
                            <div class="form-group col-md-12">
                                <label>Country Name<i class=" text-danger">*</i></label>
                                <input type="text" name="country_name" class=" form-control" placeholder=" Country Name" value="{{ $item->country_name }}" required>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Country Short Code (eg TZ for Tanzania)</label>
                                <input type="text" name="country_short_code" class=" form-control" placeholder=" Country Short Code" value="{{ $item->country_short_code }}">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Country Phone Code</label>
                                <input type="text" name="phone_code" class=" form-control" placeholder=" Country Phone Code" value="{{ $item->phone_code }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-ladda ladda-spinner">Add Country <i class=" icon-add-to-list"></i></button>
                    </div>
                </form>
                </div>
            </div>
        </div>



                            {{--  Action selector  --}}
                            <div id="delete_role{{ $item->country_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete {{ $item->country_name }} ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('countries.destroy', ['country' => $item->country_id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class=" text-right">
                                                    <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                    <button type="submit" class="btn btn-danger btn-ladda btn-ladda-spinner" data-style="slide-down"><span class="ladda-label">Yes, Delete</span></button>
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
    <!-- /dashboard content -->


    {{-- add country modal --}}
    <form action="{{ route('countries.store') }}" method="POST">
        @csrf
        <div id="addCountry" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title text-uppercase text-center">Add  countries.</h5>
                    </div>
                    <div class="modal-body">
                        <div class=" row">
                            <div class="form-group">
                                <label>Country Name<i class=" text-danger">*</i></label>
                                <input type="text" name="country_name" class=" form-control" placeholder=" Country Name" value="{{ old('country_name') }}" required>

                                <span class=" text-danger">@error('country_name') {{ $message }} @enderror</span>
                            </div>
                            <div class="form-group">
                                <label>Country Short Code (eg TZ for Tanzania)</label>
                                <input type="text" name="country_short_code" class=" form-control" placeholder=" Country Short Code" value="{{ old('country_short_code') }}">
                            </div>
                            <div class="form-group">
                                <label>Country Phone Code</label>
                                <input type="text" name="phone_code" class=" form-control" placeholder=" Country Phone Code" value="{{ old('phone_code') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-ladda btn-ladda-spinner">Add Country <i class=" icon-add-to-list"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
