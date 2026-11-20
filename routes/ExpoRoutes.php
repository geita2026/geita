<?php

use App\Http\Controllers\ExpoControllers\ExpoBackEndController;
use App\Http\Controllers\ExpoControllers\ExpoFrontEndController;
use Illuminate\Support\Facades\Route;

#----------- PRIVATE ROUTES -------#
Route::prefix('expo2025')->middleware('auth')->group(function() {
    Route::get('confirm_participation/{participationId}',[ExpoBackEndController::class,'approveConfirmedParticipant'])->name('expo.confirm_participation');
    Route::get('expo_dashboard',[ExpoBackEndController::class,'ExpoDashboard'])->name('expo_dashboard');
});


#------------- PUBLIC ROUTES ------- #
Route::get('expo2025',[ExpoFrontEndController::class,'HomePage'])->name('expo2025');

Route::prefix('expo2025')->group(function () {
    Route::get('aboutExpo',[ExpoFrontEndController::class,'AboutExpoPage'])->name('aboutExpo');
    Route::get('tanzania_pavilion',[ExpoFrontEndController::class,'TanzaniaPavilion'])->name('tanzania_pavilion');
    Route::get('expo_program',[ExpoFrontEndController::class,'ExpoPrograms'])->name('expo_program');
    Route::get('expo_registration',[ExpoFrontEndController::class,'ExpoRegistration'])->name('expo_registration');
    Route::post('expo_reg',[ExpoFrontEndController::class,'SaveExpoData'])->name('expo_reg');
    Route::get('preview_data/{participantId}',[ExpoFrontEndController::class,'PreviewParticipant'])->name('preview_data');
    Route::post('update_data',[ExpoFrontEndController::class,'updateParticipant'])->name('update_data');
    Route::get('registered',[ExpoFrontEndController::class,'viewRegistered'])->name('registered');
    Route::get('join_tanzania',[ExpoFrontEndController::class,'thirdPartRegistrationPage'])->name('join_tanzania');
    Route::post('third_part_reg',[ExpoFrontEndController::class,'thirdPartRegistration'])->name('third_part_reg');

    
});
