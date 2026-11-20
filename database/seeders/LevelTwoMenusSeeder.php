<?php

namespace Database\Seeders;

use App\Models\setting\level_one_menus;
use App\Models\setting\level_two_menus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class LevelTwoMenusSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('navigation') as $parentKey => [$label, $module, $icon, $submenus]) {
            $parent = level_one_menus::where('level_one_gate_name', "VIEW_{$parentKey}_MENU")->firstOrFail();

            foreach ($submenus as $key => [$name, $route, $actions]) {
                $gate = "VIEW_{$key}_SUBMENU";
                foreach (array_unique([$gate, ...array_values($actions)]) as $permission) {
                    Permission::findOrCreate($permission, 'web');
                }

                $submenu = level_two_menus::where('level_two_gate_name', $gate)->first()
                    ?? level_two_menus::where('level_two_menu_link', $route)->first()
                    ?? new level_two_menus(['level_two_menu_id' => Str::uuid()]);

                $submenu->fill([
                    'level_two_common_name' => $name,
                    'level_two_menu_status' => 1,
                    'level_two_icon_name' => $icon,
                    'level_two_menu_link' => $route,
                    'level_two_gate_name' => $gate,
                    'upper_level_id' => $parent->getKey(),
                ])->save();
            }
        }
    }
}
