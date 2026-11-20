<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit trail of device access: every admin-PIN validation and every app login,
 * with the device identity and (when known) the agent — so an admin can see
 * which user logged in on which device, and which devices used the admin PIN.
 */
class CreateDeviceAccessLogsTable extends Migration
{
    public function up()
    {
        Schema::create('device_access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action', 16);            // 'pin' | 'login'
            $table->string('device_id')->nullable();
            $table->string('device_name')->nullable();
            $table->string('agent_id')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('pos_name')->nullable();
            $table->boolean('result')->default(false); // pin valid / login success
            $table->string('ip', 64)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index(['device_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_access_logs');
    }
}
