<?php
use App\Http\Controllers\APIs\ValidationApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('gateCollection')->group(function () {
    Route::resource('verification',ValidationApiController::class)->only('store');
    Route::post('gate_verification',[ValidationApiController::class,'eGateVerification']);
});
