<?php

namespace App\Models\Collections;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Bill extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='bill_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'bill_id','customer_id','advice_note_id','control_number', 'total_amount','payment_status','approved_by','balance','currency','sp_invoice_no'
    ];
}
