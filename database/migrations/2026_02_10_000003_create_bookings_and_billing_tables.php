<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsAndBillingTables extends Migration
{
    public function up()
    {
        Schema::create('advice_note_deadlines', function (Blueprint $table) {
            $table->string('deadline_id', 64)->primary();
            $table->string('advice_note_id', 64)->nullable()->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->date('deadline_date')->nullable();
        });

        Schema::create('advice_notes', function (Blueprint $table) {
            $table->string('advice_note_id', 64)->primary();
            $table->string('company_id', 64)->nullable()->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('exhibition_year', 64)->nullable()->index();
            $table->string('advice_note_status')->nullable();
            $table->string('accountant_id', 64)->nullable();
            $table->text('advice_note_description')->nullable();
            $table->date('advice_note_deadline')->nullable();
            $table->string('event', 64)->nullable()->index();
            $table->decimal('vat', 16, 4)->nullable();
            $table->string('invoice_type')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->text('rejection_comment')->nullable();
            $table->string('control_number', 64)->nullable()->index();
            $table->decimal('total_amount', 16, 4)->nullable();
            $table->decimal('amount_paid', 16, 4)->default(0);
            $table->decimal('balance', 16, 4)->nullable();
            $table->string('currency')->nullable();
            $table->string('sp_invoice_no')->nullable();
            $table->decimal('total_vat_amount', 16, 4)->nullable();
            $table->date('new_deadline')->nullable();
            $table->text('endorsement_attachment')->nullable();
            $table->decimal('discount', 16, 4)->nullable();
            $table->text('discount_reasons')->nullable();
            $table->text('discount_attachment')->nullable();
            $table->string('initially_assessed_by')->nullable();
            $table->string('finally_assessed_by')->nullable();
            $table->string('rejection_reasons')->nullable();
            $table->boolean('change_currency')->nullable();
            $table->decimal('exchange_rate', 16, 4)->nullable();
            $table->dateTime('notification_date')->nullable();
            $table->string('payment_mode')->nullable();
            $table->decimal('discountAmount', 16, 4)->nullable();
            $table->dateTime('advice_note_created_at')->useCurrent();
        });

        Schema::create('bills', function (Blueprint $table) {
            $table->string('bill_id', 64)->primary();
            $table->string('customer_id', 64)->nullable()->index();
            $table->string('advice_note_id', 64)->nullable()->index();
            $table->string('control_number', 64)->nullable()->index();
            $table->decimal('total_amount', 16, 4)->nullable();
            $table->string('payment_status')->nullable();
            $table->string('approved_by')->nullable();
            $table->decimal('balance', 16, 4)->nullable();
            $table->string('currency')->nullable();
            $table->string('sp_invoice_no')->nullable();
        });

        Schema::create('booked_booths', function (Blueprint $table) {
            $table->string('bookingID', 64)->primary();
            $table->string('companyID', 64)->nullable()->index();
            $table->string('bookingStatus')->nullable();
            $table->string('userID', 64)->nullable()->index();
            $table->string('boothID', 64)->nullable();
            $table->string('exhibition_year', 64)->nullable()->index();
            $table->string('advice_note_id', 64)->nullable()->index();
            $table->string('booking_status')->nullable();
        });

        Schema::create('booked_pavilions', function (Blueprint $table) {
            $table->string('bookingID', 64)->primary();
            $table->string('companyID', 64)->nullable()->index();
            $table->string('userID', 64)->nullable()->index();
            $table->string('pavilionID', 64)->nullable()->index();
            $table->string('booking_status')->nullable();
            $table->string('exhibition_year', 64)->nullable()->index();
            $table->string('advice_note_id', 64)->nullable()->index();
            $table->string('transferStatus')->nullable();
            $table->string('oldPavilionId', 64)->nullable();
            $table->string('transferredBy')->nullable();
            $table->string('transferApprovedBy')->nullable();
            $table->string('transferReasons')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });

        Schema::create('booked_sponsorship_packages', function (Blueprint $table) {
            $table->string('booking_id', 64)->primary();
            $table->string('customer_id', 64)->nullable()->index();
            $table->string('package_id', 64)->nullable();
            $table->string('approved_by')->nullable();
            $table->string('exhibition_year', 64)->nullable()->index();
            $table->decimal('customer_amount', 16, 4)->nullable();
            $table->string('advice_note_id', 64)->nullable()->index();
        });

        Schema::create('booking_inquiries', function (Blueprint $table) {
            $table->string('enquiryId', 64)->primary();
            $table->string('enquiryNo')->nullable();
            $table->string('companyName')->nullable();
            $table->string('companyEmail')->nullable();
            $table->string('companyPhone')->nullable();
            $table->string('eventId', 64)->nullable()->index();
            $table->text('products')->nullable();
            $table->string('spaceCategory')->nullable();
            $table->string('country')->nullable();
            $table->string('inquiryStatus')->nullable();
            $table->string('experienceStatus')->nullable();
            $table->text('equiryFeedBack')->nullable();
            $table->string('registrationCertificate')->nullable();
            $table->string('companyProfile')->nullable();
            $table->text('moreDescription')->nullable();
        });

        Schema::create('canceled_advice_notes', function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->string('advice_note_id', 64)->nullable()->index();
            $table->string('canceled_by')->nullable();
            $table->text('reasons')->nullable();
        });

        Schema::create('gfs_codes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('incomes', function (Blueprint $table) {
            $table->string('gfs_code', 32)->primary();
            $table->string('income_name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_space_fee')->nullable();
            $table->boolean('is_joint_payment')->nullable();
        });

        Schema::create('particulars', function (Blueprint $table) {
            $table->string('particular_id', 64)->primary();
            $table->string('advice_note_id', 64)->nullable()->index();
            $table->string('particular_name')->nullable();
            $table->decimal('particular_quantity', 16, 4)->nullable();
            $table->decimal('cost_for_one_particular', 16, 4)->nullable();
            $table->string('type_of_currency')->nullable();
            $table->boolean('contain_vat')->nullable();
            $table->text('particular_description')->nullable();
            $table->string('particular_type')->nullable();
            $table->string('particular_category')->nullable();
            $table->string('gfs_code')->nullable();
        });

        Schema::create('payment_details', function (Blueprint $table) {
            $table->string('payment_id', 64)->primary();
            $table->string('control_number', 64)->nullable()->index();
            $table->string('receipt')->nullable();
            $table->string('bank_receipt')->nullable();
            $table->string('gateway_receipt')->nullable();
            $table->decimal('amount_paid', 16, 4)->default(0);
            $table->string('currency')->nullable();
            $table->dateTime('trans_date')->nullable();
            $table->string('bank_account')->nullable();
            $table->boolean('is_transfered')->nullable();
            $table->string('old_invoice')->nullable();
            $table->string('transfered_by')->nullable();
            $table->string('transfer_reason')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('referenceId', 64)->nullable();
            $table->string('advice_note_id', 64)->nullable()->index();
            $table->string('in_fair_invoice_id', 64)->nullable();
            $table->dateTime('payment_made_on')->useCurrent();
        });

        Schema::create('service_charges', function (Blueprint $table) {
            $table->string('service_charge_id', 64)->primary();
            $table->string('company_id', 64)->nullable()->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('exhibition_year', 64)->nullable()->index();
            $table->integer('service_quantity')->nullable();
            $table->string('service_id', 64)->nullable();
            $table->string('advice_note_id', 64)->nullable()->index();
            $table->string('booking_status')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });

    }

    public function down()
    {
        Schema::dropIfExists('service_charges');
        Schema::dropIfExists('payment_details');
        Schema::dropIfExists('particulars');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('gfs_codes');
        Schema::dropIfExists('canceled_advice_notes');
        Schema::dropIfExists('booking_inquiries');
        Schema::dropIfExists('booked_sponsorship_packages');
        Schema::dropIfExists('booked_pavilions');
        Schema::dropIfExists('booked_booths');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('advice_notes');
        Schema::dropIfExists('advice_note_deadlines');
    }
}
