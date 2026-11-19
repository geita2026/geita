<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Registration asks only for the person (as SAMS does); who they exhibit as is
 * stated when they first apply: an individual without a registered business,
 * or a registered business. Each is treated as such — individuals are billed
 * in their own name and have their own mandatory documents per exhibition.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exhibitors', function (Blueprint $t) {
            $t->string('entity_type', 20)->nullable()->index(); // individual | business; NULL until the first application
            $t->string('national_id', 40)->nullable();          // individuals, optional
        });
        // Everyone registered before this did so as an organisation.
        DB::table('exhibitors')->update(['entity_type' => 'business']);

        Schema::table('users', function (Blueprint $t) {
            $t->timestamp('terms_accepted_at')->nullable();
        });

        Schema::table('exhibitions', function (Blueprint $t) {
            $t->json('required_documents_individual')->nullable(); // BR-003 for individuals
        });
    }

    public function down(): void
    {
        Schema::table('exhibitions', fn (Blueprint $t) => $t->dropColumn('required_documents_individual'));
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('terms_accepted_at'));
        Schema::table('exhibitors', function (Blueprint $t) {
            $t->dropIndex(['entity_type']);
            $t->dropColumn(['entity_type', 'national_id']);
        });
    }
};
