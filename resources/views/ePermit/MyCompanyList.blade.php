<x-app-layout>
    <title>{{ env('APP_NAME') }} | My Companies</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('startApp') }}"><i class="icon-home2 position-left"></i> Home </a></li>
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
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                                {{-- DELETE --}}

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</x-app-layout>
