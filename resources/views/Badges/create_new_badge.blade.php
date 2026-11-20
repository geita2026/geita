<x-app-layout>
    <title>{{ env('APP_NAME') }} | Badges</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i>Home
                    </a></li>
                <li class="active">View All exhibitor Badges</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->
    <!-- Content area -->
    <div class="content" id="myTable">
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

                {{-- UPLOAD BADGES LOGICS --}}
                @include('Badges.BadgeLayouts.BadgeUploadLogic')
                {{-- END UPLOAD LOGICS --}}

               @can('search-badges')
                    <div class=" form-group">
                    <form action="{{ route('exhibitors_list.edit',['exhibitors_list'=>session('companyID')]) }}" method="POST">
                        @csrf
                        @method('GET')
                        <div class=" col-md-10">
                            <input type="text" name="search" placeholder="Type either Company Name, frist name, last name or phone number" class=" form-control" required>
                        </div>
                        <div class=" col-md-2">
                            <button type="submit" class=" btn btn-primary btn-block">Search <i class=" icon icon-search4"></i></button>
                        </div>
                    </form>
                </div>
               @endcan
                <div class=" col-md-12 form-group table-responsive">
                    <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>

                            <tr>
                                <th class=" text-bold text-center">#</th>
                                <th class=" text-bold text-center">Member Name</th>
                                <th class=" text-bold text-center"> Member Photo</th>
                                <th class=" text-bold text-center">Company Name</th>
                                <th class=" text-bold text-center">Member Group</th>
                                <th class=" text-bold text-center">Badge Status</th>
                                <th class=" text-bold text-center">Actions</th>
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
                                        @if ($item->base64Image)
                                            <a data-toggle="modal" data-target="#edit_member{{ $item->member_id }}">
                                                @if ($item->member_image != null)
                                                    <img src="data:image/png;base64,{{ $item->base64Image }}"
                                                        alt="Base64 Image" height="50" width="50">
                                                @else
                                                    <i class=" icon-user"></i>
                                                @endif
                                            </a>
                                        @else
                                            <a data-toggle="modal" data-target="#edit_member{{ $item->member_id }}">
                                                @if ($item->member_image != null)
                                                    <img src="{{ $item->member_image }}" height="50" width="50"
                                                        class=" img-rounded">
                                                @else
                                                    <i class=" icon-user"></i>
                                                @endif
                                            </a>
                                        @endif



                                    </td>
                                    <td>
                                        @php $j=0; @endphp
                                        @foreach ($associates as $value)
                                            @if ($value->companyID == $item->associateCompany)
                                                {{ $value->companyName }}
                                            @endif

                                            @php $j++; @endphp
                                        @endforeach
                                        @if ($j == 0)
                                            {{ $item->companyName }}
                                        @elseif ($item->associateCompany == null)
                                            {{ $item->companyName }}
                                        @endif

                                    </td>
                                    <td class=" text-center"><b
                                            style="background-color: {{ $item->group_background_color }};color:{{ $item->text_color }};"
                                            class=" p-20">{{ $item->group_name }}</b></td>

                                    <td class=" text-center">
                                        @if ($item->badge_status == 'PRINTED')
                                            <div class=" label label-success p-10">{{ $item->badge_status }}</div>
                                        @elseif ($item->badge_status == 'UNPRINTED')
                                            <div class=" label label-warning p-10">{{ $item->badge_status }}</div>
                                        @elseif ($item->badge_status == 'BLOCKED')
                                            <div class=" label label-warning p-10">{{ $item->badge_status }}<i class=" icon-blocked"></i></div>
                                            @elseif ($item->badge_status == 'UNBLOCKED')
                                            <div class=" label label-default p-10">{{ $item->badge_status }}</div>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li>
                                                        <a href="{{ route('badge_preview', ['member_id' => $item->member_id]) }}"
                                                           target="_blank" class="text-info">
                                                            Preview Badge <i class="icon-eye"></i>
                                                        </a>
                                                    </li>
                                                    @if ($item->badge_status == 'UNPRINTED')
                                                        @can('Update Member Badges')
                                                            @if (Auth::user()->account_type == 1 || $item->badge_status == 'UNPRINTED')
                                                                <li>
                                                                    <a data-toggle="modal"
                                                                        data-target="#edit_member{{ $item->member_id }}"
                                                                        class="text-primary">Edit Member <i
                                                                            class=" icon-pencil"></i></a>
                                                                </li>
                                                            @endif
                                                        @endcan
                                                        @can('Delete Member Badges')
                                                            @if (Auth::user()->account_type == 1 || $item->badge_status == 'UNPRINTED')
                                                                <li>
                                                                    <a data-toggle="modal"
                                                                        data-target="#delete_permission{{ $item->member_id }}"
                                                                        class="text-danger"> Delete Member <i
                                                                            class=" icon-bin"></i></a>
                                                                </li>
                                                            @endif
                                                        @endcan
                                                        @can('Block Member Badges')
                                                            <li>
                                                                <a data-toggle="modal"
                                                                    data-target="#Block{{ $item->member_id }}"
                                                                    class=" text-slate">Block Member <i
                                                                        class="icon-spam"></i></a>
                                                            </li>
                                                        @endcan
                                                    @else
                                                        @can('UnBlock Member Badges')
                                                            <li>
                                                                <a data-toggle="modal"
                                                                    data-target="#UnBlock{{ $item->member_id }}"
                                                                    class=" text-success text-bold"> UnBlock Member <i
                                                                        class=" icon-user-check"></i></a>
                                                            </li>

                                                            @can('Revert Badges')
                                                                <li>
                                                                    <a data-toggle="modal"
                                                                        data-target="#edit_member{{ $item->member_id }}"
                                                                        class="text-primary">Edit Member <i
                                                                            class=" icon-pencil"></i></a>
                                                                </li>
                                                            @endif
                                                        @endcan
                                @endif

                                </ul>
                                </li>
                                </ul>
                                </td>

                                {{-- edit  modal --}}
                                <div id="edit_member{{ $item->member_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">

                                                <h5 class="modal-title text-uppercase text-center">Edit
                                                    <b>{{ $item->first_name }} {{ $item->last_name }}</b> Badge
                                                </h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('exhibitors_list.update', ['exhibitors_list' => $item->member_id]) }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')

                                                    {{-- ******** FOR ASSOCIATE COMPANIES --}}

                                                    @php $j=0; @endphp

                                                    @foreach ($associates as $associate)
                                                        @php $j++; @endphp
                                                    @endforeach
                                                    @if ($j > 0)
                                                        <div class=" form-group col-md-12">
                                                            <label>Select Associate Company<i
                                                                    class=" text-danger">*</i></label>
                                                            <select name="associateCompany" class=" form-control">
                                                                <option value="">- - - Select Associate
                                                                    Company - - - </option>
                                                                @foreach ($associates as $associate)
                                                                    @if ($associate->companyID == $item->associateCompany)
                                                                        <option value="{{ $associate->companyID }}">
                                                                            {{ $associate->companyName }}</option>
                                                                    @endif


                                                                    <option value="{{ $associate->companyID }}">
                                                                        {{ $associate->companyName }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @else
                                                    @endif
                                                    {{-- *END FOR ASSOCIATE COMPANIES --}}

                                                    <div class=" row ">
                                                        <div class=" form-group col-md-6">
                                                            <label>First Name<i class=" text-danger">*</i></label>
                                                            <input type="text" name="first_name" class=" form-control"
                                                                placeholder=" First Name" value="{{ $item->first_name }}"
                                                                required>
                                                            {{-- <span class="text-danger"> @error('first_name'){{ $message }}@enderror</span> --}}
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Middle Name</label>
                                                            <input type="text" name="middle_name"
                                                                class=" form-control" placeholder=" Middle Name"
                                                                value="{{ $item->middle_name }}">
                                                            <span class="text-danger"> @error('middle_name')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class=" row">
                                                        <div class=" form-group col-md-6">
                                                            <label>Last Name<i class=" text-danger">*</i></label>
                                                            <input type="text" name="last_name" class=" form-control"
                                                                placeholder=" Last Name" value="{{ $item->last_name }}"
                                                                required>
                                                            <span class="text-danger"> @error('last_name')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Phone Number<i class=" text-danger">*</i></label>
                                                            <input type="text" name="phone_number"
                                                                class=" form-control" placeholder="255.........."
                                                                value="{{ $item->phone_number }}" required>
                                                            <span class=" text-muted text-small">'ll be used in
                                                                Badges recoverly when lost</span>
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Member Group<i class=" text-danger">*</i></label>
                                                            <select name="member_group" class="form-control"
                                                                data-live-search="true" data-width="100%"
                                                                @if (Auth::user()->account_type == 2) disabled @endif required>
                                                                <option value="{{ $item->member_group }}">
                                                                    {{ $item->group_name }}</option>
                                                                @foreach ($groups as $group)
                                                                    <option value="{{ $group->group_id }}">
                                                                        {{ $group->group_name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="text-danger"> @error('member_group')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label>Member Pasport Size Image<i
                                                                    class=" text-danger">*</i></label>
                                                            <input type="file" name="member_image"
                                                                class=" form-control">
                                                            <span class="help-block">Image formart must be either
                                                                png,jpg or jpeg</span>
                                                        </div>

                                                    </div>




                                                    <div class=" row">
                                                        <div class=" form-group col-md-12">
                                                            <img src="{{ $item->member_image }}" height="50"
                                                                width="50" class=" img-rounded">
                                                            <br><span class=" text-small text-muted">Current
                                                                Photo</span>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <input type="hidden" name="existing_image"
                                                            value="{{ $item->member_image }}">
                                                        <input type="hidden" name="company_id"
                                                            value="{{ $item->companyID }}">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit"
                                                    class="btn btn-primary  btn-ladda btn-ladda-spinner">Save
                                                    changes</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- /edit modal -->

                                {{-- delete modal --}}
                                <div id="delete_permission{{ $item->member_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete
                                                    <b>{{ $item->first_name }} {{ $item->last_name }}</b>
                                                    Informations
                                                </h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('exhibitors_list.destroy', ['exhibitors_list' => $item->member_id ?? 0]) }}"
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


                                {{-- Block modal --}}
                                <div id="Block{{ $item->member_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-slate">
                                                <h5 class="modal-title">Are you sure you whant to Block
                                                    <b>{{ $item->first_name }} {{ $item->last_name }}</b> Badge ?
                                                </h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('exhibitors_list.update', ['exhibitors_list' => $item->member_id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="source" value="1">
                                                    <div class=" text-right">
                                                        <button type="button" data-dismiss="modal"
                                                            class=" btn btn-secondary">Close</button>
                                                        <button type="submit" class="btn bg-slate ml-20">Yes,
                                                            Block Badge</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- End block modal --}}

                                {{-- Block modal --}}
                                <div id="UnBlock{{ $item->member_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success">
                                                <h5 class="modal-title">Are you sure you whant to UnBlock
                                                    <b>{{ $item->first_name }} {{ $item->last_name }}</b> Badge ?
                                                </h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('exhibitors_list.update', ['exhibitors_list' => $item->member_id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="source" value="2">
                                                    <div class=" text-right">
                                                        <button type="button" data-dismiss="modal"
                                                            class=" btn btn-secondary">Close</button>
                                                        <button type="submit" class="btn btn-success ml-20">Yes,
                                                            UnBlock Badge</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- End block modal --}}

                                </tr>
                                @php $i++; @endphp
                                @endforeach
                            </tbody>
                        </table>

                        <div class=" form-group text-right">
                            {{ $members->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /dashboard content -->
        {{-- add role modal --}}
        <form action="{{ route('exhibitors_list.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="addBadge" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <h5 style="font-size:1.5vmax;" class="modal-title text-uppercase text-center text-bold">Add
                                New Member Badge Information.</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="company_id" value="{{ session('companyID') }}">
                            @php $i=0; @endphp
                            @foreach ($associates as $item)
                                @php $i++; @endphp
                            @endforeach
                            @if ($i > 0)
                                <div class=" row form-group ">
                                    <div class=" form-group col-md-12">
                                        <label>Select Specific Company<i class=" text-danger">*</i></label>
                                        <select name="associateCompany" class="bootstrap-select" data-live-search="true"
                                            data-width="100%">
                                            <option value="">- - - Select Associate Company - - - </option>
                                            @foreach ($associates as $item)
                                                <option value="{{ $item->companyID }}">{{ $item->companyName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class=" row form-group">
                                <div class=" form-group col-md-6">
                                    <label>First Name<i class=" text-danger">*</i></label>
                                    <input type="text" name="first_name" class=" form-control"
                                        placeholder=" First Name" value="{{ old('first_name') }}" required>
                                    <span class="text-danger first_name_error"></span>
                                </div>
                                <div class=" form-group col-md-6">
                                    <label>Middle Name</label>
                                    <input type="text" name="middle_name" class=" form-control"
                                        placeholder=" Middle Name" value="{{ old('middle_name') }}">
                                    <span class="text-danger middle_name_error"></span>
                                </div>
                            </div>
                            <div class=" row form-group">
                                <div class=" form-group col-md-6">
                                    <label>Last Name<i class=" text-danger">*</i></label>
                                    <input type="text" name="last_name" class=" form-control"
                                        placeholder=" Last Name" value="{{ old('last_name') }}" required>
                                    <span class="text-danger">
                                        @error('last_name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <div class=" form-group col-md-6">
                                    <label>Phone Number<i class=" text-danger">*</i> (<span
                                            class=" text-muted text-small">'ll be used in Badges recoverly when
                                            lost</span>)</label>
                                    <input type="text" name="phone_number" class=" form-control"
                                        placeholder="255xxxxxxxxx" value="" required>
                                    <span class="text-danger phone_number_error"></span>
                                </div>
                            </div>
                            <div class=" row form-group">
                                <div class=" form-group col-md-6">
                                    <label>Member Group<i class=" text-danger">*</i></label>
                                    <select name="member_group" class=" form-control" required>
                                        <option value="">- - - Select Badge Group - - -</option>
                                        @foreach ($groups as $item)
                                            <option value="{{ $item->group_id }}">{{ $item->group_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger member_group_error"></span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Member Pasport Size Image<i class=" text-danger">*</i> <span
                                            class=" text-muted">(width 255px X Height 255px)</span></label>
                                    <input type="file" name="member_image" class="file-input"
                                        data-browse-class="btn btn-primary" data-remove-class="btn btn-default"
                                        accept="image/*" required>
                                    <span class="text-danger member_image_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <img src="{{ asset('images/loading2.gif') }}" id="loader" style="display: none;"
                                height="100" width="100">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                    class="ladda-label">Submit
                                    Information</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        {{--  end  --}}
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


    <script>
        $(document).ready(function() {
            var form = '#AddBadge';
            $(form).on('submit', function(event) {
                event.preventDefault();
                var url = $(this).attr('data-action');
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('#AddBadge').find('span.text-danger').text('');
                    },
                    success: function(response) {
                        if (response.status == 0) {
                            $.each(response.error, function(prefix, val) {
                                $(form).find('span.' + prefix + '_error').text(val[0]);
                            });
                        }
                        if (response.status == 1) {
                            $(document).ready(function() {
                                new PNotify({
                                    title: 'Congratulations!',
                                    text: response.message,
                                    icon: 'icon-info3',
                                    type: 'success'
                                });

                            });
                            $('#addBadge').modal('toggle');
                            setTimeout(() => {
                                location.reload();
                            }, 200);
                        }


                        if (response.status == 2) {
                            $(document).ready(function() {
                                new PNotify({
                                    title: 'Sorry, {{ Auth::user()->first_name }}',
                                    text: response.fail,
                                    icon: 'icon-warning',
                                    type: 'danger'
                                });

                            });
                            $('#addBadge').modal('toggle');

                            setTimeout(() => {
                                // document.location.reload();
                            }, 500);
                        }


                    }
                });
            });

        });
    </script>


    <script type="text/javascript">
        function showLoader() {
            document.getElementById("loader").style.display = 'block';
            document.getElementById('submitButton').style.display = "none";
        }
    </script>
