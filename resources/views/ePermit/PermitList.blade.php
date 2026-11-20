<x-app-layout>
    <title>{{ env('APP_NAME') }} | Applications</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> ePermit </a></li>
                <li class="active">View All Applications</li>
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
                    <h4 class=" text-center">List Of ePermit Applications</h4>
                </div>
                <div class=" form-group text-right">
                    <a href="{{ route('apply_permit.create') }}" class=" btn btn-success">New
                        Application <i class=" icon-plus2"></i></a>
                </div>
                <table class="table datatable-save-state table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">#</th>
                            <th class=" text-bold">Company Name</th>
                            <th class=" text-bold">Type Of Trade Promotion Event</th>
                            <th class=" text-bold">Type Of Trade Promotion Event Title</th>
                            <th class=" text-bold">Application Date</th>
                            <th class=" text-bold">Application Status</th>
                            <th class=" text-bold">Permit Status</th>
                            <th class=" text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($applications as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class=" text-uppercase">{{ $item->companyName }}</td>
                                <td>{{ $item->promotionEventTypeName }}</td>
                                <td>{{ $item->eventTitle }}</td>
                                <td>{{ date('M d, Y h:i:s', strtotime($item->applicationDate)) }}</td>
                                <td class=" text-center">
                                    @if ($item->applicationStatus == 1)
                                        <label class=" label label-rounded label-danger">NOT SUBMITED</label>
                                        @elseif ($item->applicationStatus == 2)
                                        <label class=" label label-rounded label-warning">SUBMITED FOR REVIEW</label>
                                        @elseif ($item->applicationStatus == 3)
                                    <label class=" label label-rounded label-default">REVIEWED</label>
                                    @elseif ($item->applicationStatus == 4)
                                    <label class=" label label-rounded label-info">VERIFIED</label>
                                    @elseif ($item->applicationStatus==5)
                                    <label class=" label label-rounded label-primary">APPROVED</label>
                                    @elseif ($item->applicationStatus==6)
                                    <label class=" label label-rounded label-success">CERTIFICATE ISSUED</label>
                                    @endif
                                </td>

                                <td class=" text-center">
                                    <a class=" btn btn-info" href="{{ route('apply_permit.show',['apply_permit'=>$item->applicationId]) }}">View Application</a>
                                </td>

                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a href="{{ route('apply_permit.show', ['apply_permit' => $item->applicationId]) }}"
                                                        class=" btn btn-info">View Application <i
                                                            class=" icon-eye"></i></a>
                                                </li>
                                                <li>
                                                    <a href="" data-toggle="modal"
                                                        data-target="#deleteApplication{{ $item->applicationId }}"
                                                        class="btn btn-danger"> Cancel Application <i
                                                            class=" icon-bin"></i></a>

                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                                {{-- delete modal --}}
                                <div id="deleteApplication{{ $item->applicationId }}" class="modal fade"
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
                                                    action="{{ route('apply_permit.destroy', ['apply_permit' => $item->applicationId]) }}"
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
    <!-- /dashboard content -->
    {{-- add role modal --}}
    <form data-action="{{ route('apply_permit.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="add_role" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-blue">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Create New Promotion Event Permit Application</h5>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <div class=" form-group col-md-12">
                                <label>Select Company Name<i class=" text-danger">*</i></label>
                                <select name="companyId" class=" form-control" required>
                                    @foreach ($myCompany as $data)
                                        <option value="{{ $data->companyID }}">{{ $data->companyName }}</option>
                                    @endforeach
                                </select>
                                <span class=" text-danger text-error eventType"></span>
                            </div>

                            <div class=" form-group col-md-12">
                                <label>What type of Promotion Event you are requesting permit ?<i
                                        class=" text-danger">*</i></label>
                                <select name="typeOfPromotionEvent" class=" form-control" required>
                                    <option value=""> - - - Select Promotion Event - - -</option>
                                    @foreach ($proEvents as $value)
                                        <option value="{{ $value->eventTypeId }}">{{ $value->promotionEventTypeName }}</option>
                                    @endforeach
                                </select>
                                <span class=" text-danger text-error eventType"></span>
                            </div>

                            <div class=" form-group col-md-12">
                                <label>Trade Promotion Audience<i
                                        class=" text-danger">*</i></label>
                                <select name="typeOfApplication" class=" form-control" required>
                                    <option value=""> - - - Select Promotion Audience - - -</option>
                                        <option value="1">LOCAL PARTICIPANTS ONLY</option>
                                        <option value="2">FOREIGN PARTICIPANTS ONLY</option>
                                        <option value="3">BOTH LOCAL & FOREIGN PARTICIPANTS</option>
                                </select>
                                <span class=" text-danger text-error eventType"></span>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                class="ladda-label">Start Application</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>
