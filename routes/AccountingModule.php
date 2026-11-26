<?php

use App\Http\Controllers\advice_note\adviceNoteController;
use App\Http\Controllers\advice_note\adviceNoteDeadlineController;
use App\Http\Controllers\advice_note\CanceledInvoiceController;
use App\Http\Controllers\advice_note\WithBalanceInvoiceController;
use App\Http\Controllers\FrontEnd\Invoice\InvoiceController;
use App\Http\Controllers\Payments\PaymentReceiptsController;
use App\Http\Controllers\settings\accountSettingController;
use App\Http\Controllers\settings\BankAccountController;
use App\Http\Controllers\settings\IncomeController;
use Illuminate\Support\Facades\Route;

Route::prefix('accountingManagement')->middleware('auth')->group(function() {
    Route::resource('adviceNoteDeadlines',adviceNoteDeadlineController::class);
    Route::resource('settings', accountSettingController::class);
    Route::resource('adviceNotes',adviceNoteController::class);
    Route::resource('payments',PaymentReceiptsController::class);
    Route::resource('invoice_with_balance',WithBalanceInvoiceController::class);
    Route::resource('bank_accounts',BankAccountController::class);
    Route::resource('canceled_invoices',CanceledInvoiceController::class);
    Route::resource('my_invoices',InvoiceController::class);
    Route::resource('income_list',IncomeController::class);
    Route::get('incomes',[IncomeController::class,'index'])->name('incomes');
    Route::post('add_income',[IncomeController::class,'store'])->name('add_income');

    Route::get('review_invoice_discount/{id}',[adviceNoteController::class,'reviewInvoiceDiscount'])->name('review_invoice_discount');
    Route::post('apply_discount',[adviceNoteController::class,'applyDiscount'])->name('apply_discount');
    Route::post('approve_discount',[adviceNoteController::class,'approveDiscount'])->name('approve_discount');
});
