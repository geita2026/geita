<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BookingMenuPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Role::findOrCreate('Customer', 'web')->givePermissionTo(
            Permission::findOrCreate('VIEW_SPACE_MENU', 'web'),
            Permission::findOrCreate('VIEW_BOOKING_SUBMENU', 'web')
        );
    }
}
