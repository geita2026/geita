<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Every message sent to or received from GePG, verbatim. Never edited. */
class GepgMessage extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['signature_valid' => 'boolean', 'created_at' => 'datetime'];
    }
}
