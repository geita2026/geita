<?php

namespace App\Models\ePermit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PromotionEventType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='eventTypeId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'eventTypeId','promotionEventTypeName','promotionEventTypeDescription','eventTypePoster','typeStatus'
        ,'eventTypeAmountUsd','eventTypeAmountTzs'
    ];
}
