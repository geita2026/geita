<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['balance'];

    protected function casts(): array
    {
        return [
            'subtotal' => 'float', 'vat_rate' => 'float', 'vat_amount' => 'float', 'total' => 'float', 'amount_paid' => 'float',
            'issued_on' => 'date:Y-m-d', 'due_on' => 'date:Y-m-d',
        ];
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->latest();
    }

    public function exhibitor()
    {
        return $this->belongsTo(Exhibitor::class);
    }

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function getBalanceAttribute(): float
    {
        return max(0, round($this->total - $this->amount_paid, 2));
    }
}
