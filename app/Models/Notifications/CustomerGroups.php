<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CustomerGroups extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='group_id';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'group_id','group_name','group_descriptions'
    ];
}
