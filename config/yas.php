<?php

/*
|--------------------------------------------------------------------------
| YAS / Tigo Pesa "Debit Mandate" (USSD PUSH) integration
|--------------------------------------------------------------------------
|
| Spec: "Debit Mandate - TNZ, API Specification Document (Billers) v1.x"
| (Torry Harris / THBS). We act as the BILLER:
|   1. Token            POST  {token_url}
|   2. Biller Payment   POST  {api_base_url}/API/BillerPayment/BillerPay
|   3. Biller Callback  YAS -> our callback URL
|   4. Refund           POST  {api_base_url}/API/Reverse/ReverseTransacation
|   5. Heartbeat        POST  {api_base_url}/API/Heartbeat/Heartbeat   (no token)
|   6. LiveBalance      POST  {api_base_url}/API/BillerLiveBalance/LiveBalance
|
| Endpoints are private and only reachable over the site-to-site VPN (via eGA).
|   TEST host: 41.222.176.233   (ports 8443 / 443)
|   LIVE host: fintechgslb.tigo.co.tz / 41.222.176.226 etc. (port 8443)
|
| NOTE: the spec PDF shows example ports :9101 (token/heartbeat/refund) and
| :8095 (BillerPay). The VPN access list only opens 8443/443. The real
| host:port/paths are environment-specific, so EVERYTHING here is overridable
| via .env. Adjust YAS_TOKEN_URL / YAS_API_BASE_URL once confirmed with YAS.
|
*/

return [

    // Full URL of the token endpoint. YAS provisioned CUSTOM Access Gateway
    // routes for TanTrade (per Mussa Muyulla, MFS, 2026-06-10) - NOT the generic
    // /token path from the spec PDF.
    //   TEST token : https://accessgwtest.tigo.co.tz:8443/TanTrade2DM-GetToken
    //   TEST push  : https://accessgwtest.tigo.co.tz:8443/TanTrade2DM-PushBillPay
    // Whitelisted IPs: TEST 41.222.176.233 | LIVE 41.222.182.236 / .200 / 41.222.176.226 (all :8443)
    'token_url' => env('YAS_TOKEN_URL', 'https://accessgwtest.tigo.co.tz:8443/TanTrade2DM-GetToken'),

    // Base URL that the paths below are appended to.
    'api_base_url' => env('YAS_API_BASE_URL', 'https://accessgwtest.tigo.co.tz:8443'),

    // API paths (relative to api_base_url). This gateway exposes GetToken +
    // PushBillPay only; there is NO Heartbeat/Refund/Balance route here.
    'paths' => [
        'biller_pay'    => env('YAS_PATH_BILLER_PAY', '/TanTrade2DM-PushBillPay'),
        // OUR callback path (the route YAS posts back to). Register the full URL
        // https://<domain>/api/v1/yas/callback with YAS.
        'callback'      => env('YAS_PATH_CALLBACK', '/api/v1/yas/callback'),
        'refund'        => env('YAS_PATH_REFUND', null),       // not provisioned on this gateway
        'heartbeat'     => env('YAS_PATH_HEARTBEAT', null),    // not provisioned on this gateway
        'live_balance'  => env('YAS_PATH_LIVE_BALANCE', null), // not provisioned on this gateway
    ],

    // ── TQS: Query Transaction Details (XML over HTTP) ──────────────────
    // Used to POLL YAS for a transaction's real status when the success
    // callback doesn't arrive. TXNSTATUS: Posted = paid, Declined = failed,
    // Submitted = pending. We query by EXTERNALREFID = our push ReferenceID.
    //   LIVE: https://sal-accessgwr1.tigo.co.tz:8443/TANTRADE2DMTQS
    'tqs_url'    => env('YAS_TQS_URL', 'https://sal-accessgwr1.tigo.co.tz:8443/TANTRADE2DMTQS'),
    'tqs_pin'    => env('YAS_TQS_PIN'),                 // the TQS profile PIN (from YAS)
    'tqs_msisdn' => env('YAS_TQS_MSISDN'),              // optional override; defaults to the payer MSISDN

    // Merchant / biller credentials (provided by YAS).
    'username'      => env('YAS_USERNAME'),
    'password'      => env('YAS_PASSWORD'),

    // Biller code is prefixed to every ReferenceID we send.
    'biller_code'   => env('YAS_BILLER_CODE'),

    // Biller wallet MSISDN (255 + 9 digits) and its MFS PIN (PIN used for refunds).
    'biller_msisdn' => env('YAS_BILLER_MSISDN'),
    'biller_pin'    => env('YAS_BILLER_PIN'),

    // Internal YAS fintech certs are often self-signed; allow disabling TLS
    // verification on the TEST tunnel. Keep TRUE in production if possible.
    'verify_ssl'    => env('YAS_VERIFY_SSL', false),

    // HTTP timeout (seconds) for outbound calls.
    'timeout'       => env('YAS_TIMEOUT', 30),
];
