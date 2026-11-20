<?php

namespace App\Models\off_fair;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class off_fair_spaces extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='space_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'space_id','space_registered_by','space_cost','space_descriptions','space_category_cost','space_id','space_status','space_category'
        ,'space_name','space_size','spaceImage','spaceCostWeekend'
    ];
}
