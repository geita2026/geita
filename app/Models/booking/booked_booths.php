<?php

namespace App\Models\booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class booked_booths extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='bookingID';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'bookingID','companyID','bookingStatus','userID','boothID','exhibition_year','advice_note_id','booking_status'
    ];
}
