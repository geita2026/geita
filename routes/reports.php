<?php

use App\Http\Controllers\Downloads\DownloadController;
use App\Http\Controllers\ExpoControllers\ExpoBackEndController;
use App\Http\Controllers\reports\adviceNoteReports;
use App\Http\Controllers\reports\bookedUnbookedReportController;
use App\Http\Controllers\reports\exhibitionCollectionController;
use App\Http\Controllers\reports\exhibitorsReports;
use App\Http\Controllers\reports\invoiceReportController;
use App\Http\Controllers\reports\pavilionReportController;
use App\Http\Controllers\reports\ProductReportController;
use App\Http\Controllers\reports\reportController;
use App\Http\Controllers\reports\SponsorShipReportController;
use App\Http\Controllers\specialWorkController;
use App\Http\Controllers\Vistors\GroundVisitorsCountController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function() {
// Route::get('export_companies', [DownloadController::class, 'ExportCompanies']);
Route::get('export_companies',[DownloadController::class,'ExportCompanies'])->name('export_companies');
Route::get('download_invoice', [DownloadController::class, 'DownloadInvoice'])->name('download_invoice'); // download invoice
Route::get('collection_report', [DownloadController::class, 'ExportCollectionReport'])->name('collection_report'); // download invoice
Route::get('space_report', [DownloadController::class, 'ExportSpaceReport'])->name('space_report'); // download invoice
Route::resource('reports',reportController::class);
Route::resource('pavilionReports',pavilionReportController::class);
Route::resource('invoice-reports',invoiceReportController::class);
Route::resource('space-booking-report',bookedUnbookedReportController::class);
Route::resource('exhibitors',exhibitorsReports::class);
Route::resource('adviceNote-reports',adviceNoteReports::class);
Route::resource('collection_reports',exhibitionCollectionController::class);
Route::resource('exhibition_products',ProductReportController::class);
#---TEST FUNCTION ------#
Route::get('test_function',[bookedUnbookedReportController::class,'testFunction'])->name('test_funtion');
Route::resource('ground_visitors',GroundVisitorsCountController::class);

Route::get('collectionReport',[invoiceReportController::class,'collectionReport'])->name('collectionReport');
Route::post('exportCollectionReport',[invoiceReportController::class,'ExportCollectionReport'])->name('exportCollectionReport');

Route::get('general-report',[invoiceReportController::class,'generalReport'])->name('general-report');
Route::get('export-general-report',[invoiceReportController::class,'ExportGeneralReport'])->name('export-general-report');

Route::get('sponsorship_report',[SponsorShipReportController::class,'loadSponsorshipData'])->name('sponsorship_report');
Route::post('exportSponsoshipReport',[SponsorShipReportController::class,'ExportSponsorshipReport'])->name('exportSponsoshipReport');


Route::get('tnblc_report',[specialWorkController::class,'getAllApplication'])->name('tnblc_report');

});

//
