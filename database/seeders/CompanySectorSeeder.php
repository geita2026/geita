<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanySectorSeeder extends Seeder
{
    private const SECTORS = [
        'Artisanal and Small-Scale Mining',
        'Mining and Mineral Exploration',
        'Mineral Processing and Beneficiation',
        'Mineral Trading and Export',
        'Gemstones and Jewellery',
        'Mining Equipment and Supplies',
        'Engineering and Construction',
        'Energy and Utilities',
        'Environment, Health and Safety',
        'Transport and Logistics',
        'Finance and Insurance',
        'Technology and Telecommunications',
        'Education and Research',
        'Government and Public Institutions',
        'Professional and Business Services',
        'Hospitality and Tourism',
        'Other',
    ];

    public function run(): void
    {
        $existing = DB::table('sectors')->pluck('sectorName')
            ->map(fn ($name) => Str::lower($name ?? ''))->flip();

        $newSectors = [];
        foreach (self::SECTORS as $name) {
            if ($existing->has(Str::lower($name))) {
                continue;
            }

            $newSectors[] = [
                'sectorID' => (string) Str::uuid(),
                'sectorName' => $name,
                'sectorStatus' => 1,
            ];
        }

        if ($newSectors) {
            DB::table('sectors')->insert($newSectors);
        }
    }
}
