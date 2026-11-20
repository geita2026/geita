<?php

namespace App\Models\Permits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Permit extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='permit_id';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'permit_id','company_id','event_id','permits',
        'permit_type','created_by','permitStatus',
       'approvedBy','permitNumber','itemList'
    ];

}
