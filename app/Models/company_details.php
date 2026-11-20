<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class company_details extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='companyDetailID';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'companyDetailID','companyID','userID','registrationNo','tinNo',
        'companyTel','companyAddress','website','originality','personalName','personalPhone',
        'personalEmail','personalTitle'
    ];
}
