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
            <h6 class="panel-title text-bold"> REGISTERED COMPANIES</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-body table-responsive">
                <div class="form-group text-right">
                    <a href="{{ route('companies.create') }}" class="btn btn-success">
                        Register Company <i class="icon-add-to-list"></i>
                    </a>
                </div>

                <div class="form-group">
                    <table class=" table table-bordered table-striped datatable-button-html5-columns">
                        <thead>
                            <tr>
                                <th class=" text-bold text-center">S/N</th>
                                <th class=" text-bold text-center">COMPANY NAME</th>
                                <th class=" text-bold text-center">COMPANY PHONE</th>
                                <th class=" text-bold text-center">COMPANY EMAIL</th>
                                <th class=" text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>

                             @php $i=1; @endphp
                          @foreach ($company_list as $item)
                          <tr>
                            <td>{{ $i }}</td>
                            <td class=" text-capitalize">
                                <a data-toggle="modal" data-target="#update_company{{ $item->companyID }}" >{{ $item->companyName }}</a>
                            </td>
                            <td>{{ $item->companyPhone }}</td>
                            <td>{{ $item->companyEmail }}</td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                           @can('UPDATE_COMPANY_PROFILE')
                                        <li>
                                            <a href="{{ route('companies.edit',['company'=>$item->companyID]) }}" class=" text-primary"><i class=" icon-pencil7"></i> Edit Company</a>
                                        </li>
                                           @endcan

                                           @can('PREVIEW_COMPANY')
                                            <li>
                                                <a  href="{{ route('reports.edit',['report'=>$item->companyID]) }}" class=" text-purple"><i class="icon-bin"></i> Preview Company</a>
                                            </li>
                                            @endcan

                                            @can('DELETE_COMPANY')
                                            <li>
                                                <a  data-toggle="modal" data-target="#delete_company{{ $item->companyID }}" class=" text-danger"><i class="icon-bin"></i> Delete Company</a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </li>
                                </ul>
                            </td>
                        </tr>


                        {{-- update company --}}

                        <div id="update_company{{ $item->companyID }}" class="modal fade" aria-modal="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-info-400">
                                        <h4 class="modal-title text-bold text-center text-uppercase">Update {{ $item->companyName }} DETAILS</h5>
                                        </div>
                                        <form action="{{ route('company_profiles.update', ['company_profile' => $item->companyID]) }}" method="post">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class=" row">
                                                    <div class=" form-group col-md-12">
                                                        <label>Company Name<i class=" text-danger">*</i></label>
                                                        <input type="text" name="company_name" class=" form-control" value="{{ $item->companyName }}" placeholder="Enter company Name" required>
                                                    </div>

                                                    <div class=" form-group col-md-6">
                                                        <label>Company Email</label>
                                                        <input type="text" name="company_email" class=" form-control" value="{{ $item->companyEmail }}" placeholder="Enter company Email">
                                                    </div>
                                                    <div class=" form-group col-md-6">
                                                        <label>Company Phone<i class=" text-danger">*</i></label>
                                                        <input type="text" name="company_phone" class=" form-control" value="{{ $item->companyPhone }}" placeholder="255753427137" required>
                                                    </div>
                                                    <div class=" form-group col-md-12">
                                                        <label>Contact Person Name<i class=" text-danger">*</i></label>
                                                        <input type="text" name="person_name" class=" form-control" value="{{ $item->personalName }}" placeholder="Enter contact person Name" required>
                                                    </div>

                                                    <div class=" form-group col-md-6">
                                                        <label>Conatct Person Email</label>
                                                        <input type="text" name="contact_person_email" class=" form-control" value="{{ $item->personalEmail }}" placeholder="Enter Contact Person Email">
                                                    </div>
                                                    <div class=" form-group col-md-6">
                                                        <label>Contact personal Phone Number<i class=" text-danger">*</i></label>
                                                        <input type="text" name="contact_person_phone" class=" form-control" value="{{ $item->personalPhone }}" placeholder="255753427137" required>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class=" modal-footer">
                                                <div class=" text-right">
                                                <button type="button" class=" btn btn-default border-warning text-warning" data-dismiss="modal"> Close</button>
                                                <button type="submit" class="btn btn-info  btn-ladda btn-ladda-spinner" ><span class="ladda-label">Update Company</span></button>
                                                </div>
                                            </div>
                                        </form>
                                </div>
                            </div>
                        </div>

                        {{-- update company --}}


                        <div id="delete_company{{ $item->companyID }}" class="modal fade" aria-modal="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h4 class="modal-title text-bold text-center text-uppercase">DELETE {{ $item->companyName }} AND RELATED DETAILS</h5>
                                        </div>
                                        <div class=" modal-body">
                                            <form action="{{ route('companies.destroy',['company'=>$item->companyID]) }}" method="post" id="company">
                                                @csrf
                                                @method('DELETE')
                                            </div>
                                            <div class=" modal-footer">
                                                <div class=" text-right">
                                                <button class=" btn btn-default border-warning text-warning" data-dismiss="modal"> Close</button>
                                                <button type="submit" class="btn btn-warning  btn-ladda btn-ladda-spinner" ><span class="ladda-label">Delete Company</span></button>
                                                </div>
                                            </div>
                                        </form>
                                </div>
                            </div>
                        </div>
                        @php $i++; @endphp
                          @endforeach
                        </tbody>
                    </table>
                    <div id="p1"></div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
