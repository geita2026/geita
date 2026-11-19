<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GepgBill extends Model
{
    /** Statuses in which the bill's control number can still be paid. */
    public const OPEN = ['requesting', 'issued'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amount' => 'float', 'paid_amount' => 'float',
            'expires_at' => 'datetime', 'requested_at' => 'datetime', 'issued_at' => 'datetime', 'ended_at' => 'datetime',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function outstanding(): float
    {
        return max(0, round($this->amount - $this->paid_amount, 2));
    }
}
