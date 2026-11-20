<?php

namespace App\Models\Printing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FacialName extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='company_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'company_id','company_name','group_id'
    ];
}
