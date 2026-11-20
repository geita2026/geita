<?php

use App\Http\Controllers\FrontEnd\PublicPagesController;
use App\Http\Controllers\Tickets\AgentController;
use App\Http\Controllers\Tickets\ContactVerificationController;
use App\Http\Controllers\Tickets\gateCollectionController;
use App\Http\Controllers\Tickets\locationControllers;
use App\Http\Controllers\Tickets\OnlineTicketsController;
use App\Http\Controllers\Tickets\posController;
use App\Http\Controllers\Tickets\ReconciliationController;
use App\Http\Controllers\Tickets\TicketBookingControllers;
use App\Http\Controllers\Tickets\TicketManagement;
use App\Http\Controllers\Tickets\VerifyTicketController;
use App\Http\Controllers\Tickets\VipPackageBenefitsController;
use App\Http\Controllers\Tickets\VipTicketPackagesController;
use App\Http\Controllers\Tickets\VipTicketSuppyController;
use Illuminate\Support\Facades\Route;

# ------- AUTHENTICATED ROUTES ----- #
Route::prefix('eTAC')->middleware('auth')->group(function() {
    Route::get('etac_dashboard',[TicketManagement::class,'eTACDashboard'])->name('etac_dashboard');
    Route::resource('vip_ticket_packages',VipTicketPackagesController::class);
    Route::resource('vip_packages_benefits',VipPackageBenefitsController::class);
    Route::resource('ticket_bookings',TicketBookingControllers::class);
    Route::resource('download_ticket',VipTicketSuppyController::class);
    Route::resource('pos',posController::class);
    Route::resource('selling_locations',locationControllers::class);
    Route::resource('agents',AgentController::class);
    Route::post('upload_tickets',[AgentController::class,'uploadTransactions'])->name('upload_tickets');
    Route::resource('gate_Collections',gateCollectionController::class);
    Route::resource('reconciliations',ReconciliationController::class);
});
# ------- END AUTHENTICATED ROUTES ----#


Route::get('about_sabasaba_tickets', [PublicPagesController::class, 'AboutSabaSabaTicket'])->name('about_sabasaba_tickets');
Route::get('car_parkings',[PublicPagesController::class,'carParking'])->name('car_parkings');


Route::resource('contact_verification', ContactVerificationController::class);
Route::resource('myTicket', VipTicketSuppyController::class);
Route::get('ticketCard/{id}', [VipTicketSuppyController::class, 'ticketCard'])->name('ticket_card');
Route::get('remove_ticket/{id}',[OnlineTicketsController::class,'RemoveRequest'])->name('remove_ticket');
# --------- PUBLIC ROUTES -------#
Route::resource('verify',VerifyTicketController::class);


Route::prefix('eTAC')->group(function() {

    Route::get('sabasaba_tickets',[OnlineTicketsController::class,'ticketMtandao'])->name('sabasaba_tickets');
    Route::post('send_verification_code',[OnlineTicketsController::class,'sendVerificationCode'])->name('send_verification_code');
    Route::post('verify_verification_code',[OnlineTicketsController::class,'verifyCode'])->name('verify_verification_code');
    Route::post('change_phone_number',[OnlineTicketsController::class,'changePhoneNumber'])->name('change_phone_number');
    Route::post('buy_ticket',[OnlineTicketsController::class,'buyTicket'])->name('buy_ticket');
    Route::post('buy_ticket_yas',[OnlineTicketsController::class,'buyTicketYas'])->name('buy_ticket_yas');
    Route::post('yas_online_status',[OnlineTicketsController::class,'yasOnlineStatus'])->name('yas_online_status');
    Route::resource('online_tickets',OnlineTicketsController::class);
});