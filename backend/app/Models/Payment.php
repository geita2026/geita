<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['evidence_path'];

    protected $appends = ['has_evidence'];

    protected function casts(): array
    {
        return ['amount' => 'float', 'paid_on' => 'date:Y-m-d', 'verified_at' => 'datetime'];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function gepgBill()
    {
        return $this->belongsTo(GepgBill::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getHasEvidenceAttribute(): bool
    {
        return (bool) $this->evidence_path;
    }
}
