<?php

namespace App\Models\TradePromotion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PromotionEvent extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='eventId';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'eventId','eventTitle','startFrom','endTo','eventParticipants',
        'eventDescription','eventPoster','eventStatus','registrationDeadline',
        'eventNumber'
    ];
}
