<x-app-layout>
    <title>{{ env('APP_NAME') }} | Badges</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Home</a></li>
                <li class="active">View All exhibitor Badges</li>
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
            <div class=" panel-body">
                {{-- Error or success messages --}}
                @if ($errors->any())
                    <div class=" form-group col-md-12">
                        @foreach ($errors->all() as $error)
                            <span class=" text-danger">{{ $error }} ,</span>
                        @endforeach
                    </div>
                @endif
                {{-- //error messages --}}


                <div class=" col-md-12 form-group table-responsive">
                    <table data-table-paginated class="table  table-striped table-bordered  table-responsive">
                        <thead>

                            <tr>
                                <th class=" text-bold text-center">#</th>
                                <th class=" text-bold text-center">Member Name</th>
                                <th class=" text-bold text-center"> Member Photo</th>
                                <th class=" text-bold text-center">Member Group</th>

                                <th class=" text-bold text-center" colspan="2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($members as $item)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td class=" text-uppercase">
                                        {{ $item->first_name }} {{ $item->middle_name }} {{ $item->last_name }}
                                    </td>
                                    <td class="text-center">
                                        <a data-toggle="modal" data-target="#edit_member{{ $item->member_id }}">
                                            <img src="{{ $item->member_image }}" height="50" width="50"
                                                class=" img-rounded">
                                        </a>
                                    </td>

                                    <td class=" text-center"><b
                                            style="background-color: {{ $item->group_background_color }};color:{{ $item->text_color }};"
                                            class=" p-20">{{ $item->group_name }}</b></td>

                                    <td class=" text-center">
                                        <a class=" btn btn-success" data-toggle="modal"
                                            data-target="#edit_member{{ $item->member_id }}"
                                            class="text-primary">Re-use Badge<i class=" icon-arrow-right13"></i></a>
                                    </td>

                                    <td class="text-center">
                                        <a class=" btn btn-danger" data-toggle="modal"
                                            data-target="#DeleteBadge{{ $item->member_id }}" class="text-danger">
                                            Remove <i class=" icon-bin"></i></a>

                                    </td>

                                    {{-- edit  modal --}}
                                    <div id="edit_member{{ $item->member_id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">

                                                    <h5 class="modal-title text-uppercase text-center">CONFIRM
                                                        <b>{{ $item->first_name }} {{ $item->last_name }}'S</b>
                                                        INFORMATION
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('reuse_badges.update', ['reuse_badge' => $item->member_id]) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">

                                                        </div>
                                                        <div class=" row ">
                                                            <div class=" form-group col-md-6">
                                                                <label>First Name<i class=" text-danger">*</i></label>
                                                                <input type="text" name="first_name"
                                                                    class=" form-control" placeholder=" First Name"
                                                                    value="{{ $item->first_name }}" required>
                                                                {{-- <span class="text-danger"> @error('first_name'){{ $message }}@enderror</span> --}}
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>Middle Name</label>
                                                                <input type="text" name="middle_name"
                                                                    class=" form-control" placeholder=" Middle Name"
                                                                    value="{{ $item->middle_name }}">
                                                                {{-- <span class="text-danger"> @error('middle_name'){{ $message }}@enderror</span> --}}
                                                            </div>
                                                        </div>
                                                        <div class=" row">
                                                            <div class=" form-group col-md-6">
                                                                <label>Last Name<i class=" text-danger">*</i></label>
                                                                <input type="text" name="last_name"
                                                                    class=" form-control" placeholder=" Last Name"
                                                                    value="{{ $item->last_name }}" required>
                                                                {{-- <span class="text-danger"> @error('last_name'){{ $message }}@enderror</span> --}}
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>Phone Number<i class=" text-danger">*</i></label>
                                                                <input type="text" name="phone_number"
                                                                    class=" form-control" placeholder="255.........."
                                                                    value="{{ $item->phone_number }}">
                                                                <span class=" text-muted text-small">'ll be used in
                                                                    Badges recoverly when lost</span>
                                                            </div>
                                                        </div>


                                                        <div class=" row">
                                                            <div class=" form-group col-md-6">
                                                                <label>Member Group<i class=" text-danger">*</i></label>
                                                                <select name="member_group" class="form-control"
                                                                    data-live-search="true" data-width="100%"
                                                                    @if (Auth::user()->account_type == 2) disabled @endif
                                                                    required>
                                                                    <option value="{{ $item->member_group }}">
                                                                        {{ $item->group_name }}</option>
                                                                    @foreach ($groups as $group)
                                                                        <option value="{{ $group->group_id }}">
                                                                            {{ $group->group_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                {{-- <span class="text-danger"> @error('member_group'){{ $message }}@enderror</span> --}}
                                                            </div>
                                                            <div class="form-group col-md-6">
                                                                <img src="{{ $item->member_image }}" height="100"
                                                                    width="100" class=" img-rounded">
                                                            </div>
                                                        </div>



                                                        <div class="row">
                                                            <div class=" col-md-6">
                                                                <input type="hidden" name="existing_image"
                                                                    value="{{ $item->member_image }}">
                                                                <input type="hidden" name="company_id"
                                                                    value="{{ $item->companyID }}">
                                                            </div>
                                                        </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit"
                                                        class="btn btn-primary  btn-ladda btn-ladda-spinner">Move
                                                        Member<i class=" icon-arrow-right13"></i></button>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /edit modal -->

                                    {{-- delete modal --}}
                                    <div id="DeleteBadge{{ $item->member_id }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you whant to delete
                                                        <b>{{ $item->first_name }} {{ $item->last_name }}</b>
                                                        Information
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('reuse_badges.destroy', ['reuse_badge' => $item->member_id ?? 0]) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="member_image"
                                                            value="{{ $item->member_image }}">
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
                                @php $i++; @endphp
                            @endforeach
                            @if ($i == 1)
                                <tr>
                                    <td colspan="7">
                                        <p class=" alert alert-danger">
                                            <b>No previous Member badges available Now! <a href="{{ route('exhibitors_list.edit',['exhibitors_list'=>session('companyID')]) }}" class=" btn btn-default text-primary">Go to add new badge<i class=" icon-arrow-right13"></i></a></b>
                                        </p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class=" form-group col-md-12 text-right">
                    {{ $members->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->

</x-app-layout>


<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
<!-- /theme JS files -->

<script>
    $(function() {
        $('form').on('submit', function() {
            $('select').removeAttr('disabled');
        });
    });
</script>
{{--  --}}
