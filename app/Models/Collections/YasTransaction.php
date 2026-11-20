<?php

namespace App\Models\Collections;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * One row per YAS / Tigo Pesa Debit Mandate (USSD PUSH) transaction.
 * See migration 2026_06_18_120000_create_yas_transactions_table.
 */
class YasTransaction extends Model
{
    use HasFactory;

    protected $table = 'yas_transactions';

    protected $fillable = [
        'reference_id', 'booking_id', 'customer_msisdn', 'biller_msisdn',
        'amount', 'currency', 'remarks', 'status', 'mfs_transaction_id',
        'request_payload', 'response_payload', 'callback_payload',
        'response_code', 'error', 'paid_at',
    ];

    protected $casts = [
        'request_payload'  => 'array',
        'response_payload' => 'array',
        'callback_payload' => 'array',
        'amount'           => 'decimal:2',
        'paid_at'          => 'datetime',
    ];

    // Lifecycle states.
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PUSHED  = 'PUSHED';
    public const STATUS_SUCCESS = 'SUCCESS';
    public const STATUS_FAILED  = 'FAILED';
}
