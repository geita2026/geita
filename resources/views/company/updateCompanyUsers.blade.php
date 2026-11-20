<x-app-layout>
    <title>{{ env('APP_NAME') }} | Company</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('companies.index') }}"><i class=" icon-office position-left"></i>COMPANIES</a></li>
                <li class="active">View-All Companies</li>
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
            <div class="panel-body">
                <div class="form-group">
                    @php $i=1; @endphp
                    @foreach ($users as $item)
                        <form action="{{ route('company_users.update', ['company_user' => $item->user_uuid]) }}"
                            method="post">
                            @csrf
                            @method('PUT')
                            <div class=" modal-body">
                                <div class=" row">
                                    <div class="form-group col-md-6">
                                        <label>First Name<i class=" text-danger">*</i></label>
                                        <input type="text" name="first_name" class=" form-control input-lg"
                                            placeholder="First Name" value="{{ $item->first_name }}">
                                        <span class="text-danger"> @error('first_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class=" form-group col-md-6">
                                        <label>Middle Name</label>
                                        <input type="text" name="middle_name" class=" form-control input-lg"
                                            placeholder="Middle Name" value="{{ $item->middle_name }}">
                                        <span class="text-danger"> @error('middle_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                </div>
                                <div class=" row">
                                    <div class=" form-group col-md-6">
                                        <label>Last Name<i class=" text-danger">*</i></label>
                                        <input type="text" name="last_name" class=" form-control input-lg"
                                            placeholder="Last  name" value="{{ $item->last_name }}">
                                        <span class="text-danger"> @error('last_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class=" form-group col-md-6">
                                        <label>user phone<i class=" text-danger">*</i></label>
                                        <input type="text" name="phone_number" class=" form-control input-lg"
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
                                        <input type="text" name="roleID" value="{{ $item->name }}"
                                            class=" form-control" readonly>
                                    </div>
                                </div>

                            </div>
                            <div class=" modal-footer">
                                <div class=" text-right">
                                    <button type="submit" class="btn btn-info  btn-ladda btn-ladda-spinner"><span
                                            class="ladda-label">Save Changes</span></button>
                                </div>
                            </div>
                        </form>
                        @php $i++; @endphp
                    @endforeach
                    <div id="p1"></div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
