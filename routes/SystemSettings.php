<?php

use App\Http\Controllers\settings\DefaultSettingsController;
use App\Http\Controllers\settings\DeviceAccessLogController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::get('default_settings', [DefaultSettingsController::class, 'index'])->name('default_settings.index');
    Route::patch('default_settings', [DefaultSettingsController::class, 'update'])->name('default_settings.update');

    // Audit: which device/agent used the admin PIN or logged in.
    Route::get('device_access_log', [DeviceAccessLogController::class, 'index'])->name('device_access_log.index');

    // ================= SYSTEM COMMANDS =================
    Route::post(
        '/settings/system/command',
        [DefaultSettingsController::class, 'runSystemCommand']
    )->name('settings.runCommand');

});
