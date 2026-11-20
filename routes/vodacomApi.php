<?php

use App\Http\Controllers\APIs\TicketsApiControllerVoda;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
   Route::post('services',[TicketsApiControllerVoda::class,'getAllServices'])->name('services');
   Route::post('customer_requests',[TicketsApiControllerVoda::class,'postCustomerRequests'])->name('customer_requests');
   Route::post('fetch_control_number',[TicketsApiControllerVoda::class,'checkControlNumber'])->name('fetch_control_number');
   Route::post('reconcile_transaction',[TicketsApiControllerVoda::class,'getTickets'])->name('reconcile_transaction');
   Route::post('report_collections',[TicketsApiControllerVoda::class,'payCollection'])->name('report_collections');
   // Route::post('post_payments',[TicketsApiControllerVoda::class,'postPayments'])->name('post_payments');
});
