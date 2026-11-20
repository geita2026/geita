<?php

namespace App\Models\Badges;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EmergenceBadge extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='companyId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'companyId','companyName','companyEmail',
        'companyPhone','eventCode','badgeTitle'
    ];

}
