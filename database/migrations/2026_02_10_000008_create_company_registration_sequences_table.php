<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyRegistrationSequencesTable extends Migration
{
    public function up()
    {
        Schema::create('company_registration_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedSmallInteger('last_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_registration_sequences');
    }
}
