<?php

use App\Http\Controllers\APIs\JudgingController;
use Illuminate\Support\Facades\Route;
Route::prefix('v1/exhibitors')->group(function() {
        Route::resource('ditf_participants',JudgingController::class);
});