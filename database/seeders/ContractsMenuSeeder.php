<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ContractsMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create the permission if it doesn't exist
        Permission::firstOrCreate([
            'name' => 'view-contracts',
            'guard_name' => 'web'
        ]);

        echo "✓ Permission 'view-contracts' created/verified\n";

        // 2. Assign permission to roles 20, 21, and 34
        $roleIds = [20, 21, 34];
        foreach ($roleIds as $roleId) {
            $role = Role::find($roleId);
            if ($role) {
                if (!$role->hasPermissionTo('view-contracts')) {
                    $role->givePermissionTo('view-contracts');
                    echo "✓ Permission 'view-contracts' assigned to role: {$role->name} (ID: {$roleId})\n";
                } else {
                    echo "  Permission 'view-contracts' already assigned to role: {$role->name} (ID: {$roleId})\n";
                }
            } else {
                echo "⚠ Warning: Role with ID {$roleId} not found\n";
            }
        }

        // 3. Create menu item under Companies parent
        $companiesMenuId = '8c947bcc-a035-484f-8060-adc9c8d7a49a'; // Companies menu ID

        // Generate a unique UUID for the menu item
        $menuId = 'cc7e1ed5-f20f-42b6-bea9-eef26eacb001'; // Contracts menu UUID

        // Check if menu already exists
        $existingMenu = DB::table('level_two_menuses')->where('level_two_menu_id', $menuId)->first();

        if (!$existingMenu) {
            // Check if there's already a contracts menu by name
            $existingByName = DB::table('level_two_menuses')
                ->where('level_two_common_name', 'Manage Contracts')
                ->first();

            if ($existingByName) {
                echo "⚠ Menu item 'Manage Contracts' already exists with ID: {$existingByName->level_two_menu_id}\n";
                echo "  Updating existing menu item...\n";

                DB::table('level_two_menuses')
                    ->where('level_two_menu_id', $existingByName->level_two_menu_id)
                    ->update([
                        'level_two_gate_name' => 'view-contracts',
                        'level_two_menu_link' => 'contracts.index',
                        'level_two_icon_name' => 'icon-file-text2',
                        'level_two_menu_status' => 1,
                        'upper_level_id' => $companiesMenuId
                    ]);

                echo "✓ Menu item updated successfully\n";
            } else {
                DB::table('level_two_menuses')->insert([
                    'level_two_menu_id' => $menuId,
                    'level_two_common_name' => 'Manage Contracts',
                    'level_two_menu_status' => 1,
                    'level_two_icon_name' => 'icon-file-text2',
                    'level_two_menu_link' => 'contracts.index',
                    'level_two_gate_name' => 'view-contracts',
                    'upper_level_id' => $companiesMenuId,
                    'level_two_menu_created_at' => now(),
                    'level_two_menu_updated_at' => now()
                ]);

                echo "✓ Menu item 'Manage Contracts' created successfully\n";
            }
        } else {
            echo "  Menu item 'Manage Contracts' already exists\n";
        }

        echo "\n✓ All changes completed successfully!\n";
        echo "⚠ Remember to run: php artisan permission:cache-reset\n";
    }
}
