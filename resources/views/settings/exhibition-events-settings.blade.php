<x-app-layout>
    <title>{{ env('APP_NAME') }} | Exhibition Events</title>

    <style>
        table th {
            background: #f8fafc;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            white-space: nowrap;
        }

        .event-name {
            font-weight: 700;
            color: #111827;
        }

        .event-meta {
            font-size: 12px;
            color: #6b7280;
        }

        .status-pill {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-closed {
            background: #fee2e2;
            color: #991b1b;
        }

        .table-hover tbody tr:hover {
            background-color: #f9fafb;
        }
    </style>

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li>
                    <a href="{{ route('Exhibition-Events.index') }}">
                        <i class="icon-home2 position-left"></i> Exhibition Events
                    </a>
                </li>
                <li class="active">Manage Events</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="content">
        <div class="panel panel-white">

            <div class="panel-heading">
                <h6 class="panel-title text-bold">Exhibition Events</h6>
            </div>

            <div class="panel-body">

                {{-- ADD EVENT --}}
                @can('CREATE_EXHIBITION_EVENT')
                <div class="form-group text-right">
                    <button class="btn btn-default border-primary text-primary"
                            data-toggle="modal"
                            data-target="#new_event">
                        Add Event <i class="icon-plus2"></i>
                    </button>
                </div>
                @endcan

                {{-- SINGLE TABLE --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Event</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>

                                <td>
                                    <div class="event-name">{{ $item->event_name }}</div>
                                    <div class="event-meta">
                                        <i class="icon-location4"></i>
                                        {{ $item->event_location }}
                                    </div>
                                </td>

                                <td class="event-meta">
                                    <strong>Start:</strong> {{ $item->event_start_date }}<br>
                                    <strong>End:</strong> {{ $item->event_end_date }}
                                </td>

                                <td>
                                    @if ($item->event_status == 1)
                                        <span class="status-pill status-active">Active</span>
                                    @else
                                        <span class="status-pill status-closed">Closed</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                @can('UPDATE_EXHIBITION_EVENT')
                                                <li>
                                                    <a data-toggle="modal"
                                                       data-target="#edit{{ $item->event_id }}">
                                                        <i class="icon-pencil7"></i> Edit
                                                    </a>
                                                </li>
                                                @endcan
                                                @can('DELETE_EXHIBITION_EVENT')
                                                <li>
                                                    <a data-toggle="modal"
                                                       data-target="#delete{{ $item->event_id }}"
                                                       class="text-danger">
                                                        <i class="icon-bin"></i> Delete
                                                    </a>
                                                </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                            </tr>

                            {{-- EDIT MODAL --}}
                            <div id="edit{{ $item->event_id }}" class="modal fade">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('Exhibition-Events.update', $item->event_id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h4 class="modal-title">Update {{ $item->event_name }}</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label>Event Name *</label>
                                                        <input name="event_name" value="{{ $item->event_name }}" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label>Location *</label>
                                                        <input name="event_location" value="{{ $item->event_location }}" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label>Start Date *</label>
                                                        <input type="date" name="event_start_date" value="{{ $item->event_start_date }}" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label>End Date *</label>
                                                        <input type="date" name="event_end_date" value="{{ $item->event_end_date }}" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-12 form-group">
                                                        <label>Description</label>
                                                        <textarea name="event_descriptions" class="form-control">{{ $item->event_descriptions }}</textarea>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>
                                                            <input type="checkbox" name="event_status" {{ $item->event_status ? 'checked' : '' }}>
                                                            Active Event
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer text-right">
                                                <button class="btn btn-warning" data-dismiss="modal">Close</button>
                                                <button class="btn btn-primary">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- DELETE MODAL --}}
                            <div id="delete{{ $item->event_id }}" class="modal fade">
                                <div class="modal-dialog">
                                    <form action="{{ route('Exhibition-Events.destroy', $item->event_id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <h4 class="modal-title">Delete Event</h4>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete <strong>{{ $item->event_name }}</strong>?
                                            </div>
                                            <div class="modal-footer text-right">
                                                <button class="btn btn-warning" data-dismiss="modal">Cancel</button>
                                                <button class="btn btn-danger">Yes, Delete</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- CREATE MODAL --}}
    @can('CREATE_EXHIBITION_EVENT')
    <div id="new_event" class="modal fade">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('Exhibition-Events.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title">Register New Event</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Event Name *</label>
                                <input name="event_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Location *</label>
                                <input name="event_location" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Start Date *</label>
                                <input type="date" name="event_start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>End Date *</label>
                                <input type="date" name="event_end_date" class="form-control" required>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Description</label>
                                <textarea name="event_descriptions" class="form-control"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label>
                                    <input type="checkbox" name="event_status">
                                    Set as Active Event
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button class="btn btn-warning" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Create Event</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endcan

</x-app-layout>
