<x-app-layout>
    <!-- dashboard -->
    <title>{{ env('APP_NAME') }} | Users</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('users.index') }}"><i class=" icon-users2 position-left"></i>Users</a></li>
                <li class="active">View-All-User</li>
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

                <div class=" col-md-12 row form-group">

                    <div class=" col-md-12 form-group table-header">
                        <h3 class=" text-center">LIST OF SYSTEM USERS</h3>
                    </div>

                </div>


                <div class=" col-md-6 form-group">
                    {{-- Error or success messages --}}
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <span class=" text-danger">{{ $error }} ,</span>
                        @endforeach
                    @endif
                    {{-- //error messages --}}
                </div>





                {{-- search --}}
                <div class=" col-md-12 form-group">
                    <form action="{{ route('users.index') }}" method="POST">
                        @csrf
                        @method('GET')
                        <div class=" row">
                            <div class="col-md-3 form-group" id="usersType">
                                <select name="user_type" class=" form-control" onchange="this.form.submit()">
                                    <option value=""> - - - Filter By Type of Users - - - </option>
                                    <option value="1" class=" text-capitalize"> Internal System Users (Exhibition Staff)</option>
                                    <option value="2" class=" text-capitalize">External System Users (Exhibitors)</option>
                                </select>
                                <span class="text-danger"> @error('role')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-5 form-group" id="userRole">
                                
                                <input type="text" name="userName" class=" form-control" placeholder="Enter Email">
                                <span class="text-danger"> @error('role')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                           

                            <div class="col-md-3 form-group text-right">
                                <button class=" btn btn-default text-primary btn-ladda btn-ladda-spinner">Load Data <i
                                        class=" icon-database-refresh"></i></button>
                            </div>

                        </div>
                    </form>
                </div>
                {{-- //search --}}

                <div class=" col-md-12 row form-group">
                    <div class=" text-right">
                        <a href="{{ route('users.create') }}" class=" btn btn-default border-primary">Register new user
                            <i class=" icon-user-plus"></i></a>
                    </div>
                </div>

                <div class=" col-md-12 form-group">
                    <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>
                            <tr class=" text-bold">
                                <th>#</th>
                                <th class=" text-uppercase text-bold text-center">User Name</th>
                                <th class=" text-uppercase text-bold text-center">Phone Number</th>
                                <th class=" text-uppercase text-bold text-center">User email</th>
                                <th class=" text-uppercase text-bold text-center">User Role</th>
                                <th class=" text-uppercase text-bold text-center">Account Status</th>
                                <th class=" text-uppercase text-bold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($users as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->first_name }} {{ $item->middle_name }} {{ $item->last_name }}</td>
                                    <td><a
                                            href="tel:{{ $item->phone_number }}">{{ trim(strrev(chunk_split(strrev($item->phone_number), 3, ' '))) }}</a>
                                    </td>
                                    <td><a href="mailto:{{ $item->email }}">{{ $item->email }}</a></td>
                                    <td class=" text-bold text-uppercase">{{ $item->name }}</td>
                                    <td class=" text-center">
                                       
                                        @if ($item->account_status == 1)
                                            <span class=" label label-success label-block" title="Active Account">
                                                Active</span>
                                        @elseif ($item->account_status==0)
                                            <span class=" label label-danger label-block" title="Suspended Account">
                                                Suspended</span>
                                                @elseif ($item->account_status==2)
                                            <span class=" label label-default label-block" title="Suspended Account">
                                                UNVERIFIED</span>
                                        @endif
                                    </td>

                                    {{--  action selector  --}}
                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    @can('Update Users')
                                                        <li>
                                                            <a data-toggle="modal"
                                                                data-target="#editUser{{ $item->id }}"
                                                                class=" text-primary"><i class=" icon-pencil7"></i> Edit
                                                                Account</a>
                                                        </li>
                                                    @endcan

                                                    @can('Resert Password')
                                                        <li>
                                                            <a data-toggle="modal"
                                                                data-target="#ResertPassword{{ $item->user_uuid }}"
                                                                class=" text-primary text-bold"><i
                                                                    class=" icon-envelop2"></i> Resert Password</a>
                                                        </li>
                                                    @endcan

                                                    @if ($item->account_type==2 && $item->myCompany!=Null)
                                                    @can('View Companies')
                                                    <li>
                                                        <a href="{{ route('companies.edit',['company'=>$item->myCompany]) }}"
                                                            
                                                            class=" text-primary text-bold"><i
                                                                class=" icon-office"></i> View Company</a>
                                                    </li>
                                                @endcan
                                                    @endif
                                                    

                                                    @can('Delete Users')
                                                        <li>
                                                            <a data-toggle="modal"
                                                                data-target="#delete_user{{ $item->user_uuid }}"
                                                                class=" text-danger"><i class=" icon-cross2"></i> Suspend
                                                                Account</a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>
                                    {{--  //end action selector  --}}



                                    {{-- edit user modal --}}
                                    <div id="editUser{{ $item->id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title text-uppercase">Update
                                                        <b>{{ $item->first_name }} {{ $item->last_name }}'S </b>
                                                        Informations</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('users.update', ['user' => $item->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class=" row">
                                                            <div class="form-group col-md-6">
                                                                <label>First Name<i class=" text-danger">*</i></label>
                                                                <input type="text" name="first_name"
                                                                    class=" form-control input-lg"
                                                                    placeholder="First Name"
                                                                    value="{{ $item->first_name }}">
                                                                <span class="text-danger"> @error('first_name')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>Middle Name</label>
                                                                <input type="text" name="middle_name"
                                                                    class=" form-control input-lg"
                                                                    placeholder="Middle Name"
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
                                                                    class=" form-control input-lg"
                                                                    placeholder="Last  name"
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
                                                                <input type="text" name="email"
                                                                    class=" form-control" placeholder=" user email"
                                                                    value="{{ $item->email }}">
                                                                <span class="text-danger"> @error('email')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>User Role </label>
                                                                <select name="role_name"
                                                                    class=" form-control input-lg">
                                                                    <option value="{{ $item->role }}">
                                                                        {{ $item->name }}</option>
                                                                    @foreach ($roles as $role)
                                                                        <option value="{{ $role->id }}">
                                                                            {{ $role->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="text-danger"> @error('role')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>
                                                        </div>
                                                       @if ($item->account_type==2)
                                                       <div class=" row form-group">
                                                        <div class=" col-md-6">
                                                            <select name="accountStatus" class=" form-control">
                                                                <option value="{{ $item->account_status }}">{{ $item->account_status }}</option>
                                                                <option value="1">VERIFY ACCOUNT</option>
                                                            </select>
                                                        </div>
                                                        <div class=" col-md-6">
                                                            <label>Mapp user to Company (Optional)</label>
                                                            <input type="text" name="TSRN" class=" form-control">
                                                        </div>
                                                    </div>
                                                       @endif
                                                        <div class=" row">
                                                            <div class=" form-group col-md-12 text-right">
                                                                <button type="submit"
                                                                    class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                                                        class="ladda-label">Save Changes</span></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /edit user modal -->




                                    {{-- disableAccount --}}
                                    <div id="disableAccount{{ $item->user_uuid }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning-800">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title text-uppercase">Are you sure you whant to
                                                        disable <b>{{ $item->first_name }}</b> Account ?</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p><code>Note!:</code> This action will lock user to login in the
                                                        system unless enabled</p>


                                                    <form action="{{ route('users.destroy', ['user' => $item->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="disable_ccount" value="account">
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-secondary">Close</button>
                                                            <button type="submit" class="btn btn-danger ml-20">Yes,
                                                                Disable</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- disableAccount --}}


                                    {{-- delete modal --}}
                                    <div id="delete_user{{ $item->user_uuid }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you whant to delete
                                                        <b>{{ $item->user_name }}</b> from the list ?</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p><code>Note!:</code> By deleting this user He or she will not able
                                                        to access system.</p>
                                                    <form action="{{ route('users.destroy', ['user' => $item->id]) }}"
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



                                    {{-- ResertPassword --}}
                                    <div id="ResertPassword{{ $item->user_uuid }}" class="modal fade"
                                        aria-modal="true">
                                        @csrf
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <h5 class="modal-title text-uppercase">
                                                        <b>{{ $item->first_name }}</b> PASSWORD RESERT</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('resert_password.update', ['resert_password' => $item->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class=" form-group col-md-12">
                                                            <label>User Name</label>
                                                            <input type="text" class=" form-control" readonly
                                                                value="{{ $item->email }}">
                                                        </div>
                                                        <div class=" form-group col-md-12">
                                                            <label>New Password</label>
                                                            <input type="text" name="new_password"
                                                                placeholder="New password" class=" form-control"
                                                                readonly value="12345{{ $item->id }}">
                                                        </div>
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-secondary">Close</button>
                                                            <button type="submit" class="btn btn-primary ml-20">Yes
                                                                Resert</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- ResertPassword --}}
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>

                    </table>
                </div>
                <div class=" col-md-12 form-group text-right">
                    {{ $users->links() }}
                </div>

            </div>

        </div>

    </div>

</x-app-layout>


<script type="text/javascript">
    $(function() {
        $("#filterBy").change(function() {
            if ($(this).val() == "userType") {
                $("#UsersType").show();
                $("#userRole").hide();
                $("#limit").hide();
            } else {
                $("#userRole").hide();
                $("#limit").hide();
                $("#usersType").hide();
                $("#limit").hide();
            }
        });
    });
</script>
