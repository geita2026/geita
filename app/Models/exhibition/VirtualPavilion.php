<?php

namespace App\Models\exhibition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class VirtualPavilion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='virtual_pavilion_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'virtual_pavilion_id','pavilion_descriptions','pavilion_image_name','exhibitor_id'
    ];
}
