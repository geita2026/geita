<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Generates load-test tickets that match the shape of real
 * biasharaapp / eTAC POS / consumer-app tickets:
 *
 *   - DB column `ticketId` is always a BARE 36-char dashed UUID
 *     (same as real tickets — verified via phpMyAdmin).
 *
 *   - The smart wrapper (TICKET|YYYYMMDD|uuid, VIP|range|uuid)
 *     is a *render* concern, applied only when generating the QR
 *     image. It is NOT stored in the DB.
 *
 *   - For k6 / Flutter load tests we still want to exercise every
 *     scanner-side parser branch, so the JSON export tells the
 *     consumer which `qrFormat` to render for each ticket.
 *
 * Run:
 *   php -d memory_limit=2G artisan db:seed --class=LoadTestTicketSeeder
 */
class LoadTestTicketSeeder extends Seeder
{
    // ── Configuration ─────────────────────────────────────────────────────────
    const TOTAL       = 500_000;
    const BATCH_SIZE  = 2_000;
    const EXPORT_FILE = 'storage/app/load_test_ticket_ids.json';

    /**
     * VIP season range — used at *QR render time* only.
     * Format: YYYYMMDD-YYYYMMDD
     */
    const VIP_SEASON = '20260724-20260802';

    /** Date split for non-VIP tickets (out of 10). */
    const TODAY_SHARE     = 7;
    const YESTERDAY_SHARE = 3;

    /** QR format mix for non-VIP tickets (out of 10). */
    const FORMAT_SMART_SHARE  = 5; // TICKET|YYYYMMDD|uuid    — biasharaapp
    const FORMAT_BARE_SHARE   = 3; // <uuid>                  — consumer app
    const FORMAT_LEGACY_SHARE = 2; // <uuid><DDMM-secret>     — eTAC POS

    const FALLBACK_PACKAGES = [
        ['name' => 'Adult Entry',   'amount' => 3000],
        ['name' => 'Student Entry', 'amount' => 1000],
        ['name' => 'Child Entry',   'amount' => 1000],
        ['name' => 'Weekend Pass',  'amount' => 4000],
        ['name' => 'VIP Entry',     'amount' => 1500000],
    ];

