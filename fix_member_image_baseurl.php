<?php

/**
 * Fix member_image base URL on company_members.
 *
 * Some rows stored the image as a bare relative path "/public/Members/<file>"
 * (missing the domain). This rewrites those to the full canonical URL:
 *   https://tradefair.tantrade.go.tz/public/Members/<file>
 *
 * Usage (prod):
 *   sudo -u www-data HOME=/tmp php artisan tinker --execute="require 'fix_member_image_baseurl.php';" | cat
 *
 * Flip $DRY_RUN to false to actually write. A CSV backup of changed rows is
 * written first.
 */

use App\Models\ExhibitionBadges\CompanyMember;

$DRY_RUN  = true;
$OLD_BASE = '/public/Members/';                                       // what it was stored as
$NEW_BASE = 'https://tradefair.tantrade.go.tz/public/Members/';       // canonical full URL
$NAME     = 'MANCHELE';                                               // limit to this one user

// Pull only this user's rows whose image starts with the bare relative base.
$rows = CompanyMember::where('member_image', 'like', $OLD_BASE . '%')
    ->where('last_name', $NAME)
    ->get();

echo "Found " . $rows->count() . " row(s) for '{$NAME}' with member_image starting with '{$OLD_BASE}'.\n";

if ($rows->isEmpty()) {
    echo "Nothing to do.\n";
    return;
}

// Backup the rows we are about to touch.
$backupFile = '/tmp/member_image_backup_' . date('Ymd_His') . '.csv';
$fh = fopen($backupFile, 'w');
fputcsv($fh, ['member_id', 'old_member_image', 'new_member_image']);

$changed = 0;
foreach ($rows as $row) {
    $old = $row->member_image;

    // Replace only the leading base, preserving the filename.
    $new = $NEW_BASE . substr($old, strlen($OLD_BASE));

    if ($new === $old) {
        continue;
    }

    fputcsv($fh, [$row->member_id, $old, $new]);
    echo ($DRY_RUN ? '[DRY] ' : '[UPDATE] ') . $row->member_id . "\n    {$old}\n -> {$new}\n";

    if (!$DRY_RUN) {
        $row->member_image = $new;
        $row->save();
    }

    $changed++;
}

fclose($fh);

echo "\n" . ($DRY_RUN ? 'Would change' : 'Changed') . " {$changed} row(s).\n";
echo "Backup written to {$backupFile}\n";

if ($DRY_RUN) {
    echo "\nDRY RUN — no rows written. Set \$DRY_RUN = false to apply.\n";
}
