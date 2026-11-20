<?php

namespace App\Models\off_fair\spaces;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class off_fair_space_categories extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='space_category_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'space_category_id','category_created_by','category_name',
        'space_category_cost','category_descriptions','categoryPoster',
        'populationLimit'
    ];
}
