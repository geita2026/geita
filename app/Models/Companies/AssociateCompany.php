<?php

namespace App\Models\Companies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AssociateCompany extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='ass_company_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'ass_company_id','company_id','company_name','company_email','company_phone'
    ];

}
