<?php
use App\Http\Controllers\Auth\VerifyQrCodeController;
use App\Http\Controllers\Catalogy\CatalogyController;
use App\Http\Controllers\company\SelfRegistrationController;
use App\Http\Controllers\Exhibition\ChartSessionController;
use App\Http\Controllers\FrontEnd\Booking\CustomerBookingController;
use App\Http\Controllers\FrontEnd\Booking\CustomerSpaceBookingController;
use App\Http\Controllers\FrontEnd\Booking\ViewSpacesController;
use App\Http\Controllers\FrontEnd\Invoice\InvoiceController;
use App\Http\Controllers\FrontEnd\PreviewProgramController;
use App\Http\Controllers\FrontEnd\PublicPagesController;
use App\Http\Controllers\FrontEnd\RegistrationController;
use App\Http\Controllers\FrontEnd\SabasabaRegController;
use App\Http\Controllers\FrontEnd\VirtualExhibition\VirtualExhibitionController;
use App\Http\Controllers\Guidelines\UserGuideController;
use App\Http\Controllers\sponsorships\CSponsorshipBookingController;
use Illuminate\Support\Facades\Route;

Route::resource('registrations', RegistrationController::class);
Route::resource('space-categories', CustomerSpaceBookingController::class);
Route::resource('space-list', ViewSpacesController::class);
Route::resource('customer_spaces', CustomerBookingController::class);
Route::get('get_particulars', [InvoiceController::class, 'get_particulars'])->name('getParticulars'); // get particulars
Route::get('About_ditf', [PublicPagesController::class, 'About_ditf'])->name('About_ditf');
Route::get('ditf_map', [PublicPagesController::class, 'ditf_map'])->name('ditf_map');
Route::resource('experience', PublicPagesController::class);
Route::resource('exhibited_products', VirtualExhibitionController::class);
Route::get('user_guide', [UserGuideController::class, 'userGuide'])->name('user_guide');
Route::resource('chart_session', ChartSessionController::class);
Route::resource('preview_programs', PreviewProgramController::class);
Route::resource('self_registration', SelfRegistrationController::class);
Route::resource('discover_exhibitor', CatalogyController::class);
Route::get('sabasaba_visitors', [CatalogyController::class,'sabaSabaVisitors'])->name('sabasaba_visitors');


#************BOOKING ENQUIRY *********** #
Route::resource('exhibition_space_booking',CustomerSpaceBookingController::class);
Route::resource('sponsorship_packages',CSponsorshipBookingController::class);

Route::get('verify/{data}',[VerifyQrCodeController::class,'VerifyQrCodeFunction'])->name('verify');

#---- Prefair seminar ---#
Route::prefix('prefairSeminar')->group(function() {
    Route::get('prefair-semina-registration',[SabasabaRegController::class,'PrefairSeminarRegPage'])->name('prefair-semina-registration');
    Route::post('save-prefair-semina-data',[SabasabaRegController::class,'PrefairSeminarReg'])->name('save-prefair-semina-data');

    Route::get('prefair-semina-feedback',[SabasabaRegController::class,'FeedbBackPage'])->name('prefair-semina-feedback');
    Route::post('prefair-semina-feedback-reg',[SabasabaRegController::class,'FeedBackReg'])->name('prefair-semina-feedback-reg');
});