<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PackageBenefitsMapping extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='vip_package_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'vip_package_id','benefit_id','mapping_id'
    ];
}
