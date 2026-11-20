<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PointOfSale extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='locationId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'locationId','locationName','locationDescription','locationStatus'
    ];
}
