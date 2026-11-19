<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exhibitors', function (Blueprint $t) {
            $t->id();
            $t->string('organization_name');
            $t->string('registration_number')->nullable();
            $t->string('tin')->nullable();
            $t->string('contact_person');
            $t->string('phone', 30);
            $t->string('email');
            $t->string('physical_address')->nullable();
            $t->string('country', 80)->default('Tanzania');
            $t->string('business_sector')->nullable();
            $t->text('business_activity')->nullable();
            $t->string('exhibitor_type', 30)->default('local'); // local|international|government|sme
            $t->string('status', 20)->default('active');        // active|suspended
            $t->timestamps();
            $t->index('organization_name');
        });

        Schema::table('users', function (Blueprint $t) {
            $t->string('phone', 30)->nullable();
            $t->string('role', 40)->default('exhibitor')->index();
            $t->boolean('is_active')->default(true);
            $t->boolean('must_change_password')->default(false);
            $t->unsignedTinyInteger('failed_logins')->default(0);
            $t->timestamp('locked_at')->nullable();
            $t->timestamp('last_login_at')->nullable();
            $t->foreignId('exhibitor_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::create('exhibitions', function (Blueprint $t) {
            $t->id();
            $t->string('code', 30)->unique();
            $t->string('name');
            $t->string('theme')->nullable();
            $t->text('objectives')->nullable();
            $t->string('venue');
            $t->date('start_date');
            $t->date('end_date');
            $t->date('registration_opens_on');
            $t->date('registration_closes_on');
            $t->date('payment_deadline');
            $t->json('categories')->nullable();          // exhibition categories (sectors)
            $t->json('required_documents')->nullable();  // BR-003 mandatory document types
            $t->text('terms')->nullable();
            $t->string('contact_email')->nullable();
            $t->string('contact_phone', 30)->nullable();
            $t->string('currency', 3)->default('TZS');
            $t->decimal('vat_rate', 5, 2)->default(18);
            $t->boolean('allow_self_selection')->default(true);   // FR-023
            $t->string('status', 30)->default('draft')->index();  // draft|published|registration_closed|ongoing|completed|cancelled
            $t->timestamp('published_at')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('halls', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exhibition_id')->constrained()->cascadeOnDelete();
            $t->string('code', 20);
            $t->string('name');
            $t->string('kind', 20)->default('indoor'); // indoor|outdoor|pavilion
            $t->string('description')->nullable();
            $t->unsignedSmallInteger('grid_cols')->default(12);
            $t->unsignedSmallInteger('sort')->default(0);
            $t->timestamps();
            $t->unique(['exhibition_id', 'code']);
        });

        Schema::create('space_categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exhibition_id')->constrained()->cascadeOnDelete();
            $t->string('code', 20);
            $t->string('name');
            $t->string('description')->nullable();
            $t->decimal('price_per_sqm', 14, 2)->default(0);
            $t->string('color', 9)->default('#3b82f6');
            $t->timestamps();
            $t->unique(['exhibition_id', 'code']);
        });

        Schema::create('spaces', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exhibition_id')->constrained()->cascadeOnDelete();
            $t->foreignId('hall_id')->constrained()->cascadeOnDelete();
            $t->foreignId('space_category_id')->constrained()->restrictOnDelete();
            $t->string('number', 20);
            $t->decimal('width_m', 6, 2);
            $t->decimal('length_m', 6, 2);
            $t->decimal('area_sqm', 8, 2);
            $t->decimal('price', 14, 2);
            $t->string('status', 30)->default('available')->index();
            $t->json('facilities')->nullable();
            $t->string('location_note')->nullable();
            $t->string('special_conditions')->nullable();
            $t->unsignedSmallInteger('map_col')->default(1);
            $t->unsignedSmallInteger('map_row')->default(1);
            $t->unsignedSmallInteger('map_w')->default(1);
            $t->unsignedSmallInteger('map_h')->default(1);
            $t->timestamps();
            $t->unique(['exhibition_id', 'number']);
        });

        Schema::create('applications', function (Blueprint $t) {
            $t->id();
            $t->string('reference', 40)->unique();
            $t->foreignId('exhibition_id')->constrained()->restrictOnDelete();
            $t->foreignId('exhibitor_id')->constrained()->restrictOnDelete();
            $t->foreignId('space_category_id')->nullable()->constrained()->nullOnDelete();
            $t->string('preferred_size', 40)->nullable();
            $t->unsignedSmallInteger('spaces_requested')->default(1);
            $t->string('product_category')->nullable();
            $t->text('products_description')->nullable();
            $t->text('special_requirements')->nullable();
            $t->string('status', 30)->default('draft')->index();
            $t->unsignedTinyInteger('priority')->default(3); // 1 highest, used by auto-allocation
            $t->timestamp('submitted_at')->nullable();
            $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('reviewed_at')->nullable();
            $t->text('review_notes')->nullable();
            $t->timestamp('confirmed_at')->nullable();
            $t->timestamps();
            $t->index(['exhibition_id', 'exhibitor_id']);
        });

        Schema::create('application_documents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('application_id')->constrained()->cascadeOnDelete();
            $t->string('type');
            $t->string('original_name');
            $t->string('path');
            $t->string('mime', 100)->nullable();
            $t->unsignedBigInteger('size')->default(0);
            $t->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('allocations', function (Blueprint $t) {
            $t->id();
            $t->string('reference', 40)->unique();
            $t->foreignId('application_id')->constrained()->restrictOnDelete();
            $t->foreignId('exhibition_id')->constrained()->restrictOnDelete();
            $t->foreignId('exhibitor_id')->constrained()->restrictOnDelete();
            $t->foreignId('space_id')->constrained()->restrictOnDelete();
            /* BR-001 / FR-010: equals space_id while the allocation is live and NULL once it
               ends. The unique index makes a second live allocation of a space impossible
               at the database level, whatever the application code does. */
            $t->unsignedBigInteger('active_space_id')->nullable()->unique();
            $t->string('status', 20)->default('allocated')->index(); // pending|allocated|cancelled|reallocated|transferred
            $t->string('source', 20)->default('officer');            // officer|self|auto
            $t->foreignId('allocated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('allocated_at')->nullable();
            $t->foreignId('previous_allocation_id')->nullable()->constrained('allocations')->nullOnDelete();
            $t->text('change_reason')->nullable();
            $t->foreignId('ended_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('ended_at')->nullable();
            $t->string('occupancy_status', 20)->default('not_occupied'); // not_occupied|checked_in|occupied|vacant|cancelled
            $t->string('checkin_reference', 40)->nullable();
            $t->timestamp('checked_in_at')->nullable();
            $t->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('checkin_notes')->nullable();
            $t->timestamps();
        });

        Schema::create('invoices', function (Blueprint $t) {
            $t->id();
            $t->string('number', 40)->unique();
            $t->string('control_number', 20)->nullable()->unique();
            $t->foreignId('exhibition_id')->constrained()->restrictOnDelete();
            $t->foreignId('exhibitor_id')->constrained()->restrictOnDelete();
            $t->foreignId('application_id')->constrained()->restrictOnDelete();
            $t->decimal('subtotal', 16, 2)->default(0);
            $t->decimal('vat_rate', 5, 2)->default(0);
            $t->decimal('vat_amount', 16, 2)->default(0);
            $t->decimal('total', 16, 2)->default(0);
            $t->decimal('amount_paid', 16, 2)->default(0);
            $t->string('currency', 3)->default('TZS');
            $t->string('status', 20)->default('unpaid')->index(); // unpaid|partially_paid|paid|overdue|cancelled|refunded
            $t->date('issued_on');
            $t->date('due_on');
            $t->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->foreignId('allocation_id')->nullable()->constrained()->nullOnDelete();
            $t->string('description');
            $t->string('space_number', 20)->nullable();
            $t->string('space_size', 40)->nullable();
            $t->string('space_category')->nullable();
            $t->decimal('unit_price', 14, 2);
            $t->decimal('quantity', 8, 2)->default(1);
            $t->decimal('amount', 16, 2);
            $t->timestamps();
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->string('reference', 40)->unique();
            $t->foreignId('invoice_id')->constrained()->restrictOnDelete();
            $t->decimal('amount', 16, 2);
            $t->date('paid_on');
            $t->string('method', 30)->default('bank');   // bank|mobile_money|control_number|cash
            $t->string('payer_reference')->nullable();    // bank slip / transaction id
            $t->string('evidence_path')->nullable();
            $t->string('evidence_name')->nullable();
            $t->string('verification_status', 20)->default('pending')->index(); // pending|verified|rejected
            $t->string('channel', 20)->default('manual'); // manual|gateway
            $t->string('receipt_number', 40)->nullable()->unique();
            $t->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('verified_at')->nullable();
            $t->string('verification_notes')->nullable();
            $t->timestamps();
            $t->unique(['invoice_id', 'payer_reference']);
        });

        Schema::create('app_notifications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('kind', 40);
            $t->string('title');
            $t->text('body');
            $t->string('href')->nullable();
            $t->json('channels')->nullable(); // which channels were dispatched: in_app, email, sms
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'read_at']);
        });

        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('user_name')->nullable();
            $t->string('action', 60)->index();
            $t->string('reference', 60)->nullable()->index();
            $t->string('entity_type', 60)->nullable();
            $t->unsignedBigInteger('entity_id')->nullable();
            $t->string('summary');
            $t->json('old_values')->nullable();
            $t->json('new_values')->nullable();
            $t->string('ip_address', 45)->nullable();
            $t->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'app_notifications', 'payments', 'invoice_items', 'invoices', 'allocations',
            'application_documents', 'applications', 'spaces', 'space_categories', 'halls', 'exhibitions'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('exhibitor_id');
            $t->dropColumn(['phone', 'role', 'is_active', 'must_change_password', 'failed_logins', 'locked_at', 'last_login_at']);
        });
        Schema::dropIfExists('exhibitors');
    }
};
