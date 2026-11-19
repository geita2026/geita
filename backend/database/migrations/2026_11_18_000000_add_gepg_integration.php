<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * FR-059 / BR-008: Government Electronic Payment Gateway (GePG).
 *
 * A GePG control number fixes the amount it collects, while an invoice's total
 * changes as spaces are allocated, moved or cancelled. So bills are their own
 * records: an invoice has one active bill at a time and keeps the history of
 * the ones it replaced. Every message exchanged with GePG is logged verbatim.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('space_categories', function (Blueprint $t) {
            $t->string('gfs_code', 20)->nullable(); // revenue code the category's charges are booked under
        });

        Schema::table('invoice_items', function (Blueprint $t) {
            $t->string('gfs_code', 20)->nullable();
        });

        // Control numbers stored before this were placeholders, never issued by GePG.
        DB::table('invoices')->update(['control_number' => null]);

        Schema::table('invoices', function (Blueprint $t) {
            // Mirrors the active bill: requesting|issued|failed|cancelled. NULL when GePG is off.
            $t->string('gepg_status', 20)->nullable()->index();
        });

        Schema::create('gepg_bills', function (Blueprint $t) {
            $t->id();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->string('bill_id', 40)->unique();          // our BillId, quoted back by GePG
            $t->decimal('amount', 16, 2);
            $t->decimal('paid_amount', 16, 2)->default(0);
            $t->string('currency', 3);
            $t->timestamp('expires_at');
            // requesting: sent, waiting for the control number · issued · failed · cancelled
            // superseded: replaced by a new bill after the invoice changed · paid · expired
            $t->string('status', 20)->default('requesting')->index();
            $t->string('control_number', 20)->nullable()->index();
            $t->string('status_code', 10)->nullable();     // last GePG TrxStsCode
            $t->string('error')->nullable();
            $t->unsignedTinyInteger('attempts')->default(0);
            $t->timestamp('requested_at')->nullable();
            $t->timestamp('issued_at')->nullable();
            $t->timestamp('ended_at')->nullable();
            $t->timestamps();
        });

        Schema::table('payments', function (Blueprint $t) {
            $t->string('gepg_trx_id', 60)->nullable()->unique(); // GePG transaction id: a payment is recorded once
            $t->foreignId('gepg_bill_id')->nullable()->constrained('gepg_bills')->nullOnDelete();
            $t->string('psp_name', 100)->nullable();             // bank or mobile operator that took the money
            $t->string('psp_receipt', 60)->nullable();
            $t->string('payment_channel', 40)->nullable();
            $t->string('collection_account', 40)->nullable();
            $t->string('payer_name')->nullable();
            $t->string('payer_phone', 30)->nullable();
        });

        Schema::create('gepg_messages', function (Blueprint $t) {
            $t->id();
            $t->string('direction', 3);                          // out | in
            $t->string('type', 40)->index();                     // root element, e.g. gepgBillSubReq
            $t->foreignId('gepg_bill_id')->nullable()->constrained('gepg_bills')->nullOnDelete();
            $t->string('reference', 60)->nullable()->index();    // BillId, control number or reconciliation id
            $t->longText('payload');
            $t->boolean('signature_valid')->nullable();
            $t->string('status_code', 10)->nullable();
            $t->unsignedSmallInteger('http_status')->nullable();
            $t->string('error')->nullable();
            $t->timestamp('created_at')->useCurrent()->index();
        });

        Schema::create('gepg_reconciliations', function (Blueprint $t) {
            $t->id();
            $t->string('request_id', 40)->unique();
            $t->date('transaction_date');
            $t->string('status', 20)->default('requested');      // requested | received | failed
            $t->unsignedInteger('gepg_count')->default(0);
            $t->decimal('gepg_total', 16, 2)->default(0);
            $t->unsignedInteger('matched')->default(0);
            $t->json('recovered')->nullable();        // paid at GePG, never notified to us: recorded now
            $t->json('missing_at_gepg')->nullable();  // recorded here as GePG payments, unknown to GePG
            $t->json('amount_mismatches')->nullable();
            $t->string('error')->nullable();
            $t->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('received_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gepg_reconciliations');
        Schema::dropIfExists('gepg_messages');
        Schema::table('payments', function (Blueprint $t) {
            $t->dropConstrainedForeignId('gepg_bill_id');
            $t->dropUnique(['gepg_trx_id']);
            $t->dropColumn(['gepg_trx_id', 'psp_name', 'psp_receipt', 'payment_channel', 'collection_account', 'payer_name', 'payer_phone']);
        });
        Schema::dropIfExists('gepg_bills');
        Schema::table('invoices', fn (Blueprint $t) => $t->dropColumn('gepg_status'));
        Schema::table('invoice_items', fn (Blueprint $t) => $t->dropColumn('gfs_code'));
        Schema::table('space_categories', fn (Blueprint $t) => $t->dropColumn('gfs_code'));
    }
};
