<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class VipTicketBenefit extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='benefit_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'benefit_id','benefit_name','benefit_descriptions','package_id'
    ];
}

