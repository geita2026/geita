<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignedContractsTable extends Migration
{
    public function up()
    {
        Schema::create('signed_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('registration_id', 100);
            $table->enum('signer_type', ['exhibitor', 'witness']);
            $table->string('first_name', 100);
            $table->string('middle_name', 100);
            $table->string('last_name', 100);
            $table->string('qualification', 200)->nullable();
            $table->text('address')->nullable();
            $table->string('signature_path', 255);
            $table->unsignedBigInteger('related_signature_id')->nullable();
            $table->timestamps();

            $table->index('registration_id', 'idx_registration');
            $table->index('signer_type', 'idx_signer_type');
            $table->index('related_signature_id', 'idx_related');
        });
    }

    public function down()
    {
        Schema::dropIfExists('signed_contracts');
    }
}
