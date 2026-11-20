<?php
use App\Http\Controllers\Badges\BadgeGroupController;
use App\Http\Controllers\Badges\BadgesAllocationController;
use App\Http\Controllers\Badges\badgesBackupController;
use App\Http\Controllers\Badges\BadgesController;
use App\Http\Controllers\Badges\BadgePreviewController;
use App\Http\Controllers\Badges\EmergenceBadgesController;
use App\Http\Controllers\Badges\PrepareBadgeController;
use App\Http\Controllers\Badges\ReUseBadgesController;
use App\Http\Controllers\Badges\ViewBadgesController;
use App\Http\Controllers\Printing\facialNameController;
use App\Http\Controllers\Printing\facialNameGroupController;
use App\Http\Controllers\Stickers\CarStickerController;
use App\Http\Controllers\Stickers\PrintStickerController;
use App\Http\Controllers\Stickers\StickerAllocationController;
use Illuminate\Support\Facades\Route;

// ── Badge Preview & PDF — accessible without login (share/print links) ──
Route::get('badge_preview/{member_id}', [BadgePreviewController::class, 'show'])->name('badge_preview');
Route::get('badge_pdf/{member_id}',     [BadgePreviewController::class, 'pdf'])->name('badge_pdf');

Route::prefix('badgesManagement')->middleware('auth')->group(function() {
Route::resource('exhibitors_list', BadgesController::class);
Route::resource('prepare_badges',PrepareBadgeController::class);
Route::resource('badge_groups',BadgeGroupController::class);
Route::resource('print_badges',ViewBadgesController::class);
Route::get('print_back_page',[ViewBadgesController::class,'backPage'])->name('print_back_page');
Route::resource('facial_name_groups',facialNameGroupController::class);
Route::resource('facial_name_companies',facialNameController::class);
Route::resource('badge_allocations',BadgesAllocationController::class);
Route::resource('reuse_badges',ReUseBadgesController::class);

Route::resource('stickers',CarStickerController::class);
Route::resource('print_stickers',PrintStickerController::class);
Route::resource('allocate_stickers',StickerAllocationController::class);

Route::resource('emergency_badges',EmergenceBadgesController::class);

Route::get('vip',[EmergenceBadgesController::class,'index'])->name('vip');

Route::resource('badges_backup',badgesBackupController::class);

});
