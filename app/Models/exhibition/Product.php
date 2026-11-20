<?php

namespace App\Models\exhibition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Product extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='product_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'product_id','product_name','descriptions'
    ];
}
