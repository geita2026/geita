<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = [
    'zones','tents','booths','bookings','payments','invoices',
    'exhibition_events','event_spaces','pavilions','streets',
    'booked_booths','booked_pavilions','space_categories',
    'area_distributions','user_spaces','price_configurations',
    'advice_notes','bills','payment_details','off_fair_space_bookings',
    'off_fair_spaces','exhibitors','events','additional_services',
];

foreach ($tables as $t) {
    echo str_pad($t, 32)." => ".(Schema::hasTable($t) ? 'IPO' : 'HAIPO').PHP_EOL;
}