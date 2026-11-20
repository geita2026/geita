<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('default_settings', function (Blueprint $table) {
            $table->uuid('default_setting_id')->primary();
            $table->uuid('setting_created_by')->nullable();
            $table->string('system_title', 150);
            $table->string('system_version', 20);
            $table->string('notification_channel', 10);
            $table->date('invoice_deadline_date');
            $table->text('invoice_other_information')->nullable();
            $table->uuid('active_event')->nullable();
            $table->uuid('badging_event')->nullable();
            $table->boolean('self_printing')->default(false);
            $table->boolean('print_notification')->default(false);
            $table->boolean('badge_live_test')->default(false);
            $table->string('applicationStatus', 20)->nullable();
            $table->decimal('exchangeRate', 12, 4)->nullable();
            $table->text('allowedNetworks')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('default_settings');
    }
}
