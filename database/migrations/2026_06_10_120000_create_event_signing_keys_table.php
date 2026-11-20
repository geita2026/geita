<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-event HMAC signing key for offline ticket validation.
 *
 * Additive and isolated: a brand-new table that no existing flow reads.
 * The key is handed to POS and scanner apps at login (`signingKey`) so a
 * POS-issued ticket can be cryptographically verified by any scanner /
 * the LAN hub with no internet. Nothing in the existing verification,
 * MPESA, or web paths depends on this table.
 */
class CreateEventSigningKeysTable extends Migration
{
    public function up()
    {
        Schema::create('event_signing_keys', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('signing_key', 128);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_signing_keys');
    }
}
