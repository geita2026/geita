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
            LevelOneMenusSeeder::class,
            LevelTwoMenusSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
