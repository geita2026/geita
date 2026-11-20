<?php

namespace App\Models\advice_note;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class service_charges extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='service_charge_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'service_charge_id','company_id','user_id','exhibition_year','service_quantity','service_id','advice_note_id','booking_status'
    ];
}
