<?php

namespace Database\Seeders;

use App\Models\setting\level_one_menus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class LevelOneMenusSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('navigation') as $key => [$label, $module, $icon]) {
            $gate = "VIEW_{$key}_MENU";
            Permission::findOrCreate($gate, 'web');

            $menu = level_one_menus::where('level_one_gate_name', $gate)->first()
                ?? level_one_menus::where('moduleNo', $module)->where('level_one_common_name', $label)->first()
                ?? new level_one_menus(['level_one_menu_id' => Str::uuid()]);

            $menu->fill([
                'level_one_common_name' => $label,
                'level_one_menu_status' => 1,
                'level_one_icon_name' => $icon,
                'level_one_menu_link' => '#',
                'level_one_gate_name' => $gate,
                'moduleNo' => $module,
            ])->save();
        }
    }
}
