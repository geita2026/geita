<?php

namespace App\Models\exhibition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class VirtualFiles extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='image_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'image_id','reference_id','destination','image_name','image_description'
    ];
}
