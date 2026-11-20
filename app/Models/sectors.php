<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class sectors extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='sectorID';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'sectorID','userID','sectorName','sectorDescription','sectorImage','sectorStatus','updated_at',
    ];

}
