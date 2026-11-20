<?php
use App\Http\Controllers\Auth\authController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home\HomePageController;
use App\Http\Controllers\specialWorkController;
use App\Http\Controllers\TestIntergrationController;

/*
|--------------------------------------------------------------------------
| Space Allocation Management System Routing
|--------------------------------------------------------------------------
|
*/

Route::resource('/',HomePageController::class); // home page controller

Route::get('user_manual', function () { return view('test_page'); })->name('user_manual');

// 50th DITF 2026 opening ceremony timetable (live countdown)
Route::view('ditf_timetable', 'ditf_timetable')->name('ditf_timetable');

// Next.js static export (daisyUI + HeroUI) lives at the repo root in /timetable/
// and is served directly by nginx as static files (docroot is the app root).
// URL: https://tradefair.tantrade.go.tz/timetable/



require __DIR__.'/api.php'; 
require __DIR__.'/auth.php'; 
require __DIR__.'/badges.php'; 
require __DIR__.'/Messages.php'; 
require __DIR__.'/frontEnd.php'; 
require __DIR__.'/Advertisement.php'; 
require __DIR__.'/Backend.php'; 
require __DIR__.'/reports.php'; 
require __DIR__.'/Tickets.php'; 
require __DIR__.'/TradePromotion.php'; 
require __DIR__.'/ePermit.php';
require __DIR__.'/Company.php';
require __DIR__.'/spaceManagement.php';
require __DIR__.'/SpaceAllocation.php';
require __DIR__.'/AccountingModule.php';
require __DIR__.'/SponsorshipManagement.php';
require __DIR__.'/SystemSettings.php';
require __DIR__.'/ExpoRoutes.php';
require __DIR__.'/Exhibitors.php';
require __DIR__.'/contracts.php';

// Authentication routes
Route::prefix('auth')->name('auth.')->group(function(){
    Route::post('logout',[authController::class,'logout'])->name('logout');
    Route::get('verify_email/{token}',[authController::class,'activateAccount'])->name('verify_email');
    Route::post('resert_password',[authController::class,'resertPassword'])->name('resertPassword');
});
// end Authentication routes
Route::get('nida_req',[TestIntergrationController::class,'RequestToNida']);


Route::get('tnblc',[specialWorkController::class,'TNBCompetition'])->name('tnblc');
Route::post('submit_art_work',[specialWorkController::class,'submitArtWork'])->name('submit_art_work');
Route::post('submit_marks',[specialWorkController::class,'SubmitMarks'])->name('submit_marks');
Route::get('tnblc_marking/{applicantId}',[specialWorkController::class,'TnblcMarking'])->name('tnblc_markings');
Route::get('send_notification/{applicantId}',[specialWorkController::class,'sendNotificationToApplicants'])->name('send_notification');
Route::get('verify_attending/{applicantId}',[specialWorkController::class,'verifyAttending'])->name('verify_attending');



