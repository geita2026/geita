<?php

namespace App\Traits;

use App\Models\setting\DefaultSettings;

trait System
{
    public function checkApplicationStatus(): bool
    {
        $status = DefaultSettings::first()?->applicationStatus;

        return $status !== null && $status !== 'CLOSED';
    }
}
