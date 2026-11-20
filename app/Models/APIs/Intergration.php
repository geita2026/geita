<?php

namespace App\Models\APIs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Intergration extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='intergration_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'intergration_id','billing_service_code','billing_access_token','intergration_created_by','billing_base_url'
    ];
}
