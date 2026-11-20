<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdministrationTables extends Migration
{
    public function up()
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->text('response')->nullable();
            $table->string('status')->nullable();
            $table->string('reminder')->nullable();
            $table->text('requestData')->nullable();
            $table->dateTime('response_date')->useCurrent();
        });

        Schema::create('api_requests', function (Blueprint $table) {
            $table->string('client_id', 64)->primary();
            $table->string('access_token')->nullable();
            $table->string('request_name')->nullable();
            $table->string('client_email')->nullable();
        });

        Schema::create('associate_companies', function (Blueprint $table) {
            $table->string('ass_company_id', 64)->primary();
            $table->string('company_id', 64)->nullable()->index();
            $table->string('company_name')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->string('account_id', 64)->primary();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('swift_code')->nullable();
            $table->text('account_descriptions')->nullable();
            $table->string('account_currency')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bankAccountStatus')->nullable();
        });

        Schema::create('bulk_sms', function (Blueprint $table) {
            $table->string('sms_id', 64)->primary();
            $table->text('sms_body')->nullable();
            $table->integer('sms_status')->default(0);
            $table->date('sms_sent_on')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('customer_name')->nullable();
        });

        Schema::create('bulk_sms_receivers', function (Blueprint $table) {
            $table->string('receiver_id', 64)->primary();
            $table->string('phone_number')->nullable();
            $table->string('customer_group_name')->nullable();
            $table->string('customer_name')->nullable();
        });

        Schema::create('company_admins', function (Blueprint $table) {
            $table->string('referenceId', 64)->primary();
            $table->string('adminId', 64)->nullable();
            $table->foreignUuid('companyId')->nullable()->constrained('companies', 'companyID')->nullOnDelete();
        });

        Schema::create('company_details', function (Blueprint $table) {
            $table->string('companyDetailID', 64)->primary();
            $table->string('companyID', 64)->nullable()->index();
            $table->string('userID', 64)->nullable()->index();
            $table->string('registrationNo')->nullable();
            $table->string('tinNo')->nullable();
            $table->string('companyTel')->nullable();
            $table->string('companyAddress')->nullable();
            $table->string('website')->nullable();
            $table->string('originality')->nullable();
            $table->string('personalName')->nullable();
            $table->string('personalPhone')->nullable();
            $table->string('personalEmail')->nullable();
            $table->string('personalTitle')->nullable();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->string('country_id', 64)->primary();
            $table->string('country_name')->nullable();
            $table->string('country_short_code')->nullable();
            $table->string('phone_code')->nullable();
        });

        Schema::create('created_emails', function (Blueprint $table) {
            $table->string('emailId', 64)->primary();
            $table->string('createdBy')->nullable();
            $table->string('emailTo')->nullable();
            $table->string('copyTo')->nullable();
            $table->string('linkLable')->nullable();
            $table->string('receiverName')->nullable();
            $table->string('regardsName')->nullable();
            $table->string('emailSubject')->nullable();
            $table->text('emailLink')->nullable();
            $table->text('emailContent')->nullable();
            $table->string('emailStatus', 32)->default('PENDING');
            $table->dateTime('scheduledAt')->nullable();
        });

        Schema::create('customer_groups', function (Blueprint $table) {
            $table->string('group_id', 64)->primary();
            $table->string('group_name')->nullable();
            $table->text('group_descriptions')->nullable();
        });

        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->string('currencyId', 64)->primary();
            $table->string('currencyCode')->nullable();
            $table->decimal('BUYING', 16, 4)->nullable();
            $table->decimal('SELLING', 16, 4)->nullable();
            $table->decimal('MEAN', 16, 4)->nullable();
        });

        Schema::create('ground_visitors', function (Blueprint $table) {
            $table->string('countId', 64)->primary();
            $table->integer('countType')->nullable();
            $table->integer('totalCount')->nullable();
            $table->string('countDate')->nullable();
        });

        Schema::create('guidelines', function (Blueprint $table) {
            $table->string('guideline_id', 64)->primary();
            $table->string('guideline_title')->nullable();
            $table->text('guideline_descriptions')->nullable();
        });

        Schema::create('intergrations', function (Blueprint $table) {
            $table->string('intergration_id', 64)->primary();
            $table->string('billing_service_code')->nullable();
            $table->string('billing_access_token')->nullable();
            $table->string('intergration_created_by')->nullable();
            $table->string('billing_base_url')->nullable();
        });

        Schema::create('level_one_menuses', function (Blueprint $table) {
            $table->string('level_one_menu_id', 64)->primary();
            $table->string('level_one_common_name')->nullable();
            $table->integer('level_one_menu_status')->nullable();
            $table->string('level_one_icon_name')->nullable();
            $table->string('level_one_menu_link')->nullable();
            $table->string('level_one_gate_name')->nullable();
            $table->integer('moduleNo')->nullable();
        });

        Schema::create('level_two_menuses', function (Blueprint $table) {
            $table->string('level_two_menu_id', 64)->primary();
            $table->string('level_two_common_name')->nullable();
            $table->integer('level_two_menu_status')->nullable();
            $table->string('level_two_icon_name')->nullable();
            $table->string('level_two_menu_link')->nullable();
            $table->string('level_two_gate_name')->nullable();
            $table->string('upper_level_id', 64)->nullable()->index();
            $table->dateTime('level_two_menu_created_at')->useCurrent();
            $table->dateTime('level_two_menu_updated_at')->nullable();
        });

        Schema::create('logins', function (Blueprint $table) {
            $table->id();
            $table->dateTime('login_at')->nullable();
            $table->dateTime('logout_at')->nullable();
            $table->string('user_email')->nullable();
            $table->string('status')->nullable();
            $table->integer('login_attempts_count')->default(0);
            $table->boolean('blocked')->default(0);
            $table->string('blocked_at', 12)->nullable();
            $table->string('requesting_ip')->nullable();
            $table->text('password_submited')->nullable();
        });

        Schema::create('menuses', function (Blueprint $table) {
            $table->string('menu_id', 64)->primary();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('resource_name')->nullable();
            $table->string('gate_name')->nullable();
            $table->string('common_name')->nullable();
            $table->string('status')->nullable();
            $table->string('icon_name')->nullable();
            $table->string('sidebar_season')->nullable();
        });

        Schema::create('policies', function (Blueprint $table) {
            $table->string('policy_id', 64)->primary();
            $table->text('policy_content')->nullable();
            $table->string('created_by')->nullable();
            $table->string('policy_title')->nullable();
        });

        Schema::create('sectors', function (Blueprint $table) {
            $table->string('sectorID', 64)->primary();
            $table->string('userID', 64)->nullable()->index();
            $table->string('sectorName')->nullable();
            $table->text('sectorDescription')->nullable();
            $table->string('sectorImage')->nullable();
            $table->integer('sectorStatus')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::create('site_vistors', function (Blueprint $table) {
            $table->string('vistor_id', 64)->primary();
            $table->string('vistor_ip_address')->nullable();
            $table->string('page_visted')->nullable();
            $table->string('country')->nullable();
            $table->string('hostname')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('organization')->nullable();
            $table->string('timezone')->nullable();
            $table->text('coordinates')->nullable();
            $table->dateTime('visted_on')->useCurrent();
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->string('setting_id', 64)->primary();
            $table->string('edited_by')->nullable();
            $table->string('control_number_deadline')->nullable();
            $table->string('invoice_other_information')->nullable();
            $table->string('level_two_menu_link')->nullable();
            $table->string('level_two_gate_name')->nullable();
            $table->string('upper_level_id', 64)->nullable()->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('site_vistors');
        Schema::dropIfExists('sectors');
        Schema::dropIfExists('policies');
        Schema::dropIfExists('menuses');
        Schema::dropIfExists('logins');
        Schema::dropIfExists('level_two_menuses');
        Schema::dropIfExists('level_one_menuses');
        Schema::dropIfExists('intergrations');
        Schema::dropIfExists('guidelines');
        Schema::dropIfExists('ground_visitors');
        Schema::dropIfExists('exchange_rates');
        Schema::dropIfExists('customer_groups');
        Schema::dropIfExists('created_emails');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('company_details');
        Schema::dropIfExists('company_admins');
        Schema::dropIfExists('bulk_sms_receivers');
        Schema::dropIfExists('bulk_sms');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('associate_companies');
        Schema::dropIfExists('api_requests');
        Schema::dropIfExists('api_logs');
    }
}
