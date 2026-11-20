<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TnbMark extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='markId';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'markId','tnbId','criteria','judgeNumber','marks'
    ];

}
