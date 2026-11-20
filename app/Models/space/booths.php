<?php

namespace App\Models\space;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class booths extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='boothID';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'boothID','pavilionID','boothNo','boothSize',
        'boothCost','boothDescription','bookingStatus','booth_category','allowedForBooking',
        'latitude','longitude','location'
    ];
}
