<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = trim((string) config('auth.initial_admin.email'));
        $password = (string) config('auth.initial_admin.password');

        if ($email === '' && $password === '') {
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
            throw new RuntimeException('Set a valid SUPER_ADMIN_EMAIL of at most 100 characters.');
        }

        (new User())->getConnection()->transaction(function () use ($email, $password) {
            $existing = User::where('email', $email)->first();

            if ($existing) {
                if (!$existing->hasRole('Super Admin', 'web')) {
                    throw new RuntimeException('SUPER_ADMIN_EMAIL is already in use by a user without the Super Admin role.');
                }

                $this->command?->info('The initial Super Admin account already exists.');
                return;
            }

            if (strlen($password) < 16 || strlen($password) > 72
                || trim($password) !== $password || str_contains($password, "\0")) {
                throw new RuntimeException('Set a SUPER_ADMIN_PASSWORD of 16 to 72 bytes without surrounding whitespace.');
            }

            $role = Role::findOrCreate('Super Admin', 'web');
            $admin = User::create([
                'user_uuid' => Str::uuid(),
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => $role->getKey(),
                'account_type' => 1,
                'account_status' => 1,
            ]);

            $admin->assignRole($role);
            $this->command?->info('Initial Super Admin account created.');
        });
    }
}
