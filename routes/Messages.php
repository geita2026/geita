<?php

use App\Http\Controllers\Exhibition\VirtualMessagesController;
use App\Http\Controllers\messaging\CustomerGroupController;
use App\Http\Controllers\messaging\CustomerRegistrationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function() {
    Route::resource('customer_reg',CustomerRegistrationController::class);
    Route::resource('customer_groups',CustomerGroupController::class);
    Route::resource('messages',VirtualMessagesController::class);
});
