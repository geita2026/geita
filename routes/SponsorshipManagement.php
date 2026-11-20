<?php

use App\Http\Controllers\Home\HomePageController;
use App\Http\Controllers\sponsorships\customerpackagesController;
use App\Http\Controllers\sponsorships\OurSponsorController;
use App\Http\Controllers\sponsorships\PackageGroupController;
use App\Http\Controllers\sponsorships\PublicSponsorshipBookingController;
use App\Http\Controllers\sponsorships\SponsorshipBenefitController;
use App\Http\Controllers\sponsorships\sponsorshipBookingController;
use App\Http\Controllers\sponsorships\sponsorshipController;
use App\Http\Controllers\sponsorships\SponsorshipSpacesController;
use Illuminate\Support\Facades\Route;

Route::prefix('sponsorshipManagement')->middleware('auth')->group(function() {
    Route::resource('sponsorships',sponsorshipController::class);
    Route::resource('sponsorship-bookings',sponsorshipBookingController::class);
    Route::resource('customer_packages',customerpackagesController::class);
    Route::resource('book-packages',sponsorshipBookingController::class);
    Route::resource('sponsors',OurSponsorController::class);
    Route::resource('sponsorship_spaces',SponsorshipSpacesController::class);
    Route::resource('sponsorship_benefits',SponsorshipBenefitController::class);
    Route::resource('package_groups',PackageGroupController::class);
});

Route::prefix('sponsorshipManagement')->group(function(){
   Route::get('get_sponsors',[HomePageController::class,'getAllTextAdvertisment'])->name('get_sponsors');
   Route::get('sponsorship_package_groups',[PublicSponsorshipBookingController::class,'sponsorshipPackageGroups'])->name('sponsorship_package_groups');
   Route::get('sponsorship_package_list/{packageId}',[PublicSponsorshipBookingController::class,'sponsorshipPackageList'])->name('sponsorship_package_list');
});