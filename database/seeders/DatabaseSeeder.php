<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Role::findOrCreate('Super Admin', 'web');
        Role::findOrCreate('Customer', 'web');

        $this->call([
            CountrySeeder::class,
            CompanySectorSeeder::class,
            LevelOneMenusSeeder::class,
            LevelTwoMenusSeeder::class,
            BookingMenuPermissionsSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
