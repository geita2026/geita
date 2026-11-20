<?php

namespace App\Models\Advertisements;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ClipsAdvertisement extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='advertisement_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'advertisement_id','advertisement_title','advertisement_descriptions','clip','advertising_days','status'
    ];
}
