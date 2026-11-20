<?php

namespace App\Models\ePermit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ApplicationReviewer extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='mappingId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'mappingId','applicationReference','asignedUser'
    ];
}
