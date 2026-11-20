<?php

namespace App\Models\off_fair;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class off_fair_activities extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='activity_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'activity_id','activity_created_by','activity_cost','activity_descriptions','activity_name'
    ];
}
