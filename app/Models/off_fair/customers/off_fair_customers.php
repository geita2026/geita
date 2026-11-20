<?php

namespace App\Models\off_fair\customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class off_fair_customers extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='customer_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'customer_id', 'customer_name', 'customer_phone', 'customer_email', 'customer_home_location', 'customer_registered_by','sp_customer_no'
    ];

}
