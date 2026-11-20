<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class VipTicketPackage extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='vip_package_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'vip_package_id','package_name','package_color','package_discriptions',
        'package_cost','currency','gfs_code','packageStatus','pStatus','package_description_sw'
    ];
}
