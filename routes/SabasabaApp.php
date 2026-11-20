<?php

use App\Http\Controllers\Advertisements\TextAdvertisementController;
use App\Http\Controllers\APIs\SabaSabaAppController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function() {
    Route::post('get-token',[SabaSabaAppController::class,'getToken'])->name('get-token');
    Route::post('get-all-events',[SabaSabaAppController::class,'getAllEvents'])->name('get-all-events');
    Route::post('get-all-sectors',[SabaSabaAppController::class,'getAllSectors'])->name('get-all-sectors');
    Route::post('get-exhibitors-by-event-id',[SabaSabaAppController::class,'getExhibitorsByEventId'])->name('get-exhibitors-by-event-id');
    Route::post('get-exhibitors-by-sector-id',[SabaSabaAppController::class,'getExhibitorsBySectorId'])->name('get-exhibitors-by-sector-id');
    Route::post('get-exhibitor-location',[SabaSabaAppController::class,'getExhibitorLocation'])->name('get-exhibitor-location');
    Route::post('search-exhibitor',[SabaSabaAppController::class,'searchExhibitor'])->name('search-exhibitor');
    Route::post('get-advertisements',[TextAdvertisementController::class,'getAppAdvert'])->name('get-advertisements');

});