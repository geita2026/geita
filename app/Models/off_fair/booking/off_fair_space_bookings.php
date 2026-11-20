<?php

namespace App\Models\off_fair\booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class off_fair_space_bookings extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='off_fair_booking_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'off_fair_booking_id','off_fair_space_id','space_booked_by','advice_note_id','durationCount',
        'off_fair_customer_id','start_date','end_date','customer_activity','advice_note','companyId'

    ];
}
