<?php

use App\Http\Controllers\APIs\AppEndPointController;
use App\Http\Controllers\APIs\ClientIntergrationController;
use Illuminate\Support\Facades\Route;

Route::prefix('gateCollection')->group(function () {
    Route::post('login', [AppEndPointController::class, 'AppLogin']);
    Route::post('resert-password', [AppEndPointController::class,'resertPassword']);
    Route::post('services',[AppEndPointController::class,'GetServices']);
    Route::post('transactions',[AppEndPointController::class,'postTickets']);
    Route::post('yas_push',[AppEndPointController::class,'yasPosPush']);
    Route::post('yas_status',[AppEndPointController::class,'yasPosStatus']);
    Route::get('/exhibitor_lists',[ClientIntergrationController::class,'ExhibitorList']);
});

//