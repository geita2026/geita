<?php

use App\Http\Controllers\APIs\IntergrationMappingController;
use App\Http\Controllers\APIs\RegisterApiRequestController;
use App\Http\Controllers\Auth\authController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\controls\permissionController;
use App\Http\Controllers\users\bulckUploadController;
use App\Http\Controllers\Catalogy\CatalogueSettingController;
use App\Http\Controllers\company\templateController;
use App\Http\Controllers\dashboard\userDashboardController;
use App\Http\Controllers\Database\DbManagementController;
use App\Http\Controllers\Downloads\DownloadController;
use App\Http\Controllers\sponsorships\EventSponsorController;
use App\Http\Controllers\Exhibition\EventProgramController;
use App\Http\Controllers\Exhibition\ExhibitorJudgingController;
use App\Http\Controllers\settings\DefaultSettingsController;
use App\Http\Controllers\Exhibition\VirtualPavilionController;
use App\Http\Controllers\Exhibition\VirtualServiceController;
use App\Http\Controllers\ExhibitionProducts\ProductsController;
use App\Http\Controllers\guideline\sacPolicyController;
use App\Http\Controllers\information\helpController;
use App\Http\Controllers\LevelOneMenusController;
use App\Http\Controllers\LevelTwoMenusController;
use App\Http\Controllers\mails\sendPasswordController;
use App\Http\Controllers\messaging\bulkSmsController;
use App\Http\Controllers\messaging\messagingController;
use App\Http\Controllers\off_fair\booking\off_fair_space_booking_Controller;
use App\Http\Controllers\off_fair\booking\scheduleController;
use App\Http\Controllers\off_fair\off_fair_activityController;
use App\Http\Controllers\sponsorships\packageContentController;
use App\Http\Controllers\users\resendPasswordController;
use App\Http\Controllers\off_fair\offFaircustomerController;
use App\Http\Controllers\off_fair\offFairController;
use App\Http\Controllers\off_fair\offFairSpaceController;
use App\Http\Controllers\off_fair\spaceControllers\bookingStatusController;
use App\Http\Controllers\off_fair\spaceControllers\off_fair_space_categoryController;
use App\Http\Controllers\off_fair\spaceControllers\space_size_ranges_costController;
use App\Http\Controllers\off_fair\utilityController;
use App\Http\Controllers\settings\eventController;
use App\Http\Controllers\space\eventSpaceController;
use App\Http\Controllers\Guidelines\GuidelinesController;
use App\Http\Controllers\Logs\ApiLogsController;
use App\Http\Controllers\Search\SearchController;
use App\Http\Controllers\users\CustomerPasswordResertController;
use App\Http\Controllers\Vistors\VistorsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('user-manual', function () {
    return view('user-manual');
})->middleware(['auth'])->name('user-manual');



Route::get('startApp',[userDashboardController::class,'StartApplication'])->name('startApp');
Route::get('dashboard',[userDashboardController::class,'index'])->name('dashboard');

//resource controllers.
Route::group(['middleware' => ['auth']], function() {
    Route::resource('bulkUpload',bulckUploadController::class);
    Route::resource('passwordmails',sendPasswordController::class);
    Route::resource('helps',helpController::class);
    
    Route::resource('downloads',templateController::class);
    Route::resource('resert_password',resendPasswordController::class);
    Route::resource('contents',packageContentController::class);
    Route::resource('messaging',messagingController::class);
    Route::resource('Off-Fair-Customers',offFaircustomerController::class);
    Route::resource('Off-Fair', offFairController::class);
    Route::resource('Off-Fair-Spaces', offFairSpaceController::class);
    Route::resource('Off-Fair-Utilities',utilityController::class);
    Route::resource('off-fair-space-booking',off_fair_space_booking_Controller::class);
    Route::resource('space_categories',off_fair_space_categoryController::class);
    Route::resource('off-fair-activities',off_fair_activityController::class);
    Route::resource('space_size_costs',space_size_ranges_costController::class);
    Route::resource('check-booking-status',bookingStatusController::class);
    Route::resource('Exhibition-Events',eventController::class);
    Route::resource('event-spaces',eventSpaceController::class);
    Route::resource('system_menus_level_one',LevelOneMenusController::class);
    Route::resource('system_menus_level_two',LevelTwoMenusController::class);
    Route::resource('off_fair_booking_schedule',scheduleController::class);
    Route::resource('bulk_sms',bulkSmsController::class);
    Route::get('fetch-permissions',[permissionController::class,'fetchPermissions'])->name('fetchPermissions');
    Route::resource('api_requests',RegisterApiRequestController::class);
    Route::resource('api_logs',ApiLogsController::class);
    Route::resource('ditf_guidelines',GuidelinesController::class);
    
    Route::resource('vistors',VistorsController::class);
    Route::resource('virtual_pavilions',VirtualPavilionController::class);
    Route::resource('virtual_services',VirtualServiceController::class);
   
 
    Route::resource('event_programs',EventProgramController::class);
    Route::resource('recoginized_sponsors',EventSponsorController::class);
    Route::resource('intergration_mapping',IntergrationMappingController::class);
    Route::resource('resertCustomerPassword',CustomerPasswordResertController::class);
    Route::resource('catalogue_settings',CatalogueSettingController::class);
    Route::resource('db_management',DbManagementController::class);
    Route::resource('products',ProductsController::class);
    Route::get('download_permit{id}',[DownloadController::class,'DownloadPermit'])->name('download_permit');
    Route::resource('exhibitor_judging',ExhibitorJudgingController::class);
});

Route::resource('terms_and_condition',sacPolicyController::class);  //

Route::prefix('auth')->name('auth.')->group(function(){
    // Route::post('login',[authController::class,'login'])->name('login');
    Route::post('logout',[authController::class,'logout'])->name('logout');
    // Route::post('register',[authController::class,'register'])->name('register');
    Route::post('resert_password',[authController::class,'resertPassword'])->name('resertPassword');
});


Route::controller(SearchController::class)->group(function(){
    Route::post('verify_tsrn', 'TsrnVerification')->name('verify_tsrn');
    Route::post('search_event', 'SearchEvent')->name('search_event');
    Route::get('search_companies','searchCompanies')->name('search_companies');
    Route::get('search_invoices','searchInvoices')->name('search_invoices');
    Route::get('search_off_fair_invoice','searchOffFairInvoice')->name('search_off_fair_invoice');
    Route::get('search_sectors','searchSectors')->name('search_sectors');
});
Route::get('search_approved_events',[SearchController::class,'searchApprovedEvents'])->name('search_approved_events');
Route::get('search-exhibitors',[SearchController::class,'searchExhibitor'])->name('search-exhibitors');
//



