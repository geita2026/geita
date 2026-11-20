<?php

namespace App\Models\setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ApiRequest extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='client_id';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'client_id','access_token','request_name','client_email'
    ];
}
