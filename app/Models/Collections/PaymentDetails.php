<?php

namespace App\Models\Collections;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentDetails extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='payment_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'payment_id','control_number','receipt','bank_receipt','gateway_receipt','amount_paid','currency','trans_date','bank_account',
       'is_transfered','old_invoice','transfered_by','transfer_reason','approved_by','referenceId'
    ];
}
