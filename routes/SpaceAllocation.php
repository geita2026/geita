<?php
use App\Http\Controllers\booking\bookingController;
use App\Http\Controllers\booking\BookingTransferController;
use App\Http\Controllers\booking\eventBookingController;
use App\Http\Controllers\booking\serviceBookingController;
use App\Http\Controllers\Catalogy\CatalogueSettingController;
use App\Http\Controllers\Permits\PermitsController;
use Illuminate\Support\Facades\Route;

Route::prefix('spaceAllocation')->middleware('auth')->group(function() {
    Route::resource('booking',bookingController::class);
    Route::resource('event-booking-spaces',eventBookingController::class);
    Route::resource('book-services',serviceBookingController::class);
    Route::resource('booking_transfer',BookingTransferController::class);
    Route::resource('iopermits',PermitsController::class);
    Route::get('search_permit',[PermitsController::class,'seachPermit'])->name('search_permit');
    Route::post('exhibited_product_changes',[PermitsController::class,'UpdateProductExhibited'])->name('exhibited_product_changes');
    
    Route::get('confirm_participation',[CatalogueSettingController::class,'confirmParticipation'])->name('confirm_participation');
    Route::get('participation_form/{participationId}',[CatalogueSettingController::class,'getParticipationForm'])->name('participation_form');
    Route::put('submit_form/{participationId}',[CatalogueSettingController::class,'uploadParticipationForm'])->name('submit_form');
    Route::get('upload_form/{participationId}',[CatalogueSettingController::class,'goToUploadForm'])->name('upload_form');
    Route::get('pending_bookings',[bookingController::class,'pendingBooking'])->name('pending_bookings');
});