<?php

use App\Http\Controllers\TradePromotion\EventParticipantController;
use App\Http\Controllers\TradePromotion\EventParticipantProfileController;
use App\Http\Controllers\TradePromotion\PromotionEventController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function() {
Route::resource('promotion_events', PromotionEventController::class);
Route::resource('event_participants',EventParticipantController::class);
Route::post('update_participant',[EventParticipantController::class,'updateParticipant'])->name('update_participant');
});

Route::resource('participants',EventParticipantProfileController::class);
Route::get('tpe/{event}',[EventParticipantProfileController::class,'eventParticipantRegistration'])->name('tpe');