<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Stands up the Geita Mineral Exhibition as its own event, with its own space
 * categories and the six pavilions the Geita team drew, then makes it the live
 * event.
 *
 * Pavilions reach an event only through space_categories.category_event, so
 * giving Geita its own categories detaches the imported Dar es Salaam
 * inventory in one move. Nothing is deleted: the old event keeps all of its
 * pavilions, booths and bookings and simply goes dormant (event_status = 0).
 *
 * Idempotent — every row is matched on its natural key.
 */
class GeitaExhibitionSeeder extends Seeder
{
    private const EVENT_NAME = 'Geita Mineral Exhibition 2026';
    private const BOOTH_SIZE = 9;

    /** Each pavilion: 7 columns of [top, bottom]; null = EMPTY. */
    private const PAVILIONS = [
        'MARTIN SHIGELLA PAVILLION' => [
            ['MWAMBA', 'MWAMBA'], ['AMOGTECH', null], ['PUMA', null], ['ESAP', 'ESAP'],
            ['FEMA', 'FEMA'], ['BUCKREEF', 'BUCKREEF'], ['BUCKREEF', 'BUCKREEF'],
        ],
        'ALEXANDER MNYETI PAVILLION' => [
            ['TBA', 'TBA'], ['NHIF', 'NHIF'], ['MAHAKAMA', 'MAHAKAMA'], ['PSSSF', 'PSSSF'],
            ['NSSF', 'NSSF'], ['NSSF', 'NSSF'], ['NSSF', 'NSSF'],
        ],
        'MOHAMED GOMBAT PAVILLION' => [
            ['CRDB', 'CRDB'], ['TANTRADE', 'TANTRADE'], [null, null], ['WCF', 'WCF'],
            ['NMB', 'NMB'], ['AZANIA', 'AZANIA'], ['AZANIA', 'AZANIA'],
        ],
        'ROSEMARY SENYAMULE PAVILLION' => [
            ['WAJA', 'WAJA'], ['ISRA', 'ISRA'], ['CAPITAL & MSALABS', 'CAPITAL & MSALABS'],
            ['JEMA', 'JEMA'], ['AKO', 'NJEMA'], ['NYANZAGA', 'NYANZAGA'], ['NYANZAGA', 'NYANZAGA'],
        ],
        'ROBERT GABRIEL PAVILLION' => [
            ['GGML', 'GGML'], ['GGML', 'GGML'], ['GGML', 'GGML'], ['GGML', 'GGML'],
            ['GGML', 'GGML'], ['SGS', 'SGS'], ['BLUE COAST', 'BLUE COAST'],
        ],
    ];

    /** Uwanja wa Mezani is a single row of 13 numbered plots. */
    private const MEZANI_PLOTS = [
        1 => 'CITY CENTER', 2 => 'CITY CENTER', 3 => 'CITY CENTER',
        4 => 'EAST AFRICAN DISTILLERS', 5 => 'MARTHA PUB', 6 => null, 7 => null,
        8 => 'UWABAGE', 9 => null, 10 => null, 11 => null, 12 => null, 13 => 'SHANSHUI',
    ];

