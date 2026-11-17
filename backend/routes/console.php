<?php

use Illuminate\Support\Facades\Schedule;

// Needs one cron entry on the server: * * * * * php artisan schedule:run (docs/operations.md).
Schedule::command('gememis:housekeeping')->dailyAt('06:00')->withoutOverlapping();
Schedule::command('sanctum:prune-expired --hours=24')->daily();
