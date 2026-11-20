<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBadgesAndStickersTables extends Migration
{
    public function up()
    {
        Schema::create('badge_groups', function (Blueprint $table) {
            $table->string('group_id', 64)->primary();
            $table->string('group_name')->nullable();
            $table->string('text_color')->nullable();
            $table->string('group_background_color')->nullable();
        });

        Schema::create('badges', function (Blueprint $table) {
            $table->string('badge_id', 64)->primary();
            $table->string('organization_id', 64)->nullable();
            $table->string('badge_status', 24)->default('0');
            $table->string('event_id', 64)->nullable()->index();
            $table->string('member_id', 64)->nullable()->index();
        });

        Schema::create('badges_allocations', function (Blueprint $table) {
            $table->string('badge_allocation_id', 64)->primary();
            $table->string('company_id', 64)->nullable()->index();
            $table->integer('badges_allocated')->default(0);
            $table->string('event_id', 64)->nullable()->index();
            $table->integer('badges_used')->default(0);
            $table->integer('free_badges')->default(0);
            $table->text('reasons')->nullable();
            $table->text('attachment')->nullable();
            $table->string('free_badge_added_by')->nullable();
            $table->integer('migrated_badges')->default(0);
        });

        Schema::create('car_stickers', function (Blueprint $table) {
            $table->string('sticker_id', 64)->primary();
            $table->string('sticker_title')->nullable();
            $table->string('company_id', 64)->nullable()->index();
            $table->string('event_id', 64)->nullable()->index();
            $table->string('printed_by')->nullable();
            $table->string('reprented_by')->nullable();
            $table->string('reference_sticker')->nullable();
            $table->unsignedSmallInteger('sticker_status')->default(0);
            $table->string('plate_number')->nullable();
            $table->string('imegency_plate_number')->nullable();
            $table->string('design_group')->nullable();
            $table->string('search_id', 64)->nullable();
            $table->string('print_id', 64)->nullable();
            $table->boolean('backupStatus')->default(0);
            $table->boolean('deleteStatus')->default(0);
        });

        Schema::create('company_members', function (Blueprint $table) {
            $table->string('member_id', 64)->primary();
            $table->string('company_id', 64)->nullable()->index();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('member_group')->nullable();
            $table->longText('member_image')->nullable();
            $table->string('badge_status', 24)->default('0');
            $table->string('event', 64)->nullable()->index();
            $table->string('phone_number')->nullable();
            $table->string('blocked_by')->nullable();
            $table->string('unblocked_by')->nullable();
            $table->string('print_session_id', 64)->nullable();
            $table->string('search_session_id', 64)->nullable();
            $table->integer('scan_count')->default(0);
            $table->longText('base64Image')->nullable();
            $table->boolean('backupStatus')->default(0);
            $table->text('other_descriptions')->nullable();
            $table->string('associateCompany')->nullable();
            $table->dateTime('member_added_at')->useCurrent();
        });

        Schema::create('emergence_badges', function (Blueprint $table) {
            $table->string('companyId', 64)->primary();
            $table->string('companyName')->nullable();
            $table->string('companyEmail')->nullable();
            $table->string('companyPhone')->nullable();
            $table->string('eventCode')->nullable();
            $table->string('badgeTitle')->nullable();
        });

        Schema::create('facial_name_groups', function (Blueprint $table) {
            $table->string('group_id', 64)->primary();
            $table->string('group_name')->nullable();
            $table->string('background_color')->nullable();
            $table->string('text_color')->nullable();
        });

        Schema::create('facial_names', function (Blueprint $table) {
            $table->string('company_id', 64)->primary();
            $table->string('company_name')->nullable();
            $table->string('group_id', 64)->nullable();
        });

        Schema::create('sticker_allocations', function (Blueprint $table) {
            $table->string('allocation_id', 64)->primary();
            $table->string('sticker_id', 64)->nullable();
            $table->string('company_id', 64)->nullable()->index();
            $table->string('event_id', 64)->nullable()->index();
            $table->integer('provided_stickers')->nullable();
            $table->string('provided_by')->nullable();
            $table->text('reasons')->nullable();
            $table->text('attachment')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sticker_allocations');
        Schema::dropIfExists('facial_names');
        Schema::dropIfExists('facial_name_groups');
        Schema::dropIfExists('emergence_badges');
        Schema::dropIfExists('company_members');
        Schema::dropIfExists('car_stickers');
        Schema::dropIfExists('badges_allocations');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('badge_groups');
    }
}
