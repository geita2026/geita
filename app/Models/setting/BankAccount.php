<?php

namespace App\Models\setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BankAccount extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'account_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'account_id',
        'account_number',
        'account_name',
        'swift_code',
        'account_descriptions',
        'account_currency',
        'bank_name',
        'bankAccountStatus'
    ];
}
