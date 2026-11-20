<?php

namespace App\Models\off_fair\booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class space_size_category_costs extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='space_size_category_cost_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'space_size_category_cost_id','area_size_name','sales_status','size_range_cost','user_id'
    ];
}
