<?php
use App\Http\Controllers\FrontEnd\AuthController;
use App\Http\Controllers\Advertisements\AdvertisementsController;
use App\Http\Controllers\Advertisements\TextAdvertisementController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function() {
Route::resource('advertisements',AdvertisementsController::class);
Route::resource('text_advertisements',TextAdvertisementController::class);
});
//