    public function run()
    {
        $userId = DB::table('users')->value('user_uuid');
        $sectors = DB::table('sectors')->pluck('sectorName', 'sectorID');
        $boothCategoryId = DB::table('booth_categories')->value('booth_category_id');

        if (!$userId || $sectors->isEmpty() || !$boothCategoryId) {
            $this->command->error('Missing users, sectors or booth categories. Nothing seeded.');
            return;
        }
        $allowed = $sectors->values()->implode(', ');
        $firstSector = $sectors->keys()->first();

        // ---- the Geita event -------------------------------------------
        $event = DB::table('events')->where('event_name', self::EVENT_NAME)->where('event_status', '!=', 9)->first();
        $eventId = $event->event_id ?? null;

        if (!$eventId) {
            $eventId = (string) Str::uuid();
            DB::table('events')->insert([
                'event_id' => $eventId,
                'event_name' => self::EVENT_NAME,
                'event_descriptions' => 'Geita Mineral Exhibition — minerals, equipment and services, Geita, Tanzania.',
                'event_status' => 0,
                'event_location' => 'Geita Mineral Exhibition Grounds, Geita',
                'event_start_date' => '2026-06-28',
                'event_end_date' => '2026-07-13',
                'event_created_at' => now(),
            ]);
            $this->command->info('Created event ' . self::EVENT_NAME);
        }

        // ---- its own categories ----------------------------------------
        $categories = [];
        foreach ([
            ['Exhibition Pavilions', 100000, 'TZS', 'Indoor pavilions divided into 3m x 3m booths.'],
            ['Open Plots', 50000, 'TZS', 'Outdoor plots, including Uwanja wa Mezani.'],
        ] as [$name, $cost, $currency, $desc]) {
            $row = DB::table('space_categories')
                ->where('category_name', $name)->where('category_event', $eventId)->first();
            if ($row) {
                $categories[$name] = $row->category_id;
                continue;
            }
            $id = (string) Str::uuid();
            DB::table('space_categories')->insert([
                'category_id' => $id,
                'user_id' => $userId,
                'cost_one_msq' => $cost,
                'type_of_currency' => $currency,
                'created_at' => now(),
                'category_name' => $name,
                'background_color' => '#FFBF00',
                'category_icon' => 'icon-home4',
                'category_event' => $eventId,
                'category_descriptions' => $desc,
            ]);
            $categories[$name] = $id;
        }

        $spaceIds = [];

        // ---- the five drawn pavilions ----------------------------------
        foreach (self::PAVILIONS as $pavilionName => $columns) {
            $pavilionId = $this->pavilion($pavilionName, $categories['Exhibition Pavilions'], $userId, $allowed, count($columns) * 2 * self::BOOTH_SIZE, count($columns) * 2);
            $spaceIds[] = $pavilionId;
            $n = 0;
            $prefix = $this->prefix($pavilionName);
            foreach ($columns as $column) {
                foreach ($column as $companyName) {
                    $n++;
                    $spaceIds[] = $this->booth($pavilionId, $prefix . '-' . str_pad((string) $n, 2, '0', STR_PAD_LEFT), self::BOOTH_SIZE, $boothCategoryId, $allowed, $companyName, $eventId, $userId, $firstSector);
                }
            }
        }

        // ---- Uwanja wa Mezani: one row of numbered plots ----------------
        $mezaniId = $this->pavilion('UWANJA WA MEZANI', $categories['Open Plots'], $userId, $allowed, count(self::MEZANI_PLOTS) * 40, count(self::MEZANI_PLOTS));
        $spaceIds[] = $mezaniId;
        foreach (self::MEZANI_PLOTS as $no => $companyName) {
            $spaceIds[] = $this->booth($mezaniId, 'UWM-' . str_pad((string) $no, 2, '0', STR_PAD_LEFT), 40, $boothCategoryId, $allowed, $companyName, $eventId, $userId, $firstSector);
        }

        // ---- sector distribution ---------------------------------------
        $existing = DB::table('area_distributions')->whereIn('spaceId', $spaceIds)
            ->get(['spaceId', 'participants'])
            ->map(fn ($r) => $r->spaceId . '|' . $r->participants)->flip();
        $rows = [];
        foreach ($spaceIds as $spaceId) {
            foreach ($sectors->keys() as $sectorId) {
                if ($existing->has($spaceId . '|' . $sectorId)) continue;
                $rows[] = ['distributionId' => (string) Str::uuid(), 'spaceId' => $spaceId, 'participants' => $sectorId, 'createdAt' => now()];
            }
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('area_distributions')->insert($chunk);
        }

        // ---- make Geita live, retire the imported event ------------------
        DB::table('events')->where('event_status', 1)->where('event_id', '!=', $eventId)
            ->update(['event_name' => 'DITF Archive (imported data)', 'event_status' => 0, 'event_updated_at' => now()]);
        DB::table('events')->where('event_id', $eventId)->update(['event_status' => 1, 'event_updated_at' => now()]);

        $this->command->info(sprintf('Geita is live: 6 pavilions, %d spaces, %d new sector rows. eventId = %s', count($spaceIds), count($rows), $eventId));
    }

    private function prefix(string $name): string
    {
        $words = preg_split('/\s+/', $name);
        return strtoupper(substr($words[0], 0, 2) . substr($words[1] ?? 'P', 0, 1));
    }

    private function pavilion(string $name, string $categoryId, string $userId, string $allowed, $size, int $partitions): string
    {
        $row = DB::table('pavilions')->where('pavilionName', $name)->where('pavilionCategory', $categoryId)->first();
        if ($row) return $row->pavilionID;
        $id = (string) Str::uuid();
        DB::table('pavilions')->insert([
            'pavilionID' => $id, 'userID' => $userId, 'pavilionName' => $name,
            'TotalBooths' => $partitions, 'pavilionCategory' => $categoryId,
            'pavilionDate' => now(), 'pavilion_size' => (string) $size,
            'space_status' => 'UNRESERVED', 'allowedForBooking' => $allowed,
            'whoCanBook' => 'ANY', 'numberOfPartitions' => $partitions,
        ]);
        return $id;
    }

    private function booth(string $pavilionId, string $boothNo, $size, string $boothCategoryId, string $allowed, ?string $companyName, string $eventId, string $userId, string $firstSector): string
    {
        $row = DB::table('booths')->where('pavilionID', $pavilionId)->where('boothNo', $boothNo)->first();
        if ($row) {
            $boothId = $row->boothID;
        } else {
            $boothId = (string) Str::uuid();
            DB::table('booths')->insert([
                'boothID' => $boothId, 'pavilionID' => $pavilionId, 'boothNo' => $boothNo,
                'boothSize' => (string) $size, 'booth_category' => $boothCategoryId,
                'boothDate' => now(), 'bookingStatus' => 'UNRESERVED', 'allowedForBooking' => $allowed,
            ]);
        }

        if ($companyName !== null) {
            $companyId = $this->company($companyName, $firstSector);
            $booked = DB::table('booked_pavilions')->where('pavilionID', $boothId)->where('exhibition_year', $eventId)->exists();
            if (!$booked) {
                DB::table('booked_pavilions')->insert([
                    'bookingID' => (string) Str::uuid(), 'userID' => $userId, 'companyID' => $companyId,
                    'pavilionID' => $boothId, 'exhibition_year' => $eventId,
                    'created_at' => now(), 'booking_status' => 'ACTIVE',
                ]);
            }
        }

        return $boothId;
    }

    private function company(string $name, string $sectorId): string
    {
        $c = DB::table('companies')->where('companyName', $name)->first();
        if ($c) {
            if ($c->companySector === null) {
                DB::table('companies')->where('companyID', $c->companyID)->update(['companySector' => $sectorId, 'updated_at' => now()]);
            }
            return $c->companyID;
        }
        $id = (string) Str::uuid();
        DB::table('companies')->insert([
            'companyID' => $id, 'companyName' => $name, 'registrationDate' => now(),
            'registrationID' => 'GEITA-' . strtoupper(Str::random(8)),
            'companySector' => $sectorId, 'status' => 1,
        ]);
        return $id;
    }
}
