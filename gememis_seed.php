<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$existing = DB::table('intergrations')->first();
if (!$existing) {
    DB::table('intergrations')->insert([
        'intergration_id'         => 'gememis-' . date('YmdHis'),
        'billing_service_code'    => 'GEMEMIS',
        'billing_access_token'    => 'UPDATE_ME_WITH_REAL_GEPG_TOKEN',
        'billing_base_url'        => 'https://billing.tantrade.go.tz/index.php/v1/',
        'intergration_created_by' => 'system',
    ]);
    echo "[OK] Integration record created (placeholder credentials)\n";
    echo "[WARN] Update billing_access_token with real GePG token before production\n";
} else {
    echo "[OK] Integration record already exists\n";
    echo "     Service Code: {$existing->billing_service_code}\n";
    echo "     Base URL:     {$existing->billing_base_url}\n";
}
