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
                    <h4 class=" text-center">TRADE PROMOTION EVENT PERMIT APPLICATION LIST</h4>
                </div>
                <div class=" form-group row">
                    <div class=" form-group col-md-6">
                        <form action="{{ route('review_applications.index') }}" method="POST">
                            @csrf
                            @method('GET')
                            <select name="applicationStatus" class=" form-control" onchange="this.form.submit()">
                                <option value="">Select Application status</option>
                                <option value="2">SUBMITED APPLICATION</option>
                                <option value="1">UNSUBMITED APPLICATION</option>
                                <option value="4">REVIEWED APPLICATION</option>
                                <option value="5">APPROVED APPLICATION</option>
                                <option value="6">ISSUED CERTIFICATES</option>
                                <option value="">All Applications</option>
                            </select>
                        </form>
                    </div>
                    <div class=" col-md-6">
                        <select id="search-select" name="search" class=" form-control"
                            onchange="location = this.value;">
                        </select>
                    </div>
                </div>
                <div class=" form-group">
                    <table class="table  table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                               
                                <th class=" text-bold">Company Name</th>
                                <th class=" text-bold">Trade Promotion Event Title</th>
                                <th class=" text-bold">Application Status</th>
                                <th class=" text-bold">Asigned Officer</th>
                                <th class=" text-bold">Application Date</th>
                                <th class=" text-center">Preview Application</th>
                                <th class=" text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($applications as $key => $item)
                                <tr>
                                    <td class=" text-uppercase"><a
                                            href="{{ route('review_applications.show', ['review_application' => $item->applicationId]) }}">{{ $item->companyName }}</a>
                                    </td>

                                    <td>{{ $item->eventTitle }}</td>
                                    <td class=" text-center">
                                        @if ($item->applicationStatus == 1)
                                            <label class=" label label-rounded label-danger">NOT SUBMITED</label>
                                        @elseif ($item->applicationStatus == 2)
                                            <label class=" label label-rounded bg-brown-300">SUBMITED FOR REVIEW</label>
                                        @elseif ($item->applicationStatus == 3)
                                            <label class=" label label-rounded label-default">REVIEWED</label>
                                        @elseif ($item->applicationStatus == 4)
                                            <label class=" label label-rounded label-info">VERIFIED</label>
                                        @elseif ($item->applicationStatus == 5)
                                            <label class=" label label-rounded label-primary">APPROVED</label>
                                        @elseif ($item->applicationStatus == 6)
                                            <label class=" label label-rounded label-success">CERTIFICATE ISSUED</label>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->applicationStatus >1 && $item->applicationStatus <5)
                                            @if ($item->first_name == null)
                                                <a data-toggle="modal" class=" btn btn-block"
                                                    data-target="#AsignReviwer{{ $item->applicationId }}">Asign
                                                    Officer</a>
                                            @else
                                                <a data-toggle="modal" class=" btn btn-block"
                                                    data-target="#AsignReviwer{{ $item->applicationId }}">{{ $item->first_name }}
                                                    {{ $item->last_name }} [Change Reviwer]</a>
                                            @endif
                                        @elseif ($item->applicationStatus == 1)
                                            <i class=" label label-danger">Not submited for Review</i>
                                        @elseif ($item->applicationStatus>=5)
                                        <label class=" text-primary">{{ $item->first_name }} {{ $item->last_name }}</label>
                                        @endif

                                    </td>
                                    <td>{{ date('M d, Y h:i:s', strtotime($item->applicationDate)) }}</td>
                                    <td>
                                        @if ($item->applicationStatus>1)
                                        <a href="{{ route('review_applications.show', ['review_application' => $item->applicationId]) }}" class=" btn btn-primary">Review Application <i class=" icon icon-eye"></i></a>
                                        @else
                                        <label class=" label label-danger">Not submited for review</label>
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
                                                        <a href="{{ route('review_applications.show', ['review_application' => $item->applicationId]) }}"
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
                                    <div id="AsignReviwer{{ $item->applicationId }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Asign Application Reviewer</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('review_applications.store') }}"
                                                        method="POST">
                                                        @csrf

                                                        <input type="hidden" name="applicationReference"
                                                            value="{{ $item->applicationId }}">
                                                        <div class=" form-group">
                                                            <label>Application Reviewer<i
                                                                    class=" text-danger">*</i></label>
                                                            <select name="reviewer" class="form-control">
                                                                <option value=""> - - - Select Application
                                                                    Reviewer - - - </option>
                                                                @foreach ($users as $value)
                                                                    <option value="{{ $value->user_uuid }}"
                                                                        class="text-uppercase">
                                                                        {{ $value->first_name }}
                                                                        {{ $value->last_name }} [{{ $value->name }}]
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-secondary">Close</button>
                                                            <button type="submit"
                                                                class="btn btn-primary btn-rounded btn-ladda btn-ladda-spinner ml-20">Asign
                                                                Reviewing Officer
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- DELETE --}}

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

                <div class=" form-group text-right">
                    {{ $applications->links() }}
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
<script>
    var select = document.getElementById('selection');
    select.onchange = function() {
        this.form.submit();
    };
</script>

<script>
    $(document).ready(function() {
        $('#search-select').select2({
            ajax: {
                url: '{{ route('review_applications.create') }}', // URL for the AJAX request
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // Search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: '{{ env('APP_URL') }}/ePermit/review_applications/' +
                                    item.applicationId,
                                text: item.eventTitle + ' (' + item.companyName + ')'
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Application . . .',
            minimumInputLength: 0,
        });
    });
</script>
