<?php

namespace App\Models\space;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class pavilions extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='pavilionID';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'pavilionID','pavilionName','userID','TotalBooths','floor_plan','spaceStreet',
        'pavilionCategory','pavilionRate','pavilionDescription','bookingStatus','pavilion_size','space_status',
        'allowedForBooking','whoCanBook','numberOfPartitions','latitude','longitude','location'
    ];
}
