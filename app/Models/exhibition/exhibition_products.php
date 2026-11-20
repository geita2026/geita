<?php

namespace App\Models\exhibition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class exhibition_products extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='exhibition_product_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'exhibition_product_id','user_id','exhibitor_id','product_list','event'
    ];

}
