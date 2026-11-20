<?php

namespace App\Models\booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class booked_pavilions extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='bookingID';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'bookingID','companyID','userID','pavilionID','booking_status','exhibition_year','advice_note_id'
        ,'transferStatus','oldPavilionId','transferredBy','transferApprovedBy','transferReasons'
    ];
}
