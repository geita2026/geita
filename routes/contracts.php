<?php

use App\Http\Controllers\ContractsManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('contracts-management')->middleware('auth')->name('contracts.')->group(function() {
    Route::get('/', [ContractsManagementController::class, 'index'])->name('index');
});

// Customer's own contract view - No permission check needed, users can view their own contract
Route::get('my-contract', [ContractsManagementController::class, 'myContract'])
    ->middleware('auth')
    ->name('contracts.my-contract');

// Contract Signing App - Fullpage (DEFAULT - No Laravel UI, just Flutter app)
Route::get('contract-signing', [ContractsManagementController::class, 'contractSigningFullpage'])
    ->middleware('auth')
    ->name('contract.signing');

// Contract Signing App - Embedded (With Laravel UI - for admins)
Route::get('contract-signing-embedded', [ContractsManagementController::class, 'contractSigning'])
    ->middleware('auth')
    ->name('contract.signing.embedded');

// Redirect old contract_signing URL to new protected route (for backward compatibility)
Route::get('/contract_signing', function() {
    return redirect()->route('contract.signing');
})->middleware('auth');

