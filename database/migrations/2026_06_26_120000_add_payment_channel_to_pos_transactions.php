<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tag each POS ticket with the payment channel it was sold through.
 *
 * Additive + backward-compatible: existing rows and the cash flow default to
 * 'CASH'; YAS-paid tickets are stamped 'MIXX-YAS' by the POS upload. Nothing
 * existing reads this column, so it can't break current behaviour.
 */
class AddPaymentChannelToPosTransactions extends Migration
{
    public function up()
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->string('paymentChannel', 30)->default('CASH')->after('serviceId');
        });
    }

    public function down()
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->dropColumn('paymentChannel');
        });
    }
}
