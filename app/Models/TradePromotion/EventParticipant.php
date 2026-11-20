<?php

namespace App\Models\TradePromotion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EventParticipant extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='participantId';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'participantId','participantName','participantEmail','participantPhone','participantWebsite',
        'participantProfile','participantCompany','participantTitle','eventNumber',
        'companyProfile','participantPhoto','companyLogo','participantStatus','participantRank','sector',
        'country','region'
    ];
}
