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
                <li class="active">List Of event type</li>
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
                    <h4 class=" text-center">LIst Of Trade Promotion Event Types</h4>
                </div>
                <div class=" form-group text-right">
                    <a href="{{ route('event_types.create') }}" class=" btn btn-success">New
                        Event Type <i class=" icon-plus2"></i></a>
                </div>
                <div class=" form-group col-md-12">
                    <table class="table table-responsive table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class=" text-bold">#</th>
                                <th class=" text-bold">Event Type Name</th>
                                <th class=" text-bold">Description</th>
                                <th class=" text-bold">Permit Cost</th>
                                <th class=" text-bold">Permit Poster</th>
                                <th class=" text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
    
                            @foreach ($eventTypes as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class=" text-uppercase">{{ $item->promotionEventTypeName }}</td>
                                    <td>{{ $item->promotionEventTypeDescription }}</td>
                                    <td>{{ number_format($item->eventTypeAmountTzs,2) }} TZS | {{ number_format($item->eventTypeAmountUsd,2) }} USD</td>
                                    <td>
                                        <img src="{{ $item->eventTypePoster }}" height="50" width="200">
                                    </td>
                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li>
                                                        <a href="{{ route('event_types.edit', ['event_type' => $item->eventTypeId]) }}"
                                                            class=" btn btn-info">View Application <i
                                                                class=" icon-eye"></i></a>
                                                    </li>
                                                    <li>
                                                        <a href="" data-toggle="modal"
                                                            data-target="#DeleteEventType{{ $item->eventTypeId }}"
                                                            class="btn btn-danger"> Cancel Application <i
                                                                class=" icon-bin"></i></a>
    
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>
                                    {{-- delete modal --}}
                                    <div id="DeleteEventType{{ $item->eventTypeId }}" class="modal fade"
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
                                                        action="{{ route('event_types.destroy', ['event_type' => $item->eventTypeId]) }}"
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
