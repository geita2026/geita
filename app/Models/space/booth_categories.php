<?php

namespace App\Models\space;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class booth_categories extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='booth_category_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[

        'booth_category_id','user_id','category_name','category_cost','type_of_currency','categoryCode'
    ];
}
