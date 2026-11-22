<?php

use App\Support\PermissionNames;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class StandardizePermissionNames extends Migration
{
    public function up(): void
    {
        $aliases = PermissionNames::aliases();
        $tables = config('permission.table_names');
        $pivotKey = config('permission.column_names.permission_pivot_key') ?? 'permission_id';

        DB::transaction(function () use ($aliases, $tables, $pivotKey) {
            $groups = DB::table($tables['permissions'])->orderBy('id')->get()
                ->groupBy('guard_name');

            foreach ($groups as $permissions) {
                $byCanonicalName = $permissions->groupBy(fn ($permission) =>
                    PermissionNames::canonical($permission->name, $aliases));
                foreach ($byCanonicalName as $name => $variants) {
                    $keeper = $variants->firstWhere('name', (string) $name) ?? $variants->first();
                    foreach ($variants as $variant) {
                        if ($variant->id === $keeper->id) {
                            continue;
                        }

                        foreach (['role_has_permissions', 'model_has_permissions'] as $relation) {
                            $pivot = DB::table($tables[$relation]);
                            $grants = $pivot->where($pivotKey, $variant->id)->get()
                                ->map(fn ($grant) => array_replace((array) $grant, [$pivotKey => $keeper->id]));
                            foreach ($grants->chunk(100) as $batch) {
                                DB::table($tables[$relation])->insertOrIgnore($batch->all());
                            }
                            $pivot->delete();
                        }

                        DB::table($tables['permissions'])->where('id', $variant->id)->delete();
                    }
                    if ($keeper->name !== (string) $name) {
                        DB::table($tables['permissions'])->where('id', $keeper->id)->update(['name' => $name]);
                    }
                }
            }

            foreach (['one', 'two'] as $level) {
                $table = "level_{$level}_menuses";
                $id = "level_{$level}_menu_id";
                $gate = "level_{$level}_gate_name";
                foreach (DB::table($table)->whereNotNull($gate)->get([$id, $gate]) as $menu) {
                    $canonical = PermissionNames::canonical($menu->$gate, $aliases);
                    if ($menu->$gate !== $canonical) {
                        DB::table($table)->where($id, $menu->$id)->update([$gate => $canonical]);
                    }
                }
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Merged permission grants cannot be split back into their original names.
    }
}
