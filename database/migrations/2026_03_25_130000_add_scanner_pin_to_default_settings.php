<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Replace the time-based admin PIN with a simple 4-digit PIN stored in DB.
 * Admins change it from the Default Settings page whenever needed.
 * Default is '0000' — change it immediately after deploying.
 */
class AddScannerPinToDefaultSettings extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE default_settings ADD COLUMN scanner_pin VARCHAR(4) NOT NULL DEFAULT '0000' AFTER badge_live_test");
    }

    public function down()
    {
        DB::statement("ALTER TABLE default_settings DROP COLUMN scanner_pin");
    }
}
