<?php

use App\Http\Controllers\settings\DefaultSettingsController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::get('default_settings', [DefaultSettingsController::class, 'index'])->name('default_settings.index');
    Route::patch('default_settings', [DefaultSettingsController::class, 'update'])->name('default_settings.update');


    // ================= SYSTEM COMMANDS =================
    Route::post(
        '/settings/system/command',
        [DefaultSettingsController::class, 'runSystemCommand']
    )->name('settings.runCommand');

});
