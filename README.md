# tradefair.tantrade.go.tz
Space Allocation Management System

## Initial setup

Set `SUPER_ADMIN_EMAIL` and `SUPER_ADMIN_PASSWORD` in `.env` before running `php artisan migrate --seed`. Use `php artisan db:seed --class=SuperAdminSeeder` if the schema has already been migrated. If configuration is cached after changing these values, refresh the configuration cache before seeding.

Sign in at `/login` with the configured email and password. Re-running the seeder preserves the account's current password.

## Deployment Notes

After every deployment, run the following commands in order:

```bash
php artisan migrate
php artisan config:cache
php artisan permission:cache-reset
```

`permission:cache-reset` flushes Spatie's permission cache so the app picks up any new or modified roles/permissions. It does **not** log users out or affect sessions. Skipping this step can cause "You do not have permission to perform this Action" errors for users who should have access.

### Why this matters

Spatie caches all roles and permissions for performance (default: 24 hours). The cache is automatically cleared when you use Spatie's own methods in Laravel (`syncPermissions`, `assignRole`, `givePermissionTo`, etc.).

However, modifying permissions **outside** of Laravel — such as direct SQL queries, database seeders, phpMyAdmin edits, or importing/restoring database dumps — will **not** clear the cache. The app will keep serving stale permission data, causing users to get "access denied" errors even though their permissions are correct in the database. Always run `php artisan permission:cache-reset` after any direct database changes to the permission tables (`roles`, `permissions`, `role_has_permissions`, `model_has_roles`, `model_has_permissions`).

