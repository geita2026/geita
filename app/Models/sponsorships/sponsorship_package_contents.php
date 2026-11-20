<?php

namespace App\Models\sponsorships;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class sponsorship_package_contents extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='package_content_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
    'package_content_id','package_id','content_description',
    'contentCost','created_by','benefitId','eventId'
    ];
}
