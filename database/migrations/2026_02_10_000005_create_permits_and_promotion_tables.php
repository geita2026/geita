<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermitsAndPromotionTables extends Migration
{
    public function up()
    {
        Schema::create('application_invoices', function (Blueprint $table) {
            $table->string('invoiceNo', 64)->primary();
            $table->decimal('invoiceAmount', 16, 4)->nullable();
            $table->decimal('vatAmount', 16, 4)->nullable();
            $table->string('spInvoiceNo')->nullable();
            $table->string('paymentMode')->nullable();
            $table->integer('invoiceStatus')->nullable();
            $table->text('invoiceDescription')->nullable();
            $table->string('customerId', 64)->nullable();
            $table->string('createdBy')->nullable();
            $table->string('expireDate', 32)->nullable();
            $table->string('newExpireDate', 32)->nullable();
            $table->decimal('amountPaid', 16, 4)->default(0);
            $table->string('controlNumber', 64)->nullable()->index();
            $table->string('currency')->nullable();
            $table->string('referenceApplication', 64)->nullable()->index();
            $table->dateTime('invoiceDate')->useCurrent();
        });

        Schema::create('application_reviewers', function (Blueprint $table) {
            $table->string('mappingId', 64)->primary();
            $table->string('applicationReference')->nullable();
            $table->string('asignedUser')->nullable();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->string('commentId', 64)->primary();
            $table->text('commentedBy')->nullable();
            $table->text('comment')->nullable();
            $table->string('referenceId', 64)->nullable();
            $table->text('commentStatus')->nullable();
            $table->dateTime('commentDate')->useCurrent();
        });

        Schema::create('e_permit_applications', function (Blueprint $table) {
            $table->string('applicationId', 64)->primary();
            $table->string('companyId', 64)->nullable()->index();
            $table->string('appliedBy')->nullable();
            $table->integer('applicationStatus')->nullable();
            $table->text('applicationComment')->nullable();
            $table->string('typeOfPromotionEvent')->nullable();
            $table->text('reviewComment')->nullable();
            $table->text('lastComment')->nullable();
            $table->string('typeOfApplication')->nullable();
            $table->string('permitNo')->nullable();
            $table->string('authorizedBy')->nullable();
            $table->date('DateOfIssue')->nullable();
            $table->boolean('allowedToEdit')->nullable();
            $table->boolean('mngViewStatus')->nullable();
            $table->dateTime('applicationDate')->useCurrent();
        });

        Schema::create('event_participants', function (Blueprint $table) {
            $table->string('participantId', 64)->primary();
            $table->string('participantName')->nullable();
            $table->string('participantEmail')->nullable();
            $table->string('participantPhone')->nullable();
            $table->string('participantWebsite')->nullable();
            $table->string('participantProfile')->nullable();
            $table->string('participantCompany')->nullable();
            $table->string('participantTitle')->nullable();
            $table->string('eventNumber')->nullable();
            $table->string('companyProfile')->nullable();
            $table->string('participantPhoto')->nullable();
            $table->string('companyLogo')->nullable();
            $table->string('participantStatus')->nullable();
            $table->unsignedInteger('participantRank')->nullable();
            $table->string('sector')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->dateTime('registeredAt')->useCurrent();
        });

        Schema::create('expo_participants', function (Blueprint $table) {
            $table->string('participantId', 64)->primary();
            $table->string('firstName')->nullable();
            $table->string('middleName')->nullable();
            $table->string('lastName')->nullable();
            $table->date('dateOfBirth')->nullable();
            $table->string('sex')->nullable();
            $table->string('passportNumber')->nullable();
            $table->string('typeOfPassport')->nullable();
            $table->string('emailAddress')->nullable();
            $table->string('phoneNumber')->nullable();
            $table->string('companyName')->nullable();
            $table->string('companyEmail')->nullable();
            $table->string('companyPhoneNumber')->nullable();
            $table->string('companyProfile')->nullable();
            $table->string('typeOfParticipation')->nullable();
            $table->string('programOfInterest')->nullable();
            $table->text('anyOtherDescription')->nullable();
            $table->string('dateOfVisit', 32)->nullable();
            $table->string('passportSizePhoto')->nullable();
            $table->string('travelPassport')->nullable();
            $table->string('companySector')->nullable();
            $table->date('returningDate')->nullable();
            $table->string('workingId', 64)->nullable();
            $table->string('country')->nullable();
            $table->string('participantStatus')->nullable();
            $table->dateTime('createdAt')->useCurrent();
        });

        Schema::create('feed_backs', function (Blueprint $table) {
            $table->string('feedback_id', 64)->primary();
            $table->string('asked_by')->nullable();
            $table->string('attended_by')->nullable();
            $table->text('customer_comment')->nullable();
            $table->text('answer')->nullable();
            $table->boolean('is_faq')->nullable();
        });

        Schema::create('national_brand_competitions', function (Blueprint $table) {
            $table->string('competitionNumber', 64)->primary();
            $table->string('fullName')->nullable();
            $table->string('phoneNumber')->nullable();
            $table->string('zippedFile')->nullable();
            $table->text('logoDescription')->nullable();
            $table->string('status')->nullable();
            $table->string('emailAddress')->nullable();
            $table->decimal('totalMarks', 16, 4)->nullable();
            $table->decimal('averageMarks', 16, 4)->nullable();
        });

        Schema::create('permits', function (Blueprint $table) {
            $table->string('permit_id', 64)->primary();
            $table->string('company_id', 64)->nullable()->index();
            $table->string('event_id', 64)->nullable()->index();
            $table->string('permits')->nullable();
            $table->string('permit_type')->nullable();
            $table->string('created_by')->nullable();
            $table->string('permitStatus')->nullable();
            $table->string('approvedBy')->nullable();
            $table->string('permitNumber')->nullable();
            $table->text('itemList')->nullable();
            $table->dateTime('permit_created_at')->useCurrent();
        });

        Schema::create('promotion_event_types', function (Blueprint $table) {
            $table->string('eventTypeId', 64)->primary();
            $table->string('promotionEventTypeName')->nullable();
            $table->text('promotionEventTypeDescription')->nullable();
            $table->string('eventTypePoster')->nullable();
            $table->integer('typeStatus')->nullable();
            $table->decimal('eventTypeAmountUsd', 16, 4)->nullable();
            $table->decimal('eventTypeAmountTzs', 16, 4)->nullable();
        });

        Schema::create('promotion_events', function (Blueprint $table) {
            $table->string('eventId', 64)->primary();
            $table->string('eventTitle')->nullable();
            $table->date('startFrom')->nullable();
            $table->date('endTo')->nullable();
            $table->string('eventParticipants')->nullable();
            $table->text('eventDescription')->nullable();
            $table->string('eventPoster')->nullable();
            $table->string('eventStatus')->nullable();
            $table->date('registrationDeadline')->nullable();
            $table->string('eventNumber')->nullable();
            $table->dateTime('eventCreatedAt')->useCurrent();
        });

        Schema::create('tnb_criterias', function (Blueprint $table) {
            $table->string('criteriaCode', 64)->primary();
            $table->text('criteriaDescription')->nullable();
            $table->decimal('maxMark', 16, 4)->nullable();
            $table->dateTime('createdAt')->useCurrent();
        });

        Schema::create('tnb_marks', function (Blueprint $table) {
            $table->string('markId', 64)->primary();
            $table->string('tnbId', 64)->nullable();
            $table->string('criteria')->nullable();
            $table->integer('judgeNumber')->nullable();
            $table->decimal('marks', 16, 4)->nullable();
        });

        Schema::create('trade_promotion_events', function (Blueprint $table) {
            $table->string('tradeEventId', 64)->primary();
            $table->string('tradeEventType')->nullable();
            $table->string('eventTitle')->nullable();
            $table->string('eventTheme')->nullable();
            $table->text('eventObjectives')->nullable();
            $table->string('productExhibited')->nullable();
            $table->date('startFrom')->nullable();
            $table->date('endDate')->nullable();
            $table->integer('localParticipants')->nullable();
            $table->integer('foreignParticipants')->nullable();
            $table->text('MOFAttachment')->nullable();
            $table->decimal('entryFee', 16, 4)->nullable();
            $table->decimal('estimateExpenditure', 16, 4)->nullable();
            $table->string('eventWebsite')->nullable();
            $table->string('venueAddress')->nullable();
            $table->text('venueCoordinates')->nullable();
            $table->string('typeOfBuilding')->nullable();
            $table->string('venueOwner')->nullable();
            $table->string('venueOwnerAddress')->nullable();
            $table->string('venueSize')->nullable();
            $table->string('eventApplicationId', 64)->nullable()->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trade_promotion_events');
        Schema::dropIfExists('tnb_marks');
        Schema::dropIfExists('tnb_criterias');
        Schema::dropIfExists('promotion_events');
        Schema::dropIfExists('promotion_event_types');
        Schema::dropIfExists('permits');
        Schema::dropIfExists('national_brand_competitions');
        Schema::dropIfExists('feed_backs');
        Schema::dropIfExists('expo_participants');
        Schema::dropIfExists('event_participants');
        Schema::dropIfExists('e_permit_applications');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('application_reviewers');
        Schema::dropIfExists('application_invoices');
    }
}
