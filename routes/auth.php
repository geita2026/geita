<?php

use App\Http\Controllers\Auth\authController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\controls\permissionController;
use App\Http\Controllers\controls\rolesController;
use App\Http\Controllers\controls\rolesPermissionsController;
use App\Http\Controllers\controls\userAuditController;
use App\Http\Controllers\profiles\myprofileController;
use App\Http\Controllers\users\usersController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('forgot-password/challenge/{token}', [PasswordResetLinkController::class, 'showChallenge'])
        ->name('password.challenge');

    Route::post('forgot-password/verify', [PasswordResetLinkController::class, 'verifyChallenge'])
        ->name('password.challenge.verify');

    Route::post('forgot-password/challenge/{token}/regenerate', [PasswordResetLinkController::class, 'regenerateChallenges'])
        ->name('password.challenge.regenerate');

    Route::get('forgot-password/reset/{token}', [PasswordResetLinkController::class, 'showResetForm'])
        ->name('password.reset.form');

    Route::post('forgot-password/reset', [PasswordResetLinkController::class, 'resetPassword'])
        ->name('password.reset.submit');

    // Legacy email token reset (kept for backward compatibility with emailed links)
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::get(date('dmY') . '/{data}', [authController::class, 'startApplicationFunction'])->name(date('dmY'));

Route::prefix('account')->middleware('auth')->group(function () {
    Route::resource('controls', rolesPermissionsController::class);
    Route::resource('audits', userAuditController::class);
    Route::resource('roles', rolesController::class);
    Route::resource('users', usersController::class);
    Route::resource('permissions', permissionController::class);
    Route::resource('profiles', myprofileController::class);
    Route::get('verify_email/{token}', [authController::class, 'activateAccount'])->name('verify_email');
    Route::post('resert_password', [authController::class, 'resertPassword'])->name('resertPassword');
});


Route::prefix('account')->group(function () {
    Route::get('registration', [authController::class, 'Registration'])->name('registration');
    Route::post('register', [authController::class, 'register'])->name('register');
});
