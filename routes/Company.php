<?php

use App\Http\Controllers\company\AssociateCompanyController;
use App\Http\Controllers\company\FirstCompanyRegistrationController;
use App\Http\Controllers\company\companyController;
use App\Http\Controllers\company\companyListController;
use App\Http\Controllers\company\companyUserControllers;
use App\Http\Controllers\company\sectorController;
use App\Http\Controllers\dashboard\userDashboardController;
use App\Http\Controllers\ePermit\CompanyRegistrationController;
use App\Http\Controllers\settings\CountriesController;
use Illuminate\Support\Facades\Route;

Route::prefix('customerReg')->middleware('auth')->group(function() {
    
    Route::post('first-company', [FirstCompanyRegistrationController::class, 'store'])->name('companies.first.store');
    Route::resource('companies',companyController::class);
    Route::resource('countries',CountriesController::class);
    Route::post('update_my_company',[userDashboardController::class,'store'])->name('update_my_company');
    Route::resource('associate_companies',AssociateCompanyController::class);
    Route::resource('company_profiles',companyListController::class);
    Route::resource('company_users',companyUserControllers::class);
    Route::resource('sectors',sectorController::class);
    Route::post('register_company',[CompanyRegistrationController::class,'registerCompany'])->name('register_company');
    Route::get('company_profile/{companyId}',[CompanyRegistrationController::class,'CompanyProfile'])->name('company_profile');
    Route::get('mycompanies',[CompanyRegistrationController::class,'myCompanyList'])->name('mycompanies');
    Route::post('update_company',[CompanyRegistrationController::class,'UpdateCompanyFromTRA'])->name('update_company');
});