<?php

namespace App\Models\sponsorships;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class customer_packages extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='customer_package_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
    'customer_id','customer_package_id','package_name','package_description','package_amount','exhibition_year','created_by','type_of_currency'
    ];
}
