<?php
use App\Http\Controllers\APIs\ValidationApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('gateCollection')->group(function () {
    Route::resource('verification',ValidationApiController::class);
    // Offline LAN hub pre-sync: read-only snapshot of server-issued
    // (online/MPESA/web) tickets so the hub can validate them with no internet.
    Route::get('today-tickets',[ValidationApiController::class,'todayTickets']);
    Route::post('gate_verification',[ValidationApiController::class,'eGateVerification']);
    Route::post('open_gate',[ValidationApiController::class,'openGate']);
    Route::get('load-test-tickets',[ValidationApiController::class,'loadTestTickets']);
    Route::post('load-test-scan-status',[ValidationApiController::class,'loadTestScanStatus']);
    // Public but throttled — 10 req/min/IP makes brute-force ~7 million minutes
    Route::post('validate-settings-pin',[ValidationApiController::class,'validateSettingsPin'])->middleware('throttle:10,1');
});
