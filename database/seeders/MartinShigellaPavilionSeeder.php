<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds MARTIN SHIGELLA PAVILLION exactly as the architectural sheet shows it,
 * so the rendered floor plan can be compared side by side with the CAD drawing.
 *
 *   col 1  MWAMBA   | MWAMBA
 *   col 2  AMOGTECH | (empty)
 *   col 3  PUMA     | (empty)
 *   col 4  ESAP     | ESAP
 *   col 5  FEMA     | FEMA
 *   col 6  BUCKREEF | BUCKREEF
 *   col 7  BUCKREEF | BUCKREEF
 *
 * Idempotent: every row is matched on its natural key, so running it twice
 * changes nothing. It only inserts data — no controllers or migrations.
 */
class MartinShigellaPavilionSeeder extends Seeder
{
    private const PAVILION_NAME = 'MARTIN SHIGELLA PAVILLION';
    private const BOOTH_SIZE = 9;          // 3m x 3m, as on the sheet
    private const BOOTH_PREFIX = 'MSP-';

    /** Top row then bottom row for each of the 7 columns; null = EMPTY. */
    private const OCCUPANCY = [
        ['MWAMBA',   'MWAMBA'],
        ['AMOGTECH', null],
        ['PUMA',     null],
        ['ESAP',     'ESAP'],
        ['FEMA',     'FEMA'],
        ['BUCKREEF', 'BUCKREEF'],
        ['BUCKREEF', 'BUCKREEF'],
    ];

