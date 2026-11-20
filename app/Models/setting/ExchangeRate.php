<?php

namespace App\Models\setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ExchangeRate extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='currencyId';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'currencyId','currencyCode','BUYING','SELLING','MEAN'
    ];
}
