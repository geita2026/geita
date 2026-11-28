<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookingCompanyToBooths extends Migration
{
    public function up(): void
    {
        Schema::table('booths', function (Blueprint $table) {
            $table->string('whoCanBook', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('booths', function (Blueprint $table) {
            $table->dropColumn('whoCanBook');
        });
    }
}
