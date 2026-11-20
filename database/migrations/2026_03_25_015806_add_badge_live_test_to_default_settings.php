<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBadgeLiveTestToDefaultSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('default_settings', function (Blueprint $table) {
            $table->boolean('badge_live_test')->default(false)->after('print_notification');
        });
    }

    public function down()
    {
        Schema::table('default_settings', function (Blueprint $table) {
            $table->dropColumn('badge_live_test');
        });
    }
}
