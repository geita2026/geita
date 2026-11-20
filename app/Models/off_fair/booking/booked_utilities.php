<?php

namespace App\Models\off_fair\booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class booked_utilities extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='utility_booking_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'utility_booking_id','booked_utility_id','booked_by','user_id','booking_date','booking_updated_at','booking_status','advice_note_id','companyId'
    ];
}
