<?php

namespace App\Models\booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class booked_sponsorship_packages extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='booking_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'booking_id','customer_id','package_id','approved_by','exhibition_year','customer_amount','advice_note_id'
    ];
}
