<?php

namespace App\Models\advice_note;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class advice_notes extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='advice_note_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'advice_note_id','company_id','user_id','exhibition_year','advice_note_status','accountant_id',
        'advice_note_description','advice_note_deadline','event','vat','invoice_type','approved_at','rejection_comment',
        'control_number','total_amount','amount_paid','balance','currency','sp_invoice_no','total_vat_amount','new_deadline','endorsement_attachment',
        'discount','discount_reasons','discount_attachment','initially_assessed_by','finally_assessed_by','rejection_reasons'
        ,'change_currency','exchange_rate','notification_date','payment_mode','discountAmount'
    ];
}
