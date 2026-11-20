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


                <div class=" col-md-12 form-group">
                    <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>

                            <tr class=" text-bold">
                                 <th>#</th>
                                <th class=" text-uppercase text-bold text-center">User Name</th>
                                <th class=" text-uppercase text-bold text-center">Phone Number</th>
                                <th class=" text-uppercase text-bold text-center">User email</th>
                                <th class=" text-uppercase text-bold text-center">Account Status</th>
                                <th class=" text-uppercase text-bold text-center">User Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i=1;
                            @endphp
                            @foreach ($users as $key=>$item)
                           <tr>
                             <td>{{ $key+1 }}</td>
                            <td>{{ $item->first_name }}  {{ $item->middle_name }}  {{ $item->last_name }}</td>
                            <td><a href="tel:{{ $item->phone_number }}">{{ trim(strrev(chunk_split(strrev($item->phone_number),3,' '))) }}</a></td>
                            <td><a href="mailto:{{ $item->email }}">{{ $item->email }}</a></td>
                            <td class=" text-center">
                                @if($item->account_status==1)
                                <span class=" label label-success label-block" title="Active Account"> Active</span>
                                 @else
                                  <span class=" label label-danger label-block" title="Suspended Account"> Suspended</span>
                                @endif
                                </td>
                            <td class=" text-bold text-center" data-toggle="modal" data-target="#ResertPassword{{ $item->user_uuid }}"><b class="btn btn-default text-primary">View</b></td>



                                   {{-- ResertPassword --}}
                                   <div id="ResertPassword{{ $item->user_uuid }}" class="modal fade" aria-modal="true">
                                    @csrf
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h5 class="modal-title text-uppercase text-center" id="h3"><b>{{ $item->first_name }}</b> PASSWORD RESERT</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('resert_password.update',['resert_password'=>$item->id]) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class=" form-group col-md-12">
                                                        <label>User Name</label>
                                                        <input type="text"  class=" form-control" readonly value="{{ $item->email }}">
                                                    </div>
                                                    <div class=" form-group col-md-12">
                                                        <label>New Password</label>
                                                        <input type="text" name="new_password" placeholder="New password" class=" form-control" readonly value="12345{{ $item->id }}">
                                                    </div>
                                                   <div class=" text-right">
                                                    <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                    <button type="submit" class="btn btn-primary ml-20" >Yes Resert</button>
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

            </div>

        </div>

    </div>

</x-app-layout>

