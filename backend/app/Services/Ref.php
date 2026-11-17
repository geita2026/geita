<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/** Human reference numbers derived from the row id, so they are unique without a counter table. */
class Ref
{
    public static function temp(): string
    {
        return 'TMP-'.Str::uuid();
    }

    public static function assign(Model $model, string $column, string $prefix, string $scope = ''): void
    {
        $model->forceFill([
            $column => trim($prefix.'-'.$scope, '-').'-'.str_pad((string) $model->getKey(), 5, '0', STR_PAD_LEFT),
        ])->saveQuietly();
        // Pick up column defaults (status and the like) the insert did not set.
        $model->refresh();
    }
}