    public function run()
    {
        $event = DB::table('events')->where('event_status', 1)->first();
        if (!$event) {
            $this->command->error('No active event (events.event_status = 1). Nothing seeded.');
            return;
        }

        $category = DB::table('space_categories')
            ->where('category_event', $event->event_id)
            ->orderByRaw("category_name = 'Local shared' DESC")
            ->first();
        if (!$category) {
            $this->command->error('No space_categories row for the active event. Nothing seeded.');
            return;
        }

        // booths.booth_category is a FK into booth_categories, not space_categories.
        $boothCategoryId = DB::table('booths')
            ->select('booth_category', DB::raw('count(*) as uses'))
            ->groupBy('booth_category')
            ->orderByDesc('uses')
            ->value('booth_category')
            ?? DB::table('booth_categories')->value('booth_category_id');
        if (!$boothCategoryId) {
            $this->command->error('No booth_categories row to attach booths to. Nothing seeded.');
            return;
        }

        $userId = DB::table('users')->value('user_uuid');
        if (!$userId) {
            $this->command->error('No users found. Nothing seeded.');
            return;
        }

        $sectors = DB::table('sectors')->pluck('sectorName', 'sectorID');
        if ($sectors->isEmpty()) {
            $this->command->error('No sectors found. Nothing seeded.');
            return;
        }
        $allowedForBooking = $sectors->values()->implode(', ');

        // ---- pavilion -------------------------------------------------
        $pavilion = DB::table('pavilions')
            ->where('pavilionName', self::PAVILION_NAME)
            ->where('pavilionCategory', $category->category_id)
            ->first();

        if (!$pavilion) {
            $pavilionId = (string) Str::uuid();
            DB::table('pavilions')->insert([
                'pavilionID' => $pavilionId,
                'userID' => $userId,
                'pavilionName' => self::PAVILION_NAME,
                'TotalBooths' => count(self::OCCUPANCY) * 2,
                'pavilionCategory' => $category->category_id,
                'pavilionDescription' => 'Architectural reference pavilion (14 booths, 2 x 7).',
                'pavilionDate' => now(),
                'pavilion_size' => (string) (count(self::OCCUPANCY) * 2 * self::BOOTH_SIZE),
                'space_status' => 'UNRESERVED',
                'allowedForBooking' => $allowedForBooking,
                'whoCanBook' => 'ANY',
                'numberOfPartitions' => count(self::OCCUPANCY) * 2,
            ]);
            $this->command->info('Created pavilion ' . self::PAVILION_NAME);
        } else {
            $pavilionId = $pavilion->pavilionID;
            $this->command->line('Pavilion already present, reusing ' . $pavilionId);
        }

        // ---- booths, companies & bookings -----------------------------
        $boothIds = [];
        $boothNumber = 0;

        foreach (self::OCCUPANCY as $column) {
            foreach ($column as $companyName) {
                $boothNumber++;
                $boothNo = self::BOOTH_PREFIX . str_pad((string) $boothNumber, 2, '0', STR_PAD_LEFT);

                $booth = DB::table('booths')
                    ->where('pavilionID', $pavilionId)
                    ->where('boothNo', $boothNo)
                    ->first();

                if (!$booth) {
                    $boothId = (string) Str::uuid();
                    DB::table('booths')->insert([
                        'boothID' => $boothId,
                        'pavilionID' => $pavilionId,
                        'boothNo' => $boothNo,
                        'boothSize' => (string) self::BOOTH_SIZE,
                        'booth_category' => $boothCategoryId,
                        'boothDate' => now(),
                        'bookingStatus' => 'UNRESERVED',
                        'allowedForBooking' => $allowedForBooking,
                    ]);
                } else {
                    $boothId = $booth->boothID;
                }

                $boothIds[] = $boothId;

                if ($companyName === null) {
                    continue; // the two EMPTY booths stay bookable
                }

                $companyId = $this->resolveCompany($companyName, $sectors->keys()->first());

                $alreadyBooked = DB::table('booked_pavilions')
                    ->where('pavilionID', $boothId)   // holds boothID for booth bookings
                    ->where('exhibition_year', $event->event_id)
                    ->exists();

                if (!$alreadyBooked) {
                    DB::table('booked_pavilions')->insert([
                        'bookingID' => (string) Str::uuid(),
                        'userID' => $userId,
                        'companyID' => $companyId,
                        'pavilionID' => $boothId,
                        'exhibition_year' => $event->event_id,
                        'created_at' => now(),
                        'booking_status' => 'ACTIVE',
                    ]);
                }
            }
        }

        // ---- sector distribution so the booking sector check passes ----
        $spaceIds = array_merge([$pavilionId], $boothIds);
        $existing = DB::table('area_distributions')
            ->whereIn('spaceId', $spaceIds)
            ->get(['spaceId', 'participants'])
            ->map(fn ($row) => $row->spaceId . '|' . $row->participants)
            ->flip();

        $rows = [];
        foreach ($spaceIds as $spaceId) {
            foreach ($sectors->keys() as $sectorId) {
                if ($existing->has($spaceId . '|' . $sectorId)) {
                    continue;
                }
                $rows[] = [
                    'distributionId' => (string) Str::uuid(),
                    'spaceId' => $spaceId,
                    'participants' => $sectorId,
                    'createdAt' => now(),
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('area_distributions')->insert($chunk);
        }

        $this->command->info(sprintf(
            '%s ready: %d booths, %d booked, %d new sector rows. pavilionID = %s',
            self::PAVILION_NAME,
            count($boothIds),
            count($boothIds) - 2,
            count($rows),
            $pavilionId
        ));
    }

    /** Find the exhibitor by name, creating a minimal record only if absent. */
    private function resolveCompany(string $name, string $fallbackSectorId): string
    {
        $company = DB::table('companies')->where('companyName', $name)->first();
        if ($company) {
            if ($company->companySector === null) {
                DB::table('companies')
                    ->where('companyID', $company->companyID)
                    ->update(['companySector' => $fallbackSectorId, 'updated_at' => now()]);
            }
            return $company->companyID;
        }

        $companyId = (string) Str::uuid();
        DB::table('companies')->insert([
            'companyID' => $companyId,
            'companyName' => $name,
            'registrationDate' => now(),
            'registrationID' => 'MSP-' . strtoupper(Str::random(8)),
            'companySector' => $fallbackSectorId,
            'status' => 1,
        ]);

        return $companyId;
    }
}
