<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

class RemoveOffFairModule extends Migration
{
    private const TABLES = [
        'booked_utilities',
        'off_fair_booking_particulars',
        'off_fair_bills',
        'off_fair_space_bookings',
        'off_fair_advice_notes',
        'space_size_category_costs',
        'off_fair_spaces',
        'off_fair_space_categories',
        'off_fair_utilities',
        'off_fair_customers',
        'off_fair_activities',
    ];

    private const ROUTE_RESOURCES = [
        'Off-Fair',
        'Off-Fair-Customers',
        'Off-Fair-Spaces',
        'Off-Fair-Utilities',
        'off-fair-space-booking',
        'space_categories',
        'off-fair-activities',
        'space_size_costs',
        'check-booking-status',
        'off_fair_booking_schedule',
        'Generated-Advice-Note',
        'Off-Fair-Advice-Notes',
        'online_booking',
    ];

    private const PERMISSIONS = [
        'VIEW_CHECK_BOOKING_STATUS_SUBMENU',
        'VIEW_GENERATED_ADVICE_NOTE_SUBMENU',
        'UPDATE_GENERATED_ADVICE_NOTE',
        'VIEW_SPACE_SIZE_COST_SUBMENU',
        'CREATE_SPACE_SIZE_COST',
        'UPDATE_SPACE_SIZE_COST',
        'DELETE_SPACE_SIZE_COST',
        'View Space Size Costs',
    ];

    public function up(): void
    {
        DB::transaction(function () {
            $parentIds = DB::table('level_one_menuses')
                ->get(['level_one_menu_id', 'level_one_common_name', 'level_one_gate_name'])
                ->filter(fn ($menu) => $this->isOffFair($menu->level_one_common_name)
                    || $this->isOffFair($menu->level_one_gate_name))
                ->pluck('level_one_menu_id');

            $submenuIds = DB::table('level_two_menuses')
                ->get(['level_two_menu_id', 'level_two_common_name', 'level_two_gate_name', 'level_two_menu_link', 'upper_level_id'])
                ->filter(fn ($menu) => $parentIds->contains($menu->upper_level_id)
                    || in_array(explode('.', $menu->level_two_menu_link ?? '', 2)[0], self::ROUTE_RESOURCES, true)
                    || in_array($menu->level_two_gate_name, self::PERMISSIONS, true)
                    || $this->isOffFair($menu->level_two_common_name)
                    || $this->isOffFair($menu->level_two_gate_name))
                ->pluck('level_two_menu_id');

            DB::table('level_two_menuses')->whereIn('level_two_menu_id', $submenuIds)->delete();
            DB::table('level_one_menuses')->whereIn('level_one_menu_id', $parentIds)->delete();

            $tables = config('permission.table_names');
            $pivotKey = config('permission.column_names.permission_pivot_key') ?? 'permission_id';
            $permissionIds = DB::table($tables['permissions'])->where('guard_name', 'web')
                ->get(['id', 'name'])
                ->filter(fn ($permission) => $this->isOffFair($permission->name)
                    || in_array($permission->name, self::PERMISSIONS, true))
                ->pluck('id');

            DB::table($tables['role_has_permissions'])->whereIn($pivotKey, $permissionIds)->delete();
            DB::table($tables['model_has_permissions'])->whereIn($pivotKey, $permissionIds)->delete();
            DB::table($tables['permissions'])->whereIn('id', $permissionIds)->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if (Schema::hasColumn('payment_details', 'off_fair_bill_paid')) {
            Schema::table('payment_details', function (Blueprint $table) {
                $table->dropColumn('off_fair_bill_paid');
            });
        }

        foreach (self::TABLES as $table) {
            Schema::dropIfExists($table);
        }
    }

    public function down(): void
    {
        // Removed feature data can only be restored from a database backup.
    }

    private function isOffFair(?string $name): bool
    {
        return preg_match('/(?:^|[\s_-])off[\s_-]*fair(?:$|[\s_-]|space)/i', $name ?? '') === 1;
    }
}
