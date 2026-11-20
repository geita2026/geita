<?php

namespace App\Models\settings;

use Illuminate\Database\Eloquent\Model;

/**
 * Per-event signing key used to HMAC-sign POS tickets for offline
 * validation. See migration create_event_signing_keys_table.
 */
class EventSigningKey extends Model
{
    protected $table = 'event_signing_keys';

    protected $fillable = ['event_id', 'signing_key'];
}
