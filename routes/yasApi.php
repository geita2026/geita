<?php

use App\Http\Controllers\APIs\YasPaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| YAS / Tigo Pesa Debit Mandate (USSD PUSH) routes
|--------------------------------------------------------------------------
| Loaded from routes/api.php, so everything here is under the /api prefix
| and the `api` middleware group (no web CSRF).
|
|   POST /api/v1/yas/push       -> initiate USSD push   (spCode/token auth)
|   POST /api/v1/yas/callback   -> YAS posts the outcome (PUBLIC - no auth)
|
| Register this callback URL with YAS:
|   https://<your-domain>/api/v1/yas/callback
|
| The callback is intentionally unauthenticated (YAS has no spCode/token).
| It is protected by: (1) unguessable ReferenceID that must match a row we
| created, (2) idempotency, and (3) you should additionally IP-allowlist the
| YAS source IPs (41.222.182.216/218, 41.222.182.201/202, 41.222.176.164/218)
| at the web server / firewall.
*/

Route::prefix('v1/yas')->group(function () {
    Route::post('push', [YasPaymentController::class, 'initiatePush'])->name('yas.push');

    // Spec labels the callback GET but sends a body; accept both to be safe.
    Route::match(['get', 'post'], 'callback', [YasPaymentController::class, 'callback'])->name('yas.callback');
});
