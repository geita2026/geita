<?php

namespace App\Models\off_fair;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class off_fair_utilities extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='utility_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
     'utility_id','utility_name','utility_cost','utility_created_by','utility_description','utility_priolity'
    ];
}
