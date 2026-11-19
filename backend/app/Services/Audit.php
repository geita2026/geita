<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

/** FR-048 / BR-007: one call per significant action. */
class Audit
{
    public static function log(string $action, string $summary, ?Model $entity = null, ?array $old = null, ?array $new = null, ?string $reference = null): void
    {
        $user = auth()->user() ?? auth('sanctum')->user();

        AuditLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'action' => $action,
            'reference' => $reference ?? ($entity?->reference ?? $entity?->number ?? $entity?->code ?? null),
            'entity_type' => $entity ? class_basename($entity) : null,
            'entity_id' => $entity?->getKey(),
            'summary' => $summary,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()?->ip(),
        ]);
    }

    /** Old/new pairs for only the attributes that changed on a saved model. */
    public static function diff(Model $model): array
    {
        $new = collect($model->getChanges())->except(['updated_at'])->all();
        $old = collect($new)->map(fn ($_, $key) => $model->getOriginal($key))->all();

        return [$old, $new];
    }
}
