<?php

namespace App\Models\exhibition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class VirtualService extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='virtual_service_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'virtual_service_id','participation_id','service_descriptions','service_image_name','service_name'
    ];
}
