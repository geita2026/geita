<x-app-layout>
    <title>{{ env('APP_NAME') }} | My Companies</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> ePermit </a></li>
                <li class="active">View All my companies</li>
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
            
                <div class=" col-md-12 table-header form-group">
                    
                    <h4 class=" text-center text-uppercase"> List Of My Registered Companies</h4>
                </div>
                <div class=" form-group text-right">
                    <a href="{{ route('company_profile',['companyId'=>date('dmd')]) }}" class=" btn btn-success" >New
                        Company <i class=" icon-plus2"></i></a>
                </div>
                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">TSRN</th>
                            <th class=" text-bold">Company Name</th>
                            <th class=" text-bold">Company Email</th>
                            <th class=" text-bold">Company Phone</th>
                            <th class=" text-bold">Company Status</th>
                            <th class=" text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($myCompanies as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class=" text-uppercase"><a href="{{ route('company_profile', ['companyId' =>$item->registrationID ?? 1]) }}"
                                    >{{ $item->companyName }}</a></td>
                                <td>{{ $item->companyEmail }}</td>
                                <td>{{ $item->companyPhone }}</td>
                                <td class=" text-center">
                                    @if ($item->status == 0)
                                        <label class=" label label-rounded label-danger">UNVERIFIED</label>
                                    @elseif ($item->status == 1)
                                    <label class=" label label-rounded label-success">VERIFIED</label>
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
                                                    <a href="{{ route('company_profile', ['companyId' =>$item->registrationID ?? 1]) }}"
                                                        class=" btn btn-info">View Application <i
                                                            class=" icon-eye"></i></a>
                                                </li>
                                                <li>
                                                    <a href="" data-toggle="modal"
                                                        data-target="#deleteApplication{{ $item->companyId }}"
                                                        class="btn btn-danger"> Cancel Application <i
                                                            class=" icon-bin"></i></a>

                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                                {{-- delete modal --}}
                                <div id="deleteApplication{{ $item->companyId }}" class="modal fade"
                                    aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete this Data!
                                                    ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('apply_permit.destroy', ['apply_permit' => $item->companyId]) }}"
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
                                {{-- DELETE --}}

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</x-app-layout>
