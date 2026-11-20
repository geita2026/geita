<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * YAS / Tigo Pesa "Debit Mandate" (USSD PUSH) transaction ledger.
 *
 * Additive and isolated: a brand-new table that no existing flow reads.
 * Every step of a YAS payment is recorded here so the whole lifecycle is
 * auditable end-to-end:
 *   initiate -> push sent -> gateway response -> customer approves ->
 *   YAS callback -> tickets issued.
 *
 * Nothing in the existing MPESA/Vodacom, billing, or verification paths
 * depends on this table.
 */
class CreateYasTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('yas_transactions', function (Blueprint $table) {
            $table->id();

            // Unique ref we send to YAS (biller_code + unique suffix). Primary
            // correlation key between our push and YAS's callback.
            $table->string('reference_id', 100)->unique();

            // Link back to the ticket booking being paid for.
            $table->string('booking_id')->nullable()->index();

            // Who is being charged + how much.
            $table->string('customer_msisdn', 15)->nullable();
            $table->string('biller_msisdn', 15)->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency', 8)->default('TZS');
            $table->string('remarks')->nullable();

            // Lifecycle: PENDING -> PUSHED -> SUCCESS | FAILED
            $table->string('status', 20)->default('PENDING')->index();

            // YAS's transaction id, returned in the callback.
            $table->string('mfs_transaction_id')->nullable()->index();

            // Full payloads at each hop, for audit / debugging.
            $table->json('request_payload')->nullable();   // what we sent to BillerPay
            $table->json('response_payload')->nullable();   // BillerPay sync response
            $table->json('callback_payload')->nullable();   // what YAS posted back

            // Gateway response code/description (from push and/or callback).
            $table->string('response_code')->nullable();
            $table->text('error')->nullable();

            // Guard against processing the same callback twice.
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('yas_transactions');
    }
}
