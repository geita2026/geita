<?php

namespace App\Models\space;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Street extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='streetId';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'streetId','streetName','napaReference','streetDescription','latitude',
        'longtude'
    ];
}
