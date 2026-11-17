<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/** First System Administrator for a production install. Prints a one-time password. */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('role', 'system_admin')->exists()) {
            $this->command->warn('A System Administrator already exists; nothing to do.');

            return;
        }
        $email = env('ADMIN_EMAIL') ?: $this->command->ask('Administrator email');
        $password = Str::password(16, symbols: false);
        User::create(['name' => env('ADMIN_NAME', 'System Administrator'), 'email' => strtolower($email), 'password' => $password, 'role' => 'system_admin', 'must_change_password' => true]);
        $this->command->info("Administrator $email created. One-time password: $password");
    }
}
