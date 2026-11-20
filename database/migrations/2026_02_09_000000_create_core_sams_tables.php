<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoreSamsTables extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('companyID')->primary();
            $table->string('registrationID')->nullable()->index();
            $table->string('companyName');
            $table->string('companyEmail')->nullable();
            $table->string('companyPhone')->nullable();
            $table->string('companyCategory')->nullable();
            $table->string('companyLocation')->nullable();
            $table->string('companySector')->nullable()->index();
            $table->unsignedSmallInteger('status')->default(1);
            $table->string('sp_customer_no')->nullable();
            $table->string('region')->nullable();
            $table->uuid('associate_company')->nullable()->index();
            $table->boolean('isAcceptedTerms')->default(false);
            $table->string('companyLogo')->nullable();
            $table->string('referenceModule')->nullable();
            $table->string('originality')->nullable();
            $table->string('tinNo')->nullable();
            $table->text('companyAddress')->nullable();
            $table->string('website')->nullable();
            $table->string('personalName')->nullable();
            $table->string('personalEmail')->nullable();
            $table->string('personalPhone')->nullable();
            $table->string('personalTitle')->nullable();
            $table->string('tinStatus', 20)->nullable();
            $table->string('brnStatus', 20)->nullable();
            $table->string('registrationNo')->nullable();
            $table->boolean('hasOtherCompanies')->default(false);
            $table->unsignedSmallInteger('lastUpdatedOn')->nullable();
            $table->timestamp('registrationDate')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid')->unique();
            $table->string('first_name')->nullable();
            $table->string('name')->nullable();
            $table->string('user_name')->nullable()->index();
            $table->ipAddress('user_ip_address')->nullable();
            $table->unsignedSmallInteger('user_status')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('role')->nullable()->constrained(config('permission.table_names.roles'))->nullOnDelete();
            $table->string('profile_image')->nullable();
            $table->text('user_signature')->nullable();
            $table->unsignedSmallInteger('account_type')->default(2);
            $table->unsignedSmallInteger('account_status')->default(0);
            $table->boolean('password_updated')->default(false);
            $table->boolean('profile_updated')->default(false);
            $table->uuid('myCompany')->nullable()->index();
            $table->boolean('isAcceptedTerms')->default(false);
            $table->string('api_token', 100)->nullable()->unique();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->uuid('event_id')->primary();
            $table->string('event_name');
            $table->date('event_start_date');
            $table->date('event_end_date');
            $table->text('event_descriptions')->nullable();
            $table->string('event_profile_image')->nullable();
            $table->string('event_location')->nullable();
            $table->boolean('event_status')->default(false)->index();
            $table->dateTime('event_created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('users');
        Schema::dropIfExists('companies');
    }
}
