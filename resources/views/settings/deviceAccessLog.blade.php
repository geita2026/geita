<x-app-layout>
    <title>{{ env('APP_NAME') }} | Device Access Log</title>

    <div class="content">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">
                    <i class="icon-tablet position-left"></i> Device / PIN access log
                </h5>
                <div class="heading-elements">
                    <span class="badge bg-blue">{{ $logs->total() }} records</span>
                </div>
            </div>

            <div class="panel-body">
                <form method="GET" class="form-inline" style="margin-bottom:15px;">
                    <div class="form-group" style="margin-right:8px;">
                        <input type="text" name="q" value="{{ $search }}"
                               class="form-control" placeholder="device, agent, POS, IP…">
                    </div>
                    <div class="form-group" style="margin-right:8px;">
                        <select name="action" class="form-control">
                            <option value="">All actions</option>
                            <option value="pin" {{ $action === 'pin' ? 'selected' : '' }}>PIN</option>
                            <option value="login" {{ $action === 'login' ? 'selected' : '' }}>Login</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-filter3 position-left"></i> Filter
                    </button>
                    <a href="{{ route('device_access_log.index') }}" class="btn btn-default">Reset</a>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Action</th>
                                <th>Result</th>
                                <th>Device</th>
                                <th>Device ID</th>
                                <th>Agent</th>
                                <th>POS</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td style="white-space:nowrap">{{ $log->created_at }}</td>
                                    <td>
                                        <span class="label label-default text-uppercase">{{ $log->action }}</span>
                                    </td>
                                    <td>
                                        @if ($log->result)
                                            <span class="label label-success">OK</span>
                                        @else
                                            <span class="label label-danger">FAIL</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->device_name ?: '—' }}</td>
                                    <td><small class="text-muted">{{ $log->device_id ?: '—' }}</small></td>
                                    <td>{{ $log->agent_name ?: '—' }}</td>
                                    <td>{{ $log->pos_name ?: '—' }}</td>
                                    <td>{{ $log->ip ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        No access records yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
