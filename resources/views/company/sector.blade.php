<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sectors</title>

    <style>
        .sector-card {
            background: #fff;
            border-radius: 8px;
        }

        .sector-table thead th {
            background: #f5f7fa;
            font-weight: 600;
            white-space: nowrap;
        }

        .sector-table tbody td {
            vertical-align: middle !important;
        }

        .sector-name {
            font-weight: 600;
            color: #1a237e;
        }

        .sector-desc {
            color: #666;
            max-width: 320px;
            white-space: normal;
        }

        .status-pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pill--active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-pill--suspended {
            background: #ffebee;
            color: #c62828;
        }

        .sector-actions a,
        .sector-actions button {
            margin: 0 4px;
        }

        .sector-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
        }

        .sector-search-form {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 10px;
            flex: 1 1 320px;
        }

        .sector-search-form .form-group {
            margin-bottom: 0;
        }

        .sector-pagination {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .sector-empty {
            text-align: center;
            padding: 40px 20px;
            color: #888;
        }
    </style>

    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="icon-home2 position-left"></i> Home
                    </a>
                </li>
                <li class="active">Sectors</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    <div class="content">
        <div class="panel panel-white sector-card">
            <div class="panel-heading border-bottom">
                <h6 class="panel-title text-bold">Company Sectors</h6>
                <span class="text-muted">Register and manage exhibitor sectors without requiring an image</span>
            </div>

            <div class="panel-body table-responsive">

                @if (session('success'))
                    <div class="alert alert-success alert-styled-left">{{ session('success') }}</div>
                @endif
                @if (session('fail'))
                    <div class="alert alert-danger alert-styled-left">{{ session('fail') }}</div>
                @endif

                <div class="sector-toolbar">
                    <form method="GET" action="{{ route('sectors.index') }}" class="sector-search-form">
                        <div class="form-group">
                            <label class="text-muted text-size-mini">Search</label>
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Search by name or description..."
                                   value="{{ $search ?? request('search') }}">
                        </div>
                        <div class="form-group">
                            <label class="text-muted text-size-mini">Status</label>
                            <select name="status" class="form-control">
                                <option value="">All</option>
                                <option value="1" @selected(request('status') === '1')>Active</option>
                                <option value="0" @selected(request('status') === '0')>Suspended</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-search4"></i> Search
                            </button>
                            @if (request()->hasAny(['search', 'status']))
                                <a href="{{ route('sectors.index') }}" class="btn btn-default">Clear</a>
                            @endif
                        </div>
                    </form>
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#add_sector">
                        <i class="icon-add-to-list position-left"></i> Add Sector
                    </button>
                </div>

                <p class="text-muted text-size-small">
                    Showing {{ $sectors->firstItem() ?? 0 }}–{{ $sectors->lastItem() ?? 0 }}
                    of {{ $sectors->total() }} sector(s)
                </p>

                <table class="table table-hover table-bordered sector-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:60px">#</th>
                            <th>Sector Name</th>
                            <th>Description</th>
                            <th class="text-center" style="width:110px">Status</th>
                            <th class="text-center" style="width:120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sectors as $item)
                            <tr>
                                <td class="text-center text-muted">
                                    {{ ($sectors->currentPage() - 1) * $sectors->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <span class="sector-name">{{ $item->sectorName }}</span>
                                </td>
                                <td>
                                    <span class="sector-desc">
                                        {{ $item->sectorDescription ?: '—' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ((int) $item->sectorStatus === 1)
                                        <span class="status-pill status-pill--active">Active</span>
                                    @else
                                        <span class="status-pill status-pill--suspended">Suspended</span>
                                    @endif
                                </td>
                                <td class="text-center sector-actions">
                                    <a href="#"
                                       data-toggle="modal"
                                       data-target="#edit-{{ $item->sectorID }}"
                                       class="text-primary"
                                       title="Edit">
                                        <i class="icon-pencil7"></i>
                                    </a>
                                    <a href="#"
                                       data-toggle="modal"
                                       data-target="#delete-{{ $item->sectorID }}"
                                       class="text-danger"
                                       title="Delete">
                                        <i class="icon-bin"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="sector-empty">
                                    @if (request()->hasAny(['search', 'status']))
                                        No sectors match your search. Try different keywords or
                                        <a href="{{ route('sectors.index') }}">clear filters</a>.
                                    @else
                                        No sectors registered yet. Click <strong>Add Sector</strong> to create one.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="sector-pagination">
                    <span class="text-muted text-size-small">
                        Page {{ $sectors->currentPage() }} of {{ $sectors->lastPage() ?: 1 }}
                    </span>
                    {{ $sectors->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Add sector --}}
    <div id="add_sector" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('sectors.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Add Sector</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Sector Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="sector_name"
                                   class="form-control"
                                   placeholder="e.g. Agriculture"
                                   value="{{ old('sector_name') }}"
                                   required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="sectorDescription"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Short description (optional)">{{ old('sectorDescription') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-ladda btn-ladda-spinner">Save Sector</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit / delete modals (outside table) --}}
    @foreach ($sectors as $item)
        <div id="edit-{{ $item->sectorID }}" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('sectors.update', ['sector' => $item->sectorID]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-primary">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h5 class="modal-title">Edit Sector</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Sector Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="sector_name"
                                       class="form-control"
                                       value="{{ $item->sectorName }}"
                                       required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="sectorDescription"
                                          class="form-control"
                                          rows="3">{{ $item->sectorDescription }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="sectorStatus" class="form-control" required>
                                    <option value="1" @selected((int) $item->sectorStatus === 1)>Active</option>
                                    <option value="0" @selected((int) $item->sectorStatus === 0)>Suspended</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="delete-{{ $item->sectorID }}" class="modal fade">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <form action="{{ route('sectors.destroy', ['sector' => $item->sectorID]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header bg-danger">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h5 class="modal-title">Delete Sector</h5>
                        </div>
                        <div class="modal-body">
                            <p>Delete sector <strong>{{ $item->sectorName }}</strong>? This cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger btn-ladda btn-ladda-spinner">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @if ($errors->any() || (session('fail') && old('sector_name')))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof $ !== 'undefined' && $('#add_sector').length) {
                    $('#add_sector').modal('show');
                }
            });
        </script>
    @endif
</x-app-layout>
