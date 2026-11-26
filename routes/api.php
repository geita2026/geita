<?php

use App\Http\Controllers\APIs\ApiAuthController;
use App\Http\Controllers\APIs\BackUpIntergrationController;
use App\Http\Controllers\APIs\ClientIntergrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIs\DataMigrationController;
use App\Http\Controllers\APIs\IntergrationController;
use App\Http\Controllers\APIs\JudgingController;
use App\Http\Controllers\APIs\ThirdPartMessageController;
use App\Http\Controllers\messaging\messagingController;

// auth APIs
Route::post('/auth/login', [ApiAuthController::class, 'loginUser']);
Route::post('/auth/register', [ApiAuthController::class, 'createUser']);
Route::post('/auth/forgot_password', [ApiAuthController::class, 'ForgotPassword']);
// end Auth APIs

# ************* PRIVATE APIs *********** #
Route::prefix('')->group(function() {
    Route::post('control_number',[IntergrationController::class,'PostControlNumber']);
    Route::post('payments',[IntergrationController::class,'PostPayments']);
    Route::resource('data_migration',DataMigrationController::class);
    Route::get('migrate_invoices', [DataMigrationController::class, 'InvoiceDataMigration']);
    Route::get('api_receiver_update', [DataMigrationController::class, 'ReturnInvoice']);
    Route::post('company_sectors',[JudgingController::class,'CompanySectors']);
    Route::apiResource('/thirdpart_messages',ThirdPartMessageController::class);
});

Route::group(['middleware' => ['auth']], function() {
    Route::get('/exhibitor_lists',[ClientIntergrationController::class,'ExhibitorList']);
});
//

#********APP API*********#
require __DIR__.'/AppApi.php';
require __DIR__.'/VerificationApis.php';
require __DIR__.'/SabasabaApp.php';
#******* END API*********#

#************* BACKUP *********
Route::get(date('d_m_y'),[BackUpIntergrationController::class,'getBadges']);
Route::get(date('d_m_y').'/{member_id}',[BackUpIntergrationController::class,'badgeBackup']);

Route::get(date('d_m_y2'),[BackUpIntergrationController::class,'StickersBackup']);
Route::get(date('d_m_y2').'/{sticker_id}',[BackUpIntergrationController::class,'notifyStickerBackUp']);


Route::post('mail_'.date('y_m_d'),[messagingController::class,'CreateCreatedEmails'])->name('mail_'.date('y_m_d'));
