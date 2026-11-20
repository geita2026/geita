<?php

namespace App\Models\setting;

use App\Models\Tickets\PosAgent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * One device access event — an admin-PIN validation or an app login — recorded
 * for audit. Displayed on the admin "Device access log" page.
 */
class DeviceAccessLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'action',
        'device_id',
        'device_name',
        'agent_id',
        'agent_name',
        'pos_name',
        'result',
        'ip',
        'created_at',
    ];

    /**
     * Records an access event. NEVER throws — auditing must not break the
     * request that triggered it. When [agentId] is not given but the caller
     * sent a `token`, the agent is resolved from it (the Settings-PIN case,
     * where the device is already logged in).
     */
    public static function record(
        Request $request,
        string $action,
        bool $result,
        ?string $agentId = null,
        ?string $agentName = null,
        ?string $posName = null
    ): void {
        try {
            if (!$agentId && $request->filled('token')) {
                $agent = PosAgent::where('agentToken', $request->input('token'))->first();
                $agentId = $agent?->agentId;
                $agentName = $agent?->agentName;
            }

            self::create([
                'action' => $action,
                'device_id' => (string) $request->input('device_id', ''),
                'device_name' => (string) $request->input('device_name', ''),
                // Fall back to agent details the (logged-in) app sends with the
                // request — e.g. the admin-PIN call, where no token is passed.
                'agent_id' => $agentId ?: $request->input('agent_id'),
                'agent_name' => $agentName ?: $request->input('agent_name'),
                'pos_name' => $posName ?: (string) $request->input('pos_name', ''),
                'result' => $result,
                'ip' => $request->ip(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // swallow — never break the caller because of audit logging
        }
    }
}
