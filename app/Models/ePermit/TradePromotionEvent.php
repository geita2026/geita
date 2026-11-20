<?php

namespace App\Models\ePermit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TradePromotionEvent extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='tradeEventId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'tradeEventId','tradeEventType','eventTitle','eventTheme',
        'eventObjectives','productExhibited','startFrom','endDate',
        'localParticipants','foreignParticipants','MOFAttachment','entryFee',
        'estimateExpenditure','eventWebsite','venueAddress','venueCoordinates',
        'typeOfBuilding','venueOwner','venueOwnerAddress','venueSize','eventApplicationId'
    ];
}
