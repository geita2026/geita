<?php

namespace App\Models\space;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class additional_services extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='serviceID';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'serviceID','userID','serviceName',
        'serviceCost','serviceDescription',
        'type_of_currency','service_type','relateToSpaceSize',
        'service_priolity','service_event',
        'gfs_code','is_printable','self_booking'
    ];
}
