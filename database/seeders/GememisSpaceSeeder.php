<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GememisSpaceSeeder extends Seeder
{
    public function run()
    {
        $zones = [
            // Hema
            ['zone' => 'A', 'name' => 'Mineral Related', 'type' => 'tent', 'count' => 48, 'size' => 9],
            ['zone' => 'B', 'name' => 'Private Institutions & Companies', 'type' => 'tent', 'count' => 54, 'size' => 9],
            ['zone' => 'C', 'name' => 'Umbrella\'s', 'type' => 'tent', 'count' => 80, 'size' => 9],
            ['zone' => 'D', 'name' => 'Public Institutions', 'type' => 'tent', 'count' => 42, 'size' => 9],
            ['zone' => 'E', 'name' => 'Public Services', 'type' => 'tent', 'count' => 14, 'size' => 9],
            ['zone' => 'F', 'name' => 'Security', 'type' => 'tent', 'count' => 14, 'size' => 9],
            ['zone' => 'G', 'name' => 'Equipments', 'type' => 'tent', 'count' => 12, 'size' => 9],
            ['zone' => 'H', 'name' => 'Medical Services', 'type' => 'tent', 'count' => 12, 'size' => 9],
            ['zone' => 'I', 'name' => 'Contingency', 'type' => 'tent', 'count' => 4, 'size' => 9],
            // Slots
            ['zone' => 'J', 'name' => 'Bars', 'type' => 'slot', 'count' => 13, 'size' => 375],
            ['zone' => 'K', 'name' => 'Mama Lishe', 'type' => 'slot', 'count' => 28, 'size' => 40],
            ['zone' => 'L', 'name' => 'Equipments (Slots)', 'type' => 'slot', 'count' => 8, 'size' => 735],
        ];

        // Angalia kama meza ipo
        if (!DB::getSchemaBuilder()->hasTable('spaces')) {
            $this->command->warn('Meza ya spaces haipo. Ruka.');
            return;
        }

        foreach ($zones as $zone) {
            DB::table('spaces')->insert([
                'zone' => $zone['zone'],
                'name' => $zone['name'],
                'type' => $zone['type'],
                'total_units' => $zone['count'],
                'unit_size_sqm' => $zone['size'],
                'total_size_sqm' => $zone['count'] * $zone['size'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Maeneo mengine
        $areas = [
            ['name' => 'Open Space', 'type' => 'open_space', 'size_sqm' => 6270],
            ['name' => 'Parking Area', 'type' => 'parking', 'size_sqm' => 3909.454],
            ['name' => 'Children\'s Playground', 'type' => 'playground', 'size_sqm' => 14540],
            ['name' => 'Water Source', 'type' => 'water', 'size_sqm' => 7500],
            ['name' => 'Hotel Site 2', 'type' => 'hotel', 'size_sqm' => 24389],
            ['name' => 'Hotel Site 10', 'type' => 'hotel', 'size_sqm' => 14507],
            ['name' => 'Hotel Site 11', 'type' => 'hotel', 'size_sqm' => 14757],
            ['name' => 'Hotel Site (Other)', 'type' => 'hotel', 'size_sqm' => 13617],
            ['name' => 'Hotel Site (Other)', 'type' => 'hotel', 'size_sqm' => 15485],
        ];

        if (DB::getSchemaBuilder()->hasTable('spaces')) {
            foreach ($areas as $area) {
                DB::table('spaces')->insert([
                    'name' => $area['name'],
                    'type' => $area['type'],
                    'total_size_sqm' => $area['size_sqm'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('GeMEMIS space data imeingizwa: ' . count($zones) . ' zones + ' . count($areas) . ' areas');
    }
}
