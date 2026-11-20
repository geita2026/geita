<?php

namespace App\Models\guidelines;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class policies extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='policy_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'policy_id','policy_content','created_by','policy_title'
    ];

}
