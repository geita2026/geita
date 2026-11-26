<?php
return[
    'DATA_LIMIT'=>10,
    'SEARCH_LIMIT'=>5,
    'RESTRICTED_ACCESS'=>'RESTRICTED ACCESS',
    '100'=>'Server Error',
    '101'=>'Validation Error',
    '102'=>'Authentication Error',
    '105'=>'OK',
    'ALLOWED_NETWORKS'=>[
        env('ALLOWED_NETWORK_1'),
        env('ALLOWED_NETWORK_2',),
    ],
    'INVOICE_DUE_DAYS'=>env('INVOICE_DUE_DAYS',7),
    // Module visibility & branding (safe to cache)
    'MODULE_ONE'=>env('MODULE_ONE', 'GeMEMIS'),
    'MODULE_ONE_DESCRIPTION'=>env('MODULE_ONE_DESCRIPTION', ''),
    'CALL_CENTER'=>env('CALL_CENTER', ''),
    'APP_VERSION'=>env('APP_VERSION', ''),
    'COMPANY_EXCEPTION'=>env('COMPANY_EXCEPTION', null),
    // Integration feature flags
    'APP_STATUS'=>env('APP_STATUS', 0),
    'APP_VASSION'=>env('APP_VASSION', 0),
    'BOOKING_STATUS'=>env('BOOKING_STATUS', 1),
    'BACKUP'=>env('BACKUP', 0),
    // External API credentials & URLs
    'MARKET_INFO_TOKEN'=>env('MARKET_INFO_TOKEN', ''),
    'MARKET_INFO_BASE_URL'=>env('MARKET_INFO_BASE_URL', ''),
    'BACKUP_BASE_URL'=>env('BACKUP_BASE_URL', ''),
    'LIVE_EVENT_ID'=>env('LIVE_EVENT_ID', null),
    'ADMIN_COMPANY_ID'=>env('ADMIN_COMPANY_ID', null),
    // ESB / NIDA integration
    'clientId'=>env('clientId', ''),
    'clientSecret'=>env('clientSecret', ''),
    'clientPrivateKey'=>env('clientPrivateKey', ''),
    'esbPublicKey'=>env('esbPublicKey', ''),
    'esbTokenUrl'=>env('esbTokenUrl', ''),
    'esbEngineUrl'=>env('esbEngineUrl', ''),
    'nidaUserId'=>env('nidaUserId', ''),
    'signatureAlogirthm'=>env('signatureAlogirthm', ''),
    'format'=>env('format', ''),
];
#
