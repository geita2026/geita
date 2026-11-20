<x-app-layout>
    <title>{{ env('APP_NAME') }} | Company</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title">
            </div>
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
            <h6 class="panel-title text-bold"> REGISTERED COMPANIES</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>
        <div class=" panel-body table-responsive">
            <span class=" text-danger">@error('company_file')
                {{ $message }}
                @enderror</span>
                <div class="form-group">
                    @if (Session::get('success'))
                    <div class=" alert alert-success alert-bordered alert-rounded">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                        <span class="text-semibold">Congraduration!</span> {{ session::get('success') }}
                    </div>
                    @elseif (Session::get('fail'))
                    <div class=" alert alert-danger alert-bordered">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                        <span class="text-semibold">Oops!</span> {{ session::get('fail') }}
                    </div>
                    @endif
                </div>
                <div class=" row">
                    <div class=" form-group text-right col-md-12">
                        <a href="{{ route('companies.create') }}" class=" btn btn-success">Register Company <i class=" icon-add-to-list"></i></a>
                    </div>
                </div>
                <table class=" table datatable-button-html5-columns table-bordered table-striped table-responsive table-lg text-nowrap">
                    <thead>

                        <tr>
                            <th>#</th>
                            {{--  <th class=" text-bold">Registration_Date</th>  --}}
                            <th class=" text-bold">Company Name</th>
                            <th class=" text-bold">Add Details</th>
                            <th class=" text-bold">Company Email</th>
                            <th class=" text-bold">Company Phone</th>
                            <th class=" text-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($companies as  $item)
                        <tr>
                            <td>{{ $i }}</td>
                            {{-- modal to switch status  --}}
                            <div id="status{{ $item->companyID }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-teal-400">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to Switch Company Status ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('companies.update', ['company' => $item->companyID]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="status">

                                                <div class=" text-right">
                                                    <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                    <button type="submit" class="btn btn-primary ml-20" >Yes, Switch</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--  end  --}}



                            <td>{{ $item->companyName }}</td>
                            <td class=" text-center text-bold"><a href="{{ route('companies.show',['company'=>$item->companyID]) }}" > Add Details <i class=" icon-add"></i></a></td>
                            <td>{{ $item->companyEmail }}</td>
                            <td >{{ $item->companyPhone }}</td>
                            {{--  action selector  --}}
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a href="" data-toggle="modal" data-target="#{{ $item->companyID }}"  class="text-danger">Delete Company <i class=" icon-bin"></i></a>
                                            </li>
                                            <li>
                                                <a href="{{ route('exhibitors.edit',['exhibitor'=>$item->companyID]) }}" class=" text-primary">Preview Details <i class=" icon-eye"></i></a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>
                            {{--  //end action selector  --}}


                            {{-- delete modal --}}
                            <div id="{{ $item->companyID }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete this Company ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('companies.destroy', ['company' => $item->companyID]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class=" text-right">
                                                    <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                    <button type="submit" class="btn btn-danger ml-20" >Yes, Delete</button>
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
</x-app-layout>
