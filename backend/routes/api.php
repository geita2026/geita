<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AllocationController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\ExhibitionController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SpaceController;
use Illuminate\Support\Facades\Route;

/*
| GeMEMIS REST API, version 1 (FR-062). Every route below `auth:sanctum` needs a
| bearer token; role checks happen in the controllers through User::allowed().
| docs/api.md is the reference — keep the two in step.
*/
Route::prefix('v1')->group(function () {
    Route::get('health', fn () => ['status' => 'ok', 'time' => now()->toIso8601String()]);

    Route::middleware('throttle:10,1')->group(function () {
        Route::post('auth/login', [AuthController::class, 'login']);
        Route::post('auth/register', [AuthController::class, 'register']);
    });
    Route::get('meta', fn () => [
        'roles' => config('gememis.roles'),
        'space_statuses' => config('gememis.space_statuses'),
        'manual_space_statuses' => config('gememis.manual_space_statuses'),
        'application_statuses' => config('gememis.application_statuses'),
        'invoice_statuses' => config('gememis.invoice_statuses'),
    ]);

    // FR-059: called by the payment platform, authenticated by HMAC signature.
    Route::post('integrations/payments/callback', [FinanceController::class, 'gatewayCallback'])->middleware('throttle:120,1');

    Route::middleware(['auth:sanctum', 'account'])->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::put('auth/password', [AuthController::class, 'changePassword']);
        Route::put('auth/profile', [AuthController::class, 'updateProfile']);

        Route::get('search', [ReportController::class, 'search']);
        Route::get('notifications', [AdminController::class, 'notifications']);
        Route::post('notifications/read', [AdminController::class, 'readNotifications']);

        // Exhibitions, halls, categories
        Route::get('exhibitions', [ExhibitionController::class, 'index']);
        Route::post('exhibitions', [ExhibitionController::class, 'store']);
        Route::get('exhibitions/{exhibition}', [ExhibitionController::class, 'show']);
        Route::put('exhibitions/{exhibition}', [ExhibitionController::class, 'update']);
        Route::post('exhibitions/{exhibition}/transition', [ExhibitionController::class, 'transition']);
        Route::post('exhibitions/{exhibition}/halls', [ExhibitionController::class, 'storeHall']);
        Route::put('halls/{hall}', [ExhibitionController::class, 'updateHall']);
        Route::delete('halls/{hall}', [ExhibitionController::class, 'destroyHall']);
        Route::post('exhibitions/{exhibition}/categories', [ExhibitionController::class, 'storeCategory']);
        Route::put('categories/{category}', [ExhibitionController::class, 'updateCategory']);
        Route::delete('categories/{category}', [ExhibitionController::class, 'destroyCategory']);

        // Spaces and the map
        Route::get('exhibitions/{exhibition}/spaces', [SpaceController::class, 'index']);
        Route::post('exhibitions/{exhibition}/spaces', [SpaceController::class, 'store']);
        Route::post('exhibitions/{exhibition}/spaces/generate', [SpaceController::class, 'generate']);
        Route::put('spaces/{space}', [SpaceController::class, 'update']);
        Route::post('spaces/{space}/status', [SpaceController::class, 'status']);
        Route::delete('spaces/{space}', [SpaceController::class, 'destroy']);

        // Applications and documents
        Route::get('applications', [ApplicationController::class, 'index']);
        Route::post('applications', [ApplicationController::class, 'store']);
        Route::get('applications/{application}', [ApplicationController::class, 'show']);
        Route::put('applications/{application}', [ApplicationController::class, 'update']);
        Route::post('applications/{application}/submit', [ApplicationController::class, 'submit']);
        Route::post('applications/{application}/review', [ApplicationController::class, 'review']);
        Route::post('applications/{application}/cancel', [ApplicationController::class, 'cancel']);
        Route::post('applications/{application}/documents', [ApplicationController::class, 'uploadDocument']);
        Route::get('documents/{document}', [ApplicationController::class, 'downloadDocument']);
        Route::delete('documents/{document}', [ApplicationController::class, 'destroyDocument']);
        Route::post('applications/{application}/auto-allocate', [AllocationController::class, 'auto']);
        Route::get('applications/{application}/allocation-letter', [DocumentController::class, 'allocationLetter']);

        // Allocations, check-in
        Route::get('allocations', [AllocationController::class, 'index']);
        Route::post('allocations', [AllocationController::class, 'store']);
        Route::post('allocations/{allocation}/confirm', [AllocationController::class, 'confirm']);
        Route::post('allocations/{allocation}/reallocate', [AllocationController::class, 'reallocate']);
        Route::post('allocations/{allocation}/transfer', [AllocationController::class, 'transfer']);
        Route::post('allocations/{allocation}/cancel', [AllocationController::class, 'cancel']);
        Route::post('allocations/{allocation}/occupancy', [AllocationController::class, 'occupancy']);
        Route::post('exhibitions/{exhibition}/auto-allocate', [AllocationController::class, 'autoBatch']);

        // Billing
        Route::get('invoices', [FinanceController::class, 'invoices']);
        Route::get('invoices/{invoice}', [FinanceController::class, 'invoice']);
        Route::get('invoices/{invoice}/pdf', [DocumentController::class, 'invoice']);
        Route::post('invoices/{invoice}/payments', [FinanceController::class, 'submitPayment']);
        Route::get('payments', [FinanceController::class, 'payments']);
        Route::post('payments/{payment}/verify', [FinanceController::class, 'verify']);
        Route::get('payments/{payment}/evidence', [FinanceController::class, 'evidence']);
        Route::get('payments/{payment}/receipt', [DocumentController::class, 'receipt']);

        // Dashboard and reports
        Route::get('exhibitions/{exhibition}/dashboard', [ReportController::class, 'dashboard']);
        Route::get('exhibitions/{exhibition}/reports/{type}', [ReportController::class, 'report']);
        Route::get('exhibitions/{exhibition}/schedule', [DocumentController::class, 'schedule']);

        // Directory and administration
        Route::get('exhibitors', [AdminController::class, 'exhibitors']);
        Route::get('exhibitors/{exhibitor}', [AdminController::class, 'exhibitor']);
        Route::put('exhibitors/{exhibitor}', [AdminController::class, 'updateExhibitor']);
        Route::get('users', [AdminController::class, 'users']);
        Route::post('users', [AdminController::class, 'storeUser']);
        Route::put('users/{user}', [AdminController::class, 'updateUser']);
        Route::post('users/{user}/reset-password', [AdminController::class, 'resetPassword']);
        Route::get('audit-logs', [AdminController::class, 'audit']);
    });
});
