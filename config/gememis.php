<?php
// GEMEMIS Configuration
return [
    'app_name'    => env('APP_NAME', 'GEMEMIS'),
    'event_name'  => env('GEMEMIS_EVENT_NAME', 'Geita International Trade Fair'),
    'short_name'  => env('GEMEMIS_SHORT_NAME', 'GEMEMIS'),
    'region'      => env('GEMEMIS_REGION', 'Geita'),
    'year'        => env('GEMEMIS_YEAR', date('Y')),

    // UI Labels (tumia kwenye blade: {{ config('gememis.labels.pavilion') }})
    'labels' => [
        'pavilion'      => 'Zone',
        'pavilions'     => 'Zones',
        'booth'         => 'Space',
        'booths'        => 'Spaces',
        'advice_note'   => 'Invoice',
        'advice_notes'  => 'Invoices',
        'exhibition_event'  => 'Event',
        'exhibition_events' => 'Events',
    ],

    'branding' => [
        'primary_color'   => '#1e40af',
        'secondary_color' => '#0891b2',
        'logo'            => 'gememis_logo.png',
    ],
];
