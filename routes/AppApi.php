<?php

use App\Http\Controllers\APIs\ClientIntergrationController;
use Illuminate\Support\Facades\Route;

Route::prefix('gateCollection')->group(function () {
    Route::get('/exhibitor_lists',[ClientIntergrationController::class,'ExhibitorList']);
});

//