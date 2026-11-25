<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class RegisterInvoiceActionPermissions extends Migration
{
    public function up(): void
    {
        foreach (['REQUEST_ADVICE_NOTE', 'DOWNLOAD_INVOICE', 'APPLY_DISCOUNT', 'EXTEND_DEADLINE', 'CANCEL_INVOICES'] as $name) {
            Permission::findOrCreate($name, 'web');
        }
    }

    public function down(): void
    {
        // These permissions can predate this migration or have role assignments.
    }
}
