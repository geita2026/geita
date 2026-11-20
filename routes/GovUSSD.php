<?php

use App\Http\Controllers\APIs\GovenmentUssdController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/ussd')->group(function() {
    Route::get('get-services',[GovenmentUssdController::class,'getAllServices'])->name('get-services');
});