<?php

namespace App\Models\ePermit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ePermitApplication extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='applicationId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'applicationId','companyId','appliedBy','applicationStatus',
        'applicationComment','typeOfPromotionEvent','reviewComment',
        'lastComment','typeOfApplication','permitNo','authorizedBy','DateOfIssue',
        'allowedToEdit','mngViewStatus'
    ];
}
