<x-app-layout>
    <title>{{ env('APP_NAME') }} | Company Users</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-office position-left"></i>Home</a></li>
                <li class="active">View All Company Users</li>
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
                <h6 class="panel-title text-bold"> REGISTERED COMPANY USERS</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body table-responsive">
                <div class="form-group">
                    <table class=" table table-bordered table-striped datatable-button-html5-columns">
                        <thead>
                            <tr>
                                <th class=" text-bold text-center">S/N</th>
                                <th class=" text-bold text-center">USER NAME</th>
                                <th class=" text-bold text-center">COMPANY</th>
                                <th class="text-bold text-center">USER PHONE</th>
                                <th class=" text-bold text-center">USER EMAIL</th>
                                <th class=" text-bold text-center">USER STATUS</th>
                                <th class=" text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php $i=1; @endphp
                            @forelse ($users as $item)
                                @php $rowId = $item->user_uuid . '-' . $item->companyID; @endphp
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td class=" text-capitalize">
                                        <a
                                            href="{{ route('company_users.edit', ['company_user' => $item->user_uuid]) }}">{{ $item->first_name }}
                                            {{ $item->last_name }} </a>
                                    </td>
                                    <td>{{ $item->companyName }}</td>
                                    <td>{{ $item->phone_number }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td class=" text-center">
                                        @if ($item->account_status == 1)
                                            <label class=" label label-success">ACTIVE</label>
                                        @elseif($item->account_status==0)
                                            <label class=" label label-danger">SUPENDED</label>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    @can('UPDATE_COMPANY_USER')
                                                        <li>
                                                            <a href="{{ route('company_users.edit', ['company_user' => $item->user_uuid]) }}"
                                                                class=" text-primary"><b><i class=" icon-pencil7"></i>
                                                                    Update
                                                                    User</b></a>
                                                        </li>
                                                    @endcan
                                                    @can('SUNSPEND_USER_ACCOUNT')
                                                        <li>
                                                            <a data-toggle="modal"
                                                                data-target="#SunspendAccount{{ $rowId }}">
                                                                @if ($item->account_status == 1)
                                                                    <b class=" text-danger"><i class=" icon-user-block"></i>
                                                                        Sunspend Account</b>
                                                                @elseif ($item->account_status == 0)
                                                                    <b class=" text-success"><i
                                                                            class=" icon-user-check"></i> Activate
                                                                        Account</b>
                                                                @endif
                                                            </a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>


                                {{-- update company --}}
                                <div id="updateUser{{ $rowId }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info-400">
                                                <h4 class="modal-title text-bold text-center text-uppercase">Update
                                                    {{ $item->first_name }} {{ $item->last_name }}'S DETAILS</h5>
                                            </div>
                                            <form
                                                action="{{ route('company_users.update', ['company_user' => $item->user_uuid]) }}"
                                                method="post">
                                                @csrf
                                                @method('PUT')
                                                <div class=" modal-body">
                                                    <div class=" row">
                                                        <div class="form-group col-md-6">
                                                            <label>First Name<i class=" text-danger">*</i></label>
                                                            <input type="text" name="first_name"
                                                                class=" form-control input-lg" placeholder="First Name"
                                                                value="{{ $item->first_name }}">
                                                            <span class="text-danger"> @error('first_name')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Middle Name</label>
                                                            <input type="text" name="middle_name"
                                                                class=" form-control input-lg" placeholder="Middle Name"
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
                                                            <input type="text" name="last_name"
                                                                class=" form-control input-lg" placeholder="Last  name"
                                                                value="{{ $item->last_name }}">
                                                            <span class="text-danger"> @error('last_name')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>user phone<i class=" text-danger">*</i></label>
                                                            <input type="text" name="phone_number"
                                                                class=" form-control input-lg"
                                                                placeholder=" start with: 255..., 254...., etc"
                                                                value="{{ $item->phone_number }}">
                                                            <span class="text-danger"> @error('phone_number')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class=" row">
                                                        <div class=" form-group col-md-6">
                                                            <label>user Email<i class=" text-danger">*</i></label>
                                                            <input type="text" name="email" class=" form-control"
                                                                placeholder=" user email" value="{{ $item->email }}">
                                                            <span class="text-danger"> @error('email')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                        <div class=" col-md-6">
                                                            <label>User Role</label>
                                                            <input type="text" name="roleID"
                                                                value="{{ $item->name }}" class=" form-control"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <div class=" row">
                                                        <div class="col-md-12 form-group">
                                                            <label>User Company<span
                                                                    class=" text-danger">*</span></label>
                                                            <select name="myCompany" class=" form-control"
                                                                data-live-search="true" data-width="100%">
                                                                <option value="{{ $item->myCompany }}">
                                                                    {{ $item->companyName }}</option>
                                                                @foreach ($companies as $company)
                                                                    <option value="{{ $company->companyID }}">
                                                                        {{ $company->companyName }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class=" modal-footer">
                                                    <div class=" text-right">
                                                        <button class=" btn btn-default border-warning text-warning"
                                                            data-dismiss="modal"> Close</button>
                                                        <button type="submit"
                                                            class="btn btn-info  btn-ladda btn-ladda-spinner"><span
                                                                class="ladda-label">Save Changes</span></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- update company --}}
                                <div id="SunspendAccount{{ $rowId }}" class="modal fade"
                                    aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <h4 class="modal-title text-bold text-center text-uppercase">
                                                    {{ $item->first_name }} {{ $item->last_name }}'S ACCOUNT</h5>
                                            </div>
                                            <div class=" modal-body">
                                                <form
                                                    action="{{ route('company_users.destroy', ['company_user' => $item->user_uuid]) }}"
                                                    method="post" id="company">
                                                    @csrf
                                                    @method('DELETE')
                                            </div>
                                            <div class=" modal-footer">
                                                <div class=" text-right">
                                                    <button class=" btn btn-default border-warning text-warning"
                                                        data-dismiss="modal"> Close</button>
                                                    <button type="submit"
                                                        class="btn btn-ladda btn-ladda-spinner"><span
                                                            class="ladda-label">
                                                            @if ($item->account_status == 1)
                                                                <b class=" text-danger"><i
                                                                        class=" icon-user-block"></i> Sunspend
                                                                    Account</b>
                                                            @elseif ($item->account_status == 0)
                                                                <b class=" text-success"><i
                                                                        class=" icon-user-check"></i> Activate
                                                                    Account</b>
                                                            @endif
                                                        </span></button>
                                                </div>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @php $i++; @endphp
                            @empty
                                <tr><td colspan="7" class="text-center text-muted">No company users found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div id="p1"></div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
