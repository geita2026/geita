<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use App\Models\setting\DeviceAccessLog;
use Illuminate\Http\Request;

class DeviceAccessLogController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q', ''));
        $action = (string) $request->get('action', '');

        $query = DeviceAccessLog::query()->orderByDesc('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('device_id', 'like', "%{$search}%")
                    ->orWhere('device_name', 'like', "%{$search}%")
                    ->orWhere('agent_name', 'like', "%{$search}%")
                    ->orWhere('pos_name', 'like', "%{$search}%")
                    ->orWhere('ip', 'like', "%{$search}%");
            });
        }
        if ($action !== '') {
            $query->where('action', $action);
        }

        $logs = $query->paginate(50)->withQueryString();

        return view('settings.deviceAccessLog', compact('logs', 'search', 'action'));
    }
}
