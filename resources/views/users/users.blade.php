<x-app-layout>
    <title>{{ env('APP_NAME') }} | Users</title>

    {{-- ===== PAGE HEADER ===== --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('users.index') }}"><i class="icon-users2 position-left"></i>Users</a></li>
                <li class="active">View All Users</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    {{-- ===== CONTENT ===== --}}
    <div class="content">

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-styled-left alert-arrow-left">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===== TOOLBAR CARD (Search + Register) ===== --}}
        <div class="panel panel-white">
            <div class="panel-body">
                <div class="row" style="display:flex; align-items:center; flex-wrap:wrap; gap:10px;">

                    {{-- Search form (inline) --}}
                    <div class="col-md-8" style="flex:1 1 600px;">
                        <form action="{{ route('users.index') }}" method="GET"
                              style="display:flex; align-items:center; flex-wrap:nowrap; gap:8px; margin:0;">
                            <input type="search" id="user-table-search" name="userName"
                                   class="form-control"
                                   style="flex:1 1 260px; min-width:220px;"
                                   placeholder="Search by name, email or phone"
                                   value="{{ request('userName') }}">

                            <select name="user_type" id="user-table-type"
                                    data-native-select class="form-control"
                                    style="flex:0 0 180px;">
                                <option value="">All account types</option>
                                <option value="1" {{ request('user_type') == 1 ? 'selected' : '' }}>Internal users</option>
                                <option value="2" {{ request('user_type') == 2 ? 'selected' : '' }}>Exhibitors</option>
                            </select>

                            <button type="submit" class="btn btn-primary" style="flex:0 0 auto; white-space:nowrap;">
                                <i class="icon-search4"></i> Search
                            </button>

                            @if (request()->filled('userName') || request()->filled('user_type'))
                                <a href="{{ route('users.index') }}" class="btn btn-default"
                                   style="flex:0 0 auto; white-space:nowrap;">Clear</a>
                            @endif
                        </form>
                    </div>

                    {{-- Register button --}}
                    <div class="col-md-4 text-right" style="flex:0 0 auto; margin-left:auto;">
                        <a href="{{ route('users.create') }}" class="btn btn-primary" style="white-space:nowrap;">
                            <i class="icon-user-plus"></i> Register New User
                        </a>
                    </div>

                </div>
            </div>
        </div>

        {{-- ===== USERS TABLE CARD ===== --}}
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title text-uppercase text-bold">
                    <i class="icon-users position-left"></i> List of System Users
                </h6>
                <div class="heading-elements">
                    <span class="label label-info label-rounded">{{ $users->total() }} users</span>
                </div>
            </div>

            <div class="table-responsive">
                <table 
                       class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr class="text-bold text-uppercase">
                            <th style="width:50px">#</th>
                            <th>User Name</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-center" style="width:130px">Status</th>
                            <th class="text-center" style="width:80px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $key => $item)
                            <tr>
                                <td>{{ $users->firstItem() + $key }}</td>
                                <td>
                                    <strong>{{ $item->first_name }} {{ $item->middle_name }} {{ $item->last_name }}</strong>
                                </td>
                                <td>
                                    <a href="tel:{{ $item->phone_number }}">
                                        {{ trim(strrev(chunk_split(strrev($item->phone_number), 3, ' '))) }}
                                    </a>
                                </td>
                                <td><a href="mailto:{{ $item->email }}">{{ $item->email }}</a></td>
                                <td class="text-bold text-uppercase">{{ $item->role_name }}</td>
                                <td class="text-center">
                                    @if ($item->account_status == 1)
                                        <span class="label label-success label-block" title="Active">Active</span>
                                    @elseif ($item->account_status == 0)
                                        <span class="label label-danger label-block" title="Suspended">Suspended</span>
                                    @elseif ($item->account_status == 2)
                                        <span class="label label-default label-block" title="Unverified">Unverified</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                @can('UPDATE_USER')
                                                    <li>
                                                        <a data-toggle="modal" data-target="#editUser{{ $item->id }}" class="text-primary">
                                                            <i class="icon-pencil7"></i> Edit Account
                                                        </a>
                                                    </li>
                                                @endcan

                                                @can('RESERT_PASSWORD')
                                                    <li>
                                                        <a data-toggle="modal" data-target="#resetPassword{{ $item->user_uuid }}" class="text-primary">
                                                            <i class="icon-envelop2"></i> Reset Password
                                                        </a>
                                                    </li>
                                                @endcan

                                                @if ($item->account_type == 2 && $item->myCompany != null)
                                                    @can('VIEW_COMPANIES')
                                                        <li>
                                                            <a href="{{ route('companies.edit', ['company' => $item->myCompany]) }}" class="text-primary">
                                                                <i class="icon-office"></i> View Company
                                                            </a>
                                                        </li>
                                                    @endcan
                                                @endif

                                                @can('DELETE_USER')
                                                    <li class="divider"></li>
                                                    <li>
                                                        <a data-toggle="modal" data-target="#disableAccount{{ $item->user_uuid }}" class="text-warning">
                                                            <i class="icon-lock"></i> Suspend Account
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a data-toggle="modal" data-target="#delete_user{{ $item->user_uuid }}" class="text-danger">
                                                            <i class="icon-cross2"></i> Delete User
                                                        </a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="icon-info22 position-left"></i> No users found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="panel-footer">
                <div class="text-right">
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- MODALS — ZOTE NJE YA TABLE --}}
    {{-- ============================================================ --}}
    @foreach ($users as $item)

        {{-- ===== EDIT USER MODAL ===== --}}
        @can('UPDATE_USER')
            <div id="editUser{{ $item->id }}" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h5 class="modal-title text-uppercase">
                                Update <b>{{ $item->first_name }} {{ $item->last_name }}'s</b> Information
                            </h5>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('users.update', ['user' => $item->id]) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>First Name <i class="text-danger">*</i></label>
                                        <input type="text" name="first_name" class="form-control input-lg"
                                               placeholder="First Name" value="{{ $item->first_name }}">
                                        <span class="text-danger">@error('first_name') {{ $message }} @enderror</span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Middle Name</label>
                                        <input type="text" name="middle_name" class="form-control input-lg"
                                               placeholder="Middle Name" value="{{ $item->middle_name }}">
                                        <span class="text-danger">@error('middle_name') {{ $message }} @enderror</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Last Name <i class="text-danger">*</i></label>
                                        <input type="text" name="last_name" class="form-control input-lg"
                                               placeholder="Last Name" value="{{ $item->last_name }}">
                                        <span class="text-danger">@error('last_name') {{ $message }} @enderror</span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>User Phone <i class="text-danger">*</i></label>
                                        <input type="text" name="phone_number" class="form-control input-lg"
                                               placeholder="e.g. 255..., 254..." value="{{ $item->phone_number }}">
                                        <span class="text-danger">@error('phone_number') {{ $message }} @enderror</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>User Email <i class="text-danger">*</i></label>
                                        <input type="email" name="email" class="form-control"
                                               placeholder="user email" value="{{ $item->email }}">
                                        <span class="text-danger">@error('email') {{ $message }} @enderror</span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>User Role</label>
                                        <select name="role_name" class="form-control input-lg">
                                            <option value="{{ $item->role }}">{{ $item->role_name }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">@error('role_name') {{ $message }} @enderror</span>
                                    </div>
                                </div>

                                @if ($item->account_type == 2)
                                    <div class="row form-group">
                                        <div class="col-md-6">
                                            <label>Account Status</label>
                                            <select name="accountStatus" class="form-control">
                                                <option value="{{ $item->account_status }}">{{ $item->account_status }}</option>
                                                <option value="1">VERIFY ACCOUNT</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Map User to Company (Optional)</label>
                                            <input type="text" name="TSRN" class="form-control" placeholder="TSRN">
                                        </div>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="form-group col-md-12 text-right">
                                        <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
                                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner">
                                            <span class="ladda-label">Save Changes</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        {{-- ===== RESET PASSWORD MODAL ===== --}}
        @can('RESERT_PASSWORD')
            <div id="resetPassword{{ $item->user_uuid }}" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h5 class="modal-title text-uppercase">
                                <b>{{ $item->first_name }}</b> Password Reset
                            </h5>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('resert_password.update', ['resert_password' => $item->id]) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="form-group col-md-12">
                                    <label>User Name</label>
                                    <input type="text" class="form-control" readonly value="{{ $item->email }}">
                                </div>
                                <div class="form-group col-md-12">
                                    <label>New Password</label>
                                    <input type="text" name="new_password" class="form-control"
                                           placeholder="New password" readonly value="12345{{ $item->id }}">
                                </div>
                                <div class="text-right">
                                    <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
                                    <button type="submit" class="btn btn-primary ml-20">Yes, Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        {{-- ===== DISABLE ACCOUNT MODAL ===== --}}
        @can('DELETE_USER')
            <div id="disableAccount{{ $item->user_uuid }}" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header bg-warning-800">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h5 class="modal-title text-uppercase">
                                Are you sure you want to disable <b>{{ $item->first_name }}</b>'s account?
                            </h5>
                        </div>
                        <div class="modal-body">
                            <p><code>Note!:</code> This action will lock the user from logging into the system unless enabled.</p>
                            <form action="{{ route('users.destroy', ['user' => $item->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="disable_ccount" value="account">
                                <div class="text-right">
                                    <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
                                    <button type="submit" class="btn btn-danger ml-20">Yes, Disable</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== DELETE USER MODAL ===== --}}
            <div id="delete_user{{ $item->user_uuid }}" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h5 class="modal-title">
                                Are you sure you want to delete
                                <b>{{ $item->first_name }} {{ $item->last_name }}</b> from the list?
                            </h5>
                        </div>
                        <div class="modal-body">
                            <p><code>Note!:</code> By deleting this user, he or she will not be able to access the system.</p>
                            <form action="{{ route('users.destroy', ['user' => $item->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="text-right">
                                    <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
                                    <button type="submit" class="btn btn-danger ml-20">Yes, Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

    @endforeach

</x-app-layout>