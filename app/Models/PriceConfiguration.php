<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PriceConfiguration extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='trackingId';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'trackingId','referenceId','configurationName','configDescription','configCost','eventId','currency'
    ];
}