    public function run(): void
    {
        $this->command->info('🎫 Starting LoadTest Ticket Seeder...');
        $this->command->info('   Total tickets : ' . number_format(self::TOTAL));
        $this->command->info('   Batch size    : ' . number_format(self::BATCH_SIZE));
        $this->command->newLine();

        // ── Load real ticket packages ─────────────────────────────────────────
        $dbPackages = DB::table('vip_ticket_packages')
            ->where('packageStatus', 1)
            ->select('package_discriptions as name', 'package_cost as amount')
            ->get()
            ->map(fn ($p) => ['name' => $p->name, 'amount' => (int) $p->amount])
            ->toArray();

        $packages = count($dbPackages) > 0 ? $dbPackages : self::FALLBACK_PACKAGES;
        $pkgCount = count($packages);

        $this->command->info('📦 Using ' . $pkgCount . ' ticket type(s):');
        foreach ($packages as $pkg) {
            $this->command->line('   • ' . $pkg['name'] . ' — ' . number_format($pkg['amount']) . ' TZS');
        }
        $this->command->newLine();

        // ── Dates ─────────────────────────────────────────────────────────────
        $todayDb      = now()->format('Y-m-d');
        $yesterdayDb  = now()->subDay()->format('Y-m-d');
        $todayStr     = now()->format('Ymd');         // for smart wrapper
        $yesterdayStr = now()->subDay()->format('Ymd');
        $seededAt     = now()->toDateTimeString();
        $dailySecret  = now()->format('dm');          // DDMM rotation for legacy QR

        $this->command->info('📅 Date distribution (non-VIP tickets):');
        $this->command->line('   Today     → ' . $todayDb . '  (' . self::TODAY_SHARE . '/10 tickets) — accepted');
        $this->command->line('   Yesterday → ' . $yesterdayDb . ' (' . self::YESTERDAY_SHARE . '/10 tickets) — rejected (stale)');
        $this->command->line('   VIP       → season ' . self::VIP_SEASON . ' (never expires)');
        $this->command->newLine();

        $this->command->info('🔤 QR format mix (non-VIP):');
        $this->command->line('   smart  (TICKET|YYYYMMDD|uuid) — ' . self::FORMAT_SMART_SHARE  . '/10');
        $this->command->line('   bare   (<uuid>)                — ' . self::FORMAT_BARE_SHARE   . '/10');
        $this->command->line('   legacy (<uuid><DDMM>)          — ' . self::FORMAT_LEGACY_SHARE . '/10');
        $this->command->newLine();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('ALTER TABLE produced_tickets DISABLE KEYS');

        // ── Stream the JSON export to disk row-by-row so 500k rows
        //    never live in memory at once.
        $exportPath = base_path(self::EXPORT_FILE);
        @mkdir(dirname($exportPath), 0775, true);
        $fh = fopen($exportPath, 'w');
        if ($fh === false) {
            throw new \RuntimeException("Cannot open export file: $exportPath");
        }
        fwrite($fh, '{'
            . '"generated_at":' . json_encode($seededAt)
            . ',"today":'       . json_encode($todayDb)
            . ',"yesterday":'   . json_encode($yesterdayDb)
            . ',"vip_season":'  . json_encode(self::VIP_SEASON)
            . ',"daily_secret":'. json_encode($dailySecret)
            . ',"tickets":[');
        $firstExport = true;

        $bar = $this->command->getOutput()->createProgressBar(self::TOTAL);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %elapsed:6s% elapsed');
        $bar->start();

        $batches        = intdiv(self::TOTAL, self::BATCH_SIZE);
        $countToday     = 0;
        $countYesterday = 0;
        $countVip       = 0;
        $countSmart     = 0;
        $countBare      = 0;
        $countLegacy    = 0;

        for ($b = 0; $b < $batches; $b++) {
            $rows = [];

            for ($i = 0; $i < self::BATCH_SIZE; $i++) {
                $position = $b * self::BATCH_SIZE + $i;
                $pkg      = $packages[$position % $pkgCount];
                $isVip    = $pkg['amount'] >= 1500000;

                $uuid = (string) Str::uuid();

                if ($isVip) {
                    $visitDate = $todayDb;
                    $qrFormat  = 'vip';
                    $qrPayload = 'VIP|' . self::VIP_SEASON . '|' . $uuid;
                    $countVip++;
                } else {
                    // Date slot: 7/10 today, 3/10 yesterday
                    $dateSlot = $position % 10;
                    if ($dateSlot < self::TODAY_SHARE) {
                        $visitDate = $todayDb;
                        $dateStr   = $todayStr;
                        $countToday++;
                    } else {
                        $visitDate = $yesterdayDb;
                        $dateStr   = $yesterdayStr;
                        $countYesterday++;
                    }

                    // Format slot (independent of date slot)
                    $fmtSlot = intdiv($position, 7) % 10;
                    if ($fmtSlot < self::FORMAT_SMART_SHARE) {
                        $qrFormat  = 'smart';
                        $qrPayload = 'TICKET|' . $dateStr . '|' . $uuid;
                        $countSmart++;
                    } elseif ($fmtSlot < self::FORMAT_SMART_SHARE + self::FORMAT_BARE_SHARE) {
                        $qrFormat  = 'bare';
                        $qrPayload = $uuid;
                        $countBare++;
                    } else {
                        $qrFormat  = 'legacy';
                        $qrPayload = $uuid . $dailySecret;
                        $countLegacy++;
                    }
                }

                // The DB row stores ONLY the bare UUID in ticketId —
                // matching every real biasharaapp / eTAC / consumer ticket.
                // `produced_tickets` doesn't use Laravel's `created_at` /
                // `updated_at` columns — `ticketDate` is the timestamp.
                $rows[] = [
                    'ticketId'     => $uuid,
                    'bookingId'    => (string) Str::uuid(),
                    'ticketNo'     => strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(8)),
                    'ticketStatus' => 1,
                    'scanCount'    => 0,
                    'ticketAmount' => $pkg['amount'],
                    'customerName' => 'Load Test User ' . ($position + 1),
                    'ticketName'   => $pkg['name'],
                    'visitDate'    => $visitDate,
                    'ticketDate'   => $seededAt,
                ];

                // Stream one export row per ticket so memory stays flat.
                fwrite($fh, ($firstExport ? '' : ',') . json_encode([
                    'uuid'      => $uuid,
                    'qrFormat'  => $qrFormat,
                    'qrPayload' => $qrPayload,
                    'type'      => $isVip ? 'VIP' : 'TICKET',
                    'visitDate' => $visitDate,
                    'amount'    => $pkg['amount'],
                ]));
                $firstExport = false;
            }

            DB::table('produced_tickets')->insert($rows);
            $bar->advance(self::BATCH_SIZE);
        }

        DB::statement('ALTER TABLE produced_tickets ENABLE KEYS');
        DB::statement('ANALYZE TABLE produced_tickets');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Close the JSON array + envelope.
        fwrite($fh, '],"counts":' . json_encode([
            'total'       => self::TOTAL,
            'today'       => $countToday,
            'yesterday'   => $countYesterday,
            'vip'         => $countVip,
            'smart'       => $countSmart,
            'bare'        => $countBare,
            'legacy'      => $countLegacy,
        ]) . '}');
        fclose($fh);

        $bar->finish();
        $this->command->newLine(2);

        $this->command->info('✅ Done!');
        $this->command->table(
            ['Metric', 'Value'],
            [
                ['Total inserted',           number_format(self::TOTAL)],
                ['Today tickets',            number_format($countToday)     . ' — verifier accepts'],
                ['Yesterday tickets',        number_format($countYesterday) . ' — Flutter rejects client-side'],
                ['VIP tickets',              number_format($countVip)       . ' — season ' . self::VIP_SEASON],
                ['QR: smart  (TICKET|…)',    number_format($countSmart)],
                ['QR: bare   (<uuid>)',      number_format($countBare)],
                ['QR: legacy (<uuid><DDMM>)',number_format($countLegacy)],
                ['Seed date',                $todayDb],
                ['Daily secret (DDMM)',      $dailySecret],
                ['Export file',              self::EXPORT_FILE],
                ['Wipe THIS run',
                    "DELETE FROM produced_tickets\n  WHERE DATE(ticketDate) = '{$todayDb}'\n  AND customerName LIKE 'Load Test User %'"],
            ]
        );
    }
}
