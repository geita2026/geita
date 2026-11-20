<?php

use App\Http\Controllers\reports\mySpaceController;
use App\Http\Controllers\space\additionaServiceController;
use App\Http\Controllers\space\boothCategoryController;
use App\Http\Controllers\space\boothController;
use App\Http\Controllers\space\pavilionController;
use App\Http\Controllers\space\spaceCategoryController;
use App\Http\Controllers\space\StreetController;
use Illuminate\Support\Facades\Route;

Route::prefix('spaceManagement')->middleware('auth')->group(function() {
    Route::resource('pavilions',pavilionController::class);
    Route::resource('boothCategories',boothCategoryController::class);
    Route::resource('spaceCategories',spaceCategoryController::class);
    Route::resource('additional_services',additionaServiceController::class);
    Route::resource('booths',boothController::class);
    Route::resource('my-spaces',mySpaceController::class);
    Route::resource('streets',StreetController::class);
});