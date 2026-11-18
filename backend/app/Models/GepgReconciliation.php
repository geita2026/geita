<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GepgReconciliation extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date:Y-m-d', 'gepg_total' => 'float',
            'recovered' => 'array', 'missing_at_gepg' => 'array', 'amount_mismatches' => 'array',
            'received_at' => 'datetime',
        ];
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
