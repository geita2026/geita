<?php

/**
 * Government Electronic Payment Gateway (GePG) — FR-059.
 *
 * Every value marked "from MoF" is issued by the Ministry of Finance when the
 * institution is registered as a GePG Service Provider; the message formats
 * and endpoint paths must be checked against the integration specification it
 * provides. docs/09-operations.md lists the onboarding steps.
 *
 * Modes:
 *   off        no bills are sent; invoices carry no control number
 *   simulator  a built-in stand-in for GePG answers in-process, signing its
 *              messages with its own keys — for development, tests and demos
 *   live       messages go to GEPG_BASE_URL
 */
return [
    'mode' => env('GEPG_MODE', 'simulator'),

    // Service Provider identity (from MoF).
    'sp_code' => env('GEPG_SP_CODE', 'SP00000'),
    'sub_sp_code' => env('GEPG_SUB_SP_CODE', '1001'),
    'sp_sys_id' => env('GEPG_SP_SYS_ID', 'TGEMEMIS001'),
    'sp_name' => env('GEPG_SP_NAME', 'Geita Mineral Exhibition'),

    // Endpoint (from MoF). Paths follow the published v4 posting API; confirm against the spec.
    'base_url' => env('GEPG_BASE_URL'),
    'paths' => [
        'bill' => env('GEPG_PATH_BILL', '/api/bill/sigqrequest'),
        'reuse' => env('GEPG_PATH_REUSE', '/api/bill/sigqrequest_reuse'),
        'cancel' => env('GEPG_PATH_CANCEL', '/api/bill/sigcancel_request'),
        'reconciliation' => env('GEPG_PATH_RECONCILIATION', '/api/reconciliations/sig_sp_qrequest'),
    ],
    // Routing headers GePG expects on each request (from MoF).
    'headers' => [
        'Gepg-Com' => env('GEPG_COM', 'default.sp.in'),
        'Gepg-Code' => env('GEPG_SP_CODE', 'SP00000'),
    ],
    'timeout' => (int) env('GEPG_TIMEOUT', 30),

    // Keys. The SP signs every message with its private key; GePG's messages are
    // verified with GePG's public certificate. Keep both outside the repository.
    'private_key_path' => env('GEPG_PRIVATE_KEY_PATH'),
    'private_key_passphrase' => env('GEPG_PRIVATE_KEY_PASSPHRASE'),
    'gepg_certificate_path' => env('GEPG_CERTIFICATE_PATH'),
    'signature_algorithm' => env('GEPG_SIGNATURE_ALGORITHM', 'sha1'), // sha1 | sha256 — per the spec

    // Billing rules.
    'default_gfs_code' => env('GEPG_DEFAULT_GFS_CODE'), // used when a space category has none
    'payment_option' => (int) env('GEPG_PAYMENT_OPTION', 2), // 1 full, 2 partial, 3 exact — confirm with MoF
    'collection_account' => env('GEPG_COLLECTION_ACCOUNT'),

    // Status codes (TrxStsCode). 7101 is GePG's "successful"; the two failure
    // codes GeMEMIS answers with are placeholders until the spec's list is known.
    'codes' => [
        'success' => '7101',
        'failure' => env('GEPG_CODE_FAILURE', '7201'),
        'invalid_signature' => env('GEPG_CODE_INVALID_SIGNATURE', '7303'),
    ],
];
