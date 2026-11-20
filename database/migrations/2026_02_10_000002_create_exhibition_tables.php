<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExhibitionTables extends Migration
{
    public function up()
    {
        Schema::create('additional_services', function (Blueprint $table) {
            $table->string('serviceID', 64)->primary();
            $table->string('userID', 64)->nullable()->index();
            $table->string('serviceName')->nullable();
            $table->decimal('serviceCost', 16, 4)->nullable();
            $table->text('serviceDescription')->nullable();
            $table->string('type_of_currency')->nullable();
            $table->string('service_type')->nullable();
            $table->string('relateToSpaceSize')->nullable();
            $table->string('service_priolity', 32)->nullable();
            $table->string('service_event')->nullable();
            $table->string('gfs_code')->nullable();
            $table->boolean('is_printable')->nullable();
            $table->boolean('self_booking')->nullable();
        });

        Schema::create('area_distributions', function (Blueprint $table) {
            $table->string('distributionId', 64)->primary();
            $table->string('spaceId', 64)->nullable();
            $table->string('participants', 64)->index();
        });

        Schema::create('booth_categories', function (Blueprint $table) {
            $table->string('booth_category_id', 64)->primary();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('category_name')->nullable();
            $table->decimal('category_cost', 16, 4)->nullable();
            $table->string('type_of_currency')->nullable();
            $table->string('categoryCode')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });

        Schema::create('booths', function (Blueprint $table) {
            $table->string('boothID', 64)->primary();
            $table->string('pavilionID', 64)->nullable()->index();
            $table->string('boothNo')->nullable();
            $table->decimal('boothSize', 16, 4)->nullable();
            $table->decimal('boothCost', 16, 4)->nullable();
            $table->text('boothDescription')->nullable();
            $table->string('bookingStatus')->nullable();
            $table->string('booth_category')->nullable();
            $table->text('allowedForBooking')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('location')->nullable();
        });

        Schema::create('catalogue_settings', function (Blueprint $table) {
            $table->string('leader_id', 64)->primary();
            $table->string('leader_name')->nullable();
            $table->string('leader_title')->nullable();
            $table->string('leader_image')->nullable();
            $table->text('appriciation_word')->nullable();
            $table->integer('order_number')->nullable();
            $table->string('event', 64)->nullable()->index();
        });

        Schema::create('clips_advertisements', function (Blueprint $table) {
            $table->string('advertisement_id', 64)->primary();
            $table->string('advertisement_title')->nullable();
            $table->text('advertisement_descriptions')->nullable();
            $table->string('clip')->nullable();
            $table->integer('advertising_days')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('advertisement_created_at')->useCurrent();
        });

        Schema::create('company_registration_templates', function (Blueprint $table) {
            $table->string('company_name', 255)->primary();
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_category')->nullable();
            $table->string('company_location')->nullable();
        });

        Schema::create('customer_packages', function (Blueprint $table) {
            $table->string('customer_package_id', 64)->primary();
            $table->string('customer_id', 64)->nullable()->index();
            $table->string('package_name')->nullable();
            $table->text('package_description')->nullable();
            $table->decimal('package_amount', 16, 4)->nullable();
            $table->string('exhibition_year', 64)->nullable()->index();
            $table->string('created_by')->nullable();
            $table->string('type_of_currency')->nullable();
        });

        Schema::create('event_programs', function (Blueprint $table) {
            $table->string('program_id', 64)->primary();
            $table->string('program_title')->nullable();
            $table->text('program_description')->nullable();
            $table->string('program_file')->nullable();
            $table->string('event', 64)->nullable()->index();
            $table->boolean('share_program')->nullable();
            $table->date('program_date')->nullable();
            $table->text('characters')->nullable();
        });

        Schema::create('event_sponsors', function (Blueprint $table) {
            $table->string('sponsor_id', 64)->primary();
            $table->string('sponsor_name')->nullable();
            $table->string('sponsor_logo')->nullable();
            $table->string('sponsor_website')->nullable();
            $table->string('sponsorship_category')->nullable();
        });

        Schema::create('exhibition_products', function (Blueprint $table) {
            $table->string('exhibition_product_id', 64)->primary();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('exhibitor_id', 64)->nullable()->index();
            $table->text('product_list')->nullable();
            $table->string('event', 64)->nullable()->index();
        });

        Schema::create('exhibitors', function (Blueprint $table) {
            $table->string('participation_id', 64)->primary();
            $table->string('exhibitor_id', 64)->nullable()->index();
            $table->string('exhibitor_event', 64)->nullable()->index();
            $table->text('pavilion_descriptions')->nullable();
            $table->text('welcome_word')->nullable();
            $table->string('pavilion_image_name')->nullable();
            $table->integer('visibility_status')->nullable();
            $table->string('judging_location')->nullable();
            $table->integer('judging_status')->nullable();
            $table->string('judgingCategory')->nullable();
            $table->unsignedSmallInteger('pStatus')->default(0);
            $table->string('participationCommitimentFile')->nullable();
            $table->boolean('fileIsEditable')->nullable();
            $table->text('exhibitedProducts')->nullable();
            $table->string('makeSales')->nullable();
            $table->string('exhibitionSpace')->nullable();
        });

        Schema::create('judging_categories', function (Blueprint $table) {
            $table->string('categoryId', 64)->primary();
            $table->string('categoryName')->nullable();
            $table->text('description')->nullable();
            $table->integer('categoryStatus')->nullable();
        });

        Schema::create('package_groups', function (Blueprint $table) {
            $table->string('packageGroupId', 64)->primary();
            $table->string('packageGroupName')->nullable();
            $table->text('packageGroupDescription')->nullable();
            $table->string('packageGroupStatus')->nullable();
            $table->string('backgroundColor')->nullable();
            $table->string('textColor')->nullable();
        });

        Schema::create('pavilions', function (Blueprint $table) {
            $table->string('pavilionID', 64)->primary();
            $table->string('pavilionName')->nullable();
            $table->string('userID', 64)->nullable()->index();
            $table->integer('TotalBooths')->nullable();
            $table->string('floor_plan')->nullable();
            $table->string('spaceStreet')->nullable();
            $table->string('pavilionCategory')->nullable();
            $table->decimal('pavilionRate', 16, 4)->nullable();
            $table->text('pavilionDescription')->nullable();
            $table->string('bookingStatus')->nullable();
            $table->decimal('pavilion_size', 16, 4)->nullable();
            $table->string('space_status')->nullable();
            $table->text('allowedForBooking')->nullable();
            $table->text('whoCanBook')->nullable();
            $table->integer('numberOfPartitions')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('location')->nullable();
            $table->string('event', 64)->nullable()->index();
        });

        Schema::create('prefair_seminar_feed_backs', function (Blueprint $table) {
            $table->id();
            $table->text('feedBackId')->nullable();
            $table->string('programName')->nullable();
            $table->text('attendeeFeedback')->nullable();
            $table->string('saticifactionLevel')->nullable();
        });

        Schema::create('prefair_seminars', function (Blueprint $table) {
            $table->id();
            $table->string('serminarId', 64)->nullable();
            $table->string('fullName')->nullable();
            $table->string('email')->nullable();
            $table->string('phoneNumber')->nullable();
            $table->text('description')->nullable();
            $table->string('participationId', 64)->nullable();
        });

        Schema::create('price_configurations', function (Blueprint $table) {
            $table->string('trackingId', 64)->primary();
            $table->string('referenceId', 64)->nullable();
            $table->string('configurationName')->nullable();
            $table->text('configDescription')->nullable();
            $table->string('configCost')->nullable();
            $table->string('eventId', 64)->nullable()->index();
            $table->string('currency')->nullable();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->string('product_id', 64)->primary();
            $table->string('product_name')->nullable();
            $table->text('descriptions')->nullable();
        });

        Schema::create('space_categories', function (Blueprint $table) {
            $table->string('category_id', 64)->primary();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('category_name')->nullable();
            $table->decimal('cost_one_msq', 16, 4)->nullable();
            $table->string('type_of_currency')->nullable();
            $table->string('background_color')->nullable();
            $table->string('category_icon')->nullable();
            $table->string('category_event')->nullable();
            $table->text('category_descriptions')->nullable();
        });

        Schema::create('sponsor_spaces', function (Blueprint $table) {
            $table->string('booking_id', 64)->primary();
            $table->string('space_id', 64)->nullable();
            $table->string('packageId', 64)->nullable();
        });

        Schema::create('sponsorship_benefits', function (Blueprint $table) {
            $table->string('benefitId', 64)->primary();
            $table->string('benefitTitle')->nullable();
            $table->text('benefitDescription')->nullable();
            $table->decimal('benefitCost', 16, 4)->nullable();
            $table->string('benefitStatus')->nullable();
        });

        Schema::create('sponsorship_package_contents', function (Blueprint $table) {
            $table->string('package_content_id', 64)->primary();
            $table->string('package_id', 64)->nullable();
            $table->text('content_description')->nullable();
            $table->decimal('contentCost', 16, 4)->nullable();
            $table->string('created_by')->nullable();
            $table->string('benefitId', 64)->nullable();
            $table->string('eventId', 64)->nullable()->index();
        });

        Schema::create('sponsorship_packages', function (Blueprint $table) {
            $table->string('package_id', 64)->primary();
            $table->string('package_name')->nullable();
            $table->decimal('package_amount', 16, 4)->nullable();
            $table->text('package_description')->nullable();
            $table->string('type_of_currency')->nullable();
            $table->string('gfs_code')->nullable();
            $table->string('packageStatus')->nullable();
            $table->string('packageGroup')->nullable();
        });

        Schema::create('streets', function (Blueprint $table) {
            $table->string('streetId', 64)->primary();
            $table->string('streetName')->nullable();
            $table->string('napaReference')->nullable();
            $table->text('streetDescription')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longtude', 10, 7)->nullable();
        });

        Schema::create('text_advertisements', function (Blueprint $table) {
            $table->string('advertisement_id', 64)->primary();
            $table->string('advertisement_title')->nullable();
            $table->string('advertisement_link')->nullable();
            $table->integer('advertising_days')->nullable();
            $table->string('status')->nullable();
            $table->string('slidingImage')->nullable();
            $table->text('imageDescription')->nullable();
        });

        Schema::create('user_spaces', function (Blueprint $table) {
            $table->string('userSpaceID', 64)->primary();
            $table->string('assignedTo')->nullable();
            $table->string('assignedBy')->nullable();
            $table->string('pavilionID', 64)->nullable()->index();
            $table->string('spaceID', 64)->nullable();
        });

        Schema::create('virtual_files', function (Blueprint $table) {
            $table->string('image_id', 64)->primary();
            $table->string('reference_id', 64)->nullable();
            $table->string('destination')->nullable();
            $table->string('image_name')->nullable();
            $table->text('image_description')->nullable();
            $table->dateTime('image_added_at')->useCurrent();
        });

        Schema::create('virtual_messages', function (Blueprint $table) {
            $table->string('message_id', 64)->primary();
            $table->string('message')->nullable();
            $table->string('message_from')->nullable();
            $table->string('message_to')->nullable();
            $table->string('message_status')->nullable();
            $table->dateTime('message_sent_on')->nullable();
            $table->dateTime('message_read_on')->nullable();
            $table->string('message_file')->nullable();
            $table->string('chart_session')->nullable();
            $table->string('reference_email')->nullable();
        });

        Schema::create('virtual_pavilions', function (Blueprint $table) {
            $table->string('virtual_pavilion_id', 64)->primary();
            $table->text('pavilion_descriptions')->nullable();
            $table->string('pavilion_image_name')->nullable();
            $table->string('exhibitor_id', 64)->nullable()->index();
        });

        Schema::create('virtual_services', function (Blueprint $table) {
            $table->string('virtual_service_id', 64)->primary();
            $table->string('participation_id', 64)->nullable();
            $table->text('service_descriptions')->nullable();
            $table->string('service_image_name')->nullable();
            $table->string('service_name')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('virtual_services');
        Schema::dropIfExists('virtual_pavilions');
        Schema::dropIfExists('virtual_messages');
        Schema::dropIfExists('virtual_files');
        Schema::dropIfExists('user_spaces');
        Schema::dropIfExists('text_advertisements');
        Schema::dropIfExists('streets');
        Schema::dropIfExists('sponsorship_packages');
        Schema::dropIfExists('sponsorship_package_contents');
        Schema::dropIfExists('sponsorship_benefits');
        Schema::dropIfExists('sponsor_spaces');
        Schema::dropIfExists('space_categories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('price_configurations');
        Schema::dropIfExists('prefair_seminars');
        Schema::dropIfExists('prefair_seminar_feed_backs');
        Schema::dropIfExists('pavilions');
        Schema::dropIfExists('package_groups');
        Schema::dropIfExists('judging_categories');
        Schema::dropIfExists('exhibitors');
        Schema::dropIfExists('exhibition_products');
        Schema::dropIfExists('event_sponsors');
        Schema::dropIfExists('event_programs');
        Schema::dropIfExists('customer_packages');
        Schema::dropIfExists('company_registration_templates');
        Schema::dropIfExists('clips_advertisements');
        Schema::dropIfExists('catalogue_settings');
        Schema::dropIfExists('booths');
        Schema::dropIfExists('booth_categories');
        Schema::dropIfExists('area_distributions');
        Schema::dropIfExists('additional_services');
    }
}
