<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPavilionCodeToPavilions extends Migration
{
    public function up(): void
    {
        Schema::table('pavilions', function (Blueprint $table) {
            $table->string('pavilionCode', 64)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('pavilions', function (Blueprint $table) {
            $table->dropIndex(['pavilionCode']);
            $table->dropColumn('pavilionCode');
        });
    }
}
