<?php

use Illuminate\Support\Facades\Schedule;

// Needs one cron entry on the server: * * * * * php artisan schedule:run (docs/operations.md).
Schedule::command('gememis:housekeeping')->dailyAt('06:00')->withoutOverlapping();
Schedule::command('sanctum:prune-expired --hours=24')->daily();
// FR-059: compare yesterday's payments with GePG's records.
Schedule::command('gepg:reconcile')->dailyAt('07:00')->withoutOverlapping();
