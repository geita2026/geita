<?php

namespace App\Traits;

use App\Models\setting\DefaultSettings;

trait System
{
    public function checkApplicationStatus()
    {
        $defaultSettings = DefaultSettings::first();

        // Preserve booking access if settings have not yet been configured.
        // Explicit CLOSED status still blocks booking.
        if ($defaultSettings === null) {
            return true;
        }

        return strtoupper(trim((string) $defaultSettings->applicationStatus)) !== 'CLOSED';
    }
}