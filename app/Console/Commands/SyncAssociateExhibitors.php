<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncAssociateExhibitors extends Command
{
    protected $signature = 'exhibitors:sync-associates {eventId}';
    protected $description = 'Create exhibitors for associate companies whose parent company is already an exhibitor for the selected event';

    public function handle(): int
    {
        $eventId = $this->argument('eventId');
        DB::transaction(function () use ($eventId) {
            $rows = DB::table('companies as child')
                ->join('exhibitors as parent_exhibitor', function ($join) use ($eventId) {
                    $join->on('parent_exhibitor.exhibitor_id', '=', 'child.associate_company')
                         ->where('parent_exhibitor.exhibitor_event', '=', $eventId);
                })
                ->whereNotNull('child.associate_company')
                ->whereNotExists(function ($query) use ($eventId) {
                    $query->select(DB::raw(1))
                        ->from('exhibitors as existing')
                        ->whereColumn('existing.exhibitor_id', 'child.companyID')
                        ->where('existing.exhibitor_event', $eventId);
                })
                ->select([
                    'child.companyID as exhibitor_id',
                    'parent_exhibitor.exhibitor_event',
                    'parent_exhibitor.visibility_status',
                    'parent_exhibitor.judging_status',
                    'parent_exhibitor.pStatus',
                    'parent_exhibitor.fileIsEditable',
                    'parent_exhibitor.makeSales',
                ])
                ->get();

            if ($rows->isEmpty()) {
                $this->info('No missing associate exhibitors found.');
                return;
            }

            $now = now();

            $insertData = $rows->map(function ($row) use ($now) {
                return [
                    'participation_id' => (string) Str::uuid(),
                    'exhibitor_id' => $row->exhibitor_id,
                    'served_at' => $now,
                    'updated_at' => $now,
                    'exhibitor_event' => $row->exhibitor_event,
                    'visibility_status' => $row->visibility_status,
                    'judging_status' => $row->judging_status,
                    'pStatus' => $row->pStatus,
                    'fileIsEditable' => $row->fileIsEditable,
                    'makeSales' => $row->makeSales,
                ];
            })->toArray();

            DB::table('exhibitors')->insert($insertData);

            $this->info(count($insertData) . ' associate exhibitors created successfully.');
        });

        return self::SUCCESS;
    }
}