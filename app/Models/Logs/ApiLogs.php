<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ApiLogs extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='id';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'response','id','status','reminder','requestData'
    ];

}
