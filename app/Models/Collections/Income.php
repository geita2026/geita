<?php

namespace App\Models\Collections;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Income extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='gfs_code';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'gfs_code','income_name', 'description','is_space_fee','is_joint_payment'
    ];
}
