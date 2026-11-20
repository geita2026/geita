<?php

namespace App\Models\off_fair\advice_notes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class off_fair_booking_particulars extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='particular_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'particular_id','particular_name','particular_amount','type_of_currency','particular_quantity','vat','advice_note_id',
        'particulars_approved_by','advice_note_id','particular_duration','gfs_code'
    ];
}
