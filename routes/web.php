
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

// ==========================================================
// GeMEMIS HOMEPAGE
// ==========================================================
// Homepage inatumia index() pekee.
// Hatutumii Route::resource() kwa homepage.

Route::get('/', [HomePageController::class, 'index'])
    ->name('home');

// ==========================================================
// GENERAL PUBLIC PAGES
// ==========================================================

Route::get('user_manual', function () {
    return view('test_page');
})->name('user_manual');

// 50th DITF 2026 opening ceremony timetable
Route::view('ditf_timetable', 'ditf_timetable')
    ->name('ditf_timetable');

// ==========================================================
// APPLICATION ROUTE FILES
// ==========================================================

require __DIR__.'/api.php';
require __DIR__.'/auth.php';
require __DIR__.'/badges.php';
require __DIR__.'/Messages.php';
require __DIR__.'/frontEnd.php';
require __DIR__.'/Advertisement.php';
require __DIR__.'/Backend.php';
require __DIR__.'/reports.php';
require __DIR__.'/TradePromotion.php';

// [GEMEMIS-CLEANUP] require __DIR__.'/ePermit.php';

require __DIR__.'/Company.php';
require __DIR__.'/spaceManagement.php';
require __DIR__.'/SpaceAllocation.php';
require __DIR__.'/AccountingModule.php';
require __DIR__.'/SponsorshipManagement.php';
require __DIR__.'/SystemSettings.php';
require __DIR__.'/ExpoRoutes.php';
require __DIR__.'/Exhibitors.php';

// ==========================================================
// AUTHENTICATION ROUTES
// ==========================================================

Route::prefix('auth')->name('auth.')->group(function () {

    Route::post('logout', [authController::class, 'logout'])
        ->name('logout');

    Route::get('verify_email/{token}', [authController::class, 'activateAccount'])
        ->name('verify_email');

    Route::post('resert_password', [authController::class, 'resertPassword'])
        ->name('resertPassword');

});

// ==========================================================
// NIDA INTEGRATION
// ==========================================================

Route::get('nida_req', [TestIntergrationController::class, 'RequestToNida']);

// ==========================================================
// TN BLC COMPETITION
// ==========================================================

Route::get('tnblc', [specialWorkController::class, 'TNBCompetition'])
    ->name('tnblc');

Route::post('submit_art_work', [specialWorkController::class, 'submitArtWork'])
    ->name('submit_art_work');

Route::post('submit_marks', [specialWorkController::class, 'SubmitMarks'])
    ->name('submit_marks');

Route::get('tnblc_marking/{applicantId}', [specialWorkController::class, 'TnblcMarking'])
    ->name('tnblc_markings');

Route::get('send_notification/{applicantId}', [specialWorkController::class, 'sendNotificationToApplicants'])
    ->name('send_notification');

Route::get('verify_attending/{applicantId}', [specialWorkController::class, 'verifyAttending'])
    ->name('verify_attending');

// ==========================================================
// GeMEMIS LANGUAGE SWITCH
// ==========================================================

Route::get('/lang/{locale}', function ($locale) {

    if (!in_array($locale, ['en', 'sw'])) {
        abort(404);
    }

    session(['locale' => $locale]);

    return redirect()->back();

})->name('lang.switch');
