<?php

namespace App\Models\space;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class space_categories extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='category_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'category_id','user_id','category_name','cost_one_msq','type_of_currency','background_color','category_icon','category_event','category_descriptions'
    ];
}
