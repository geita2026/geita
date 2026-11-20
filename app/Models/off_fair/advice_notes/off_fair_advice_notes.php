<?php

namespace App\Models\off_fair\advice_notes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class off_fair_advice_notes extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='off_fair_advice_note_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'off_fair_advice_note_id','advice_note_generated_by','advice_note_owned_by',
        'off_fair_advice_note_deadline','off_fair_advice_note_status','advice_note_id','vat','advice_note_attended_by',
        'accountant_id','advice_note_description','approved_at','rejection_comment',
        'control_number','total_amount','amount_paid','balance','currency','sp_invoice_no','total_vat_amount'
    ];
}
