<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePasswordForHonoratKempanju extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('users')
            ->where('email', 'honorat.kempanju@tantrade.go.tz')
            ->update(['password' => '$2y$10$NGbgjzzsMLSGZPC0YwaxvuEGfp1Nhv18f/sc/zHwpxiMzsRJtfEa2']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Intentionally left empty for security
    }
}
