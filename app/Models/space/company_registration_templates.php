<?php

namespace App\Models\space;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class company_registration_templates extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='company_name';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'company_name','company_email','company_phone','company_category','company_location'
    ];

}
