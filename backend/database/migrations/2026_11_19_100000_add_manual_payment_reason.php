<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Why a payment was recorded by hand instead of arriving through GePG. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', fn (Blueprint $t) => $t->text('manual_reason')->nullable());
    }

    public function down(): void
    {
        Schema::table('payments', fn (Blueprint $t) => $t->dropColumn('manual_reason'));
    }
};
