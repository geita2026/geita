<?php

use App\Http\Controllers\dashboard\userDashboardController;
use App\Http\Controllers\ePermit\ApplicationInvoiceController;
use App\Http\Controllers\ePermit\applicationReviewController;
use App\Http\Controllers\ePermit\ApplyPermitController;
use App\Http\Controllers\ePermit\ePermitController;
use App\Http\Controllers\ePermit\EventTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('ePermit')->middleware('auth')->group(function() {
    Route::get('permitDashboard',[userDashboardController::class,'ePermitDashboard'])->name('permitDashboard');
    Route::resource('apply_permit',ApplyPermitController::class);
    Route::post('request_controlNo',[ApplyPermitController::class,'requestControlNumber'])->name('request_controlNo');
    Route::resource('review_applications',applicationReviewController::class);
    Route::get('permit_list',[applicationReviewController::class,'permitList'])->name('permit_list');
    Route::resource('app_invoices',ApplicationInvoiceController::class);
    Route::resource('event_types',EventTypeController::class);
});


Route::prefix('ePermit')->group(function() {
    Route::get('/',[ePermitController::class,'AboutPermit'])->name('/');
});

Route::prefix('ePermit')->group(function() {
    Route::get('approved_events',[ePermitController::class,'ApprovedEvents'])->name('approved_events');
});
