<x-app-layout>
    <title> {{ env('APP_NAME') }} | Bank Accounts </title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('countries.index') }}"><i class=" icon-accessibility position-left"></i> BANK
                        ACCOUNTS</a></li>
                <li class="active">View All bank accounts</li>
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
                    <h6 class=" text-bold">REGISTERED BANK ACCOUNTS</h6>
                </div>
                <div class=" form-group text-right">
                    <a data-toggle="modal" data-target="#AddBankAccount" class=" btn btn-success">Add Bank Account <i
                            class=" icon-add-to-list"></i></a>
                </div>

                <table class="table datatable-button-html5-columns table-bordered table-striped  table-lg text-nowrap">
                    <thead>
                        <tr>
                            <th class=" text-bold text-center">#</th>
                            <th class=" text-bold text-center">ACCOUNT NAME</th>
                            <th class=" text-bold text-center">ACCOUNT NUMBER</th>
                            <th class=" text-bold text-center">SWIFT CODE</th>
                            <th class=" text-bold text-center">ACCOUNT CURRENCY</th>
                            <th class=" text-bold text-center">BANK NAME</th>


                            <th class=" text-bold text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($banks as $item)
                            <tr>
                                <td>{{ $i }}</td>
                                <th class=" text-capitalize">{{ $item->account_name }} [ <b>{{ $item->bankAccountStatus }}</b> ]
                                </th>
                                <td>{{ $item->account_number }}</td>
                                <td class=" text-center">{{ $item->swift_code }}</td>
                                <td class=" text-center">{{ $item->account_currency }}</td>
                                <td class=" text-center">{{ $item->bank_name }}</td>


                                {{--  action selector  --}}
                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a data-toggle="modal"
                                                        data-target="#editAccountNumber{{ $item->account_id }}"
                                                        class=" text-primary">Edit Account <i
                                                            class=" icon-pencil7"></i></a>
                                                </li>
                                                <li>
                                                    <a href="" data-toggle="modal"
                                                        data-target="#deleteAccountNo{{ $item->account_id }}"
                                                        class="text-danger">Delete Account <i class=" icon-bin"></i></a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>

                                {{-- edit country modal --}}

                                <div id="editAccountNumber{{ $item->account_id }}" class="modal fade"
                                    aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-blue">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title text-uppercase text-center">UPDATE
                                                    {{ $item->account_name }} BANK ACCOUNT</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('bank_accounts.update', ['bank_account' => $item->account_id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label>Bank Name<i class=" text-danger">*</i></label>
                                                            <input type="text" name="bank_name" class=" form-control"
                                                                placeholder=" Bank Name" value="{{ $item->bank_name }}"
                                                                required>
                                                            <span class=" text-danger">
                                                                @error('bank_name')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <label>Account Name<i class=" text-danger">*</i></label>
                                                            <input type="text" name="account_name"
                                                                class=" form-control" placeholder=" Account Name"
                                                                value="{{ $item->account_name }}" required>
                                                            <span class=" text-danger">
                                                                @error('account_name')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class=" row">
                                                        <div class="form-group col-md-6">
                                                            <label>Bank Account Number<i
                                                                    class="text-danger">*</i></label>
                                                            <input type="text" name="account_number"
                                                                class=" form-control" placeholder=" Bank Account Number"
                                                                value="{{ $item->account_number }}" required>
                                                            <span class=" text-danger">
                                                                @error('account_name')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label>Swift Code<i class="text-danger">*</i></label>
                                                            <input type="text" name="swift_code"
                                                                class=" form-control" placeholder="Swift Code"
                                                                value="{{ $item->swift_code }}" required>
                                                        </div>
                                                    </div>

                                                    <div class=" row">
                                                        <div class=" form-group col-md-6">
                                                            <label>Account Currency<i class=" text-danger">*</i></label>
                                                            <select name="account_currency" class=" form-control"
                                                                required>
                                                                <option value="{{ $item->account_currency }}">
                                                                    {{ $item->account_currency }}</option>
                                                                <option value="TZS">TZS</option>
                                                                <option value="USD">USD</option>
                                                            </select>
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Account Status<i class=" text-danger">*</i></label>
                                                            <select name="bankAccountStatus" class=" form-control"
                                                                required>
                                                                <option value="{{ $item->bankAccountStatus }}">
                                                                    {{ $item->bankAccountStatus }}</option>
                                                                <option value="ACTIVE">ACTIVE</option>
                                                                <option value="INACTIVE">INACTIVE</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class=" row">
                                                        <div class="form-group col-md-12">
                                                            <label>Bank account descriptions</label>
                                                            <textarea name="account_descriptions" class=" form-control" placeholder="Bank descriptions">{{ $item->account_descriptions }}</textarea>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-link"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit"
                                                    class="btn btn-success btn-ladda ladda-spinner">Save Changes <i
                                                        class=" icon-add-to-list"></i></button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>



                                {{--  Action selector  --}}
                                <div id="deleteAccountNo{{ $item->account_id }}" class="modal fade"
                                    aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete
                                                    {{ $item->account_name }} Bank account ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('bank_accounts.destroy', ['bank_account' => $item->account_id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class=" text-right">
                                                        <button type="button" data-dismiss="modal"
                                                            class=" btn btn-secondary">Close</button>
                                                        <button type="submit"
                                                            class="btn btn-danger btn-ladda btn-ladda-spinner"
                                                            data-style="slide-down"><span class="ladda-label">Yes,
                                                                Delete</span></button>
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
    <form action="{{ route('bank_accounts.store') }}" method="POST">
        @csrf
        <div id="AddBankAccount" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-blue">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title text-uppercase text-center">Add Bank Account.</h5>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Bank Name<i class=" text-danger">*</i></label>
                                    <input type="text" name="bank_name" class=" form-control"
                                        placeholder=" Bank Name" value="{{ old('bank_name') }}" required>
                                    <span class=" text-danger">
                                        @error('bank_name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Account Name<i class=" text-danger">*</i></label>
                                    <input type="text" name="account_name" class=" form-control"
                                        placeholder=" Account Name" value="{{ old('account_name') }}" required>
                                    <span class=" text-danger">
                                        @error('account_name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>


                            <div class=" row">
                                <div class="form-group col-md-6">
                                    <label>Bank Account Number<i class="text-danger">*</i></label>
                                    <input type="text" name="account_number" class=" form-control"
                                        placeholder=" Bank Account Number" value="{{ old('account_number') }}"
                                        required>
                                    <span class=" text-danger">
                                        @error('account_name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Swift Code<i class="text-danger">*</i></label>
                                    <input type="text" name="swift_code" class=" form-control"
                                        placeholder="Swift Code" value="{{ old('swift_code') }}" required>
                                </div>
                            </div>

                            <div class=" row">
                                <div class=" form-group col-md-6">
                                    <label>Account Currency<i class=" text-danger">*</i></label>
                                    <select name="account_currency" class=" form-control" required>
                                        <option value=""> - - - Select Currency Type - - -</option>
                                        <option value="TZS">TZS</option>
                                        <option value="USD">USD</option>
                                    </select>
                                </div>
                                <div class=" form-group col-md-6">
                                    <label>Account Status<i class=" text-danger">*</i></label>
                                    <select name="bankAccountStatus" class=" form-control" required>
                                        <option value=""> - - - Select Account Status - - -</option>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>

                            </div>
                            <div class=" row">
                                <div class="form-group col-md-12">
                                    <label>Bank account descriptions</label>
                                    <textarea name="account_descriptions" class=" form-control" placeholder="Bank descriptions"></textarea>
                                </div>
                            </div>



                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-ladda btn-ladda-spinner">Add Account <i
                                class=" icon-add-to-list"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
